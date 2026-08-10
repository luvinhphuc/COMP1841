<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\Post;
use PDO;
use Throwable;

/**
 * Encapsulates discussion queries, filtering, activity tracking, and slug generation.
 */
class PostRepository
{
    private PDO $db;
    private string $table = 'posts';
    private ?array $userColumns = null;
    private array $tableExistsCache = [];

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->connect();
    }

    // Create
    public function create(array $data)
    {
        $title = trim((string) ($data['title'] ?? ''));
        $content = trim((string) ($data['content'] ?? ''));
        $userId = (int) ($data['user_id'] ?? 0);
        $moduleId = (int) ($data['module_id'] ?? 0);
        $slug = trim((string) ($data['slug'] ?? ''));

        if ($title === '' || $userId <= 0 || $moduleId <= 0) {
            return 0;
        }

        $stmt = $this->db->prepare("INSERT INTO {$this->table} (title, slug, content, user_id, module_id)
             VALUES (:title, :slug, :content, :user_id, :module_id)");

        $stmt->execute(['title' => $title, 'slug' => $this->uniqueSlug($slug !== '' ? $slug : $title), 'content' => $content, 'user_id' => $userId, 'module_id' => $moduleId,]);

        return (int) $this->db->lastInsertId();
    }

    // Read
    public function findAll(int $limit = 20, int $offset = 0)
    {
        $stmt = $this->db->prepare($this->baseSelectSql() . '
            ORDER BY p.created_at DESC
            LIMIT :limit OFFSET :offset
        ');

        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findLatest(int $limit = 10)
    {
        $stmt = $this->db->prepare($this->baseSelectSql() . '
            ORDER BY p.created_at DESC
            LIMIT :limit
        ');

        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByUserId(int $userId, int $limit = 10, int $offset = 0)
    {
        $stmt = $this->db->prepare($this->baseSelectSql() . '
            AND p.user_id = :user_id
            ORDER BY p.created_at DESC
            LIMIT :limit OFFSET :offset
        ');

        $stmt->bindValue('user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countByUserId(int $userId)
    {
        if ($userId <= 0) {
            return 0;
        }

        $stmt = $this->db->prepare(
            "SELECT COUNT(*)
             FROM {$this->table}
             WHERE user_id = :user_id
               AND deleted_at IS NULL"
        );
        $stmt->execute(['user_id' => $userId]);

        return (int) $stmt->fetchColumn();
    }

    public function findByModuleId(int $moduleId, int $limit = 20, int $offset = 0)
    {
        $stmt = $this->db->prepare($this->baseSelectSql() . '
            AND p.module_id = :module_id
            ORDER BY p.created_at DESC
            LIMIT :limit OFFSET :offset
        ');

        $stmt->bindValue('module_id', $moduleId, PDO::PARAM_INT);
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countDiscussions(array $filters)
    {
        $params = [];
        $sql = "
            SELECT COUNT(*)
            FROM {$this->table} p
            INNER JOIN modules m ON m.id = p.module_id
            WHERE p.deleted_at IS NULL
        " . $this->discussionFilterSql($filters, $params);

        $stmt = $this->db->prepare($sql);
        $this->bindDiscussionParams($stmt, $params);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    public function countByStatus()
    {
        $stmt = $this->db->query(
            "SELECT
                COUNT(*) AS total,
                SUM(status = 'open') AS open_count,
                SUM(status = 'solved') AS solved_count
             FROM {$this->table}
             WHERE deleted_at IS NULL"
        );
        $counts = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

        return [
            'total' => (int) ($counts['total'] ?? 0),
            'open' => (int) ($counts['open_count'] ?? 0),
            'solved' => (int) ($counts['solved_count'] ?? 0),
        ];
    }

    public function findPopularDiscussions(int $limit = 5)
    {
        return $this->findDiscussions(['sort' => 'popular'], $limit, 0);
    }

    public function findDiscussions(array $filters, int $limit = 20, int $offset = 0)
    {
        $params = [];
        $sql = $this->baseSelectSql() . $this->discussionFilterSql($filters, $params) . $this->discussionOrderSql($filters) . '
            LIMIT :limit OFFSET :offset
        ';

        $stmt = $this->db->prepare($sql);
        $this->bindDiscussionParams($stmt, $params);
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(int $id)
    {
        if ($id <= 0) {
            return null;
        }

        $stmt = $this->db->prepare(
            "SELECT id, title, slug, content, status, view_count, user_id, module_id,
                created_at, updated_at, deleted_at
             FROM {$this->table}
             WHERE id = :id AND deleted_at IS NULL
             LIMIT 1"
        );

        $stmt->execute(['id' => $id]);
        $postRecord = $stmt->fetch(PDO::FETCH_ASSOC);

        return $postRecord ? new Post($postRecord) : null;
    }

    public function findBySlug(string $slug)
    {
        $slug = trim($slug);

        if ($slug === '') {
            return null;
        }

        $stmt = $this->db->prepare(
            "SELECT id, title, slug, content, status, view_count, user_id, module_id,
                created_at, updated_at, deleted_at
             FROM {$this->table}
             WHERE slug = :slug AND deleted_at IS NULL
             LIMIT 1"
        );

        $stmt->execute(['slug' => $slug]);
        $postRecord = $stmt->fetch(PDO::FETCH_ASSOC);

        return $postRecord ? new Post($postRecord) : null;
    }

    public function findDetailsById(int $id)
    {
        if ($id <= 0) {
            return null;
        }

        $stmt = $this->db->prepare($this->baseSelectSql() . '
            AND p.id = :id
            LIMIT 1
        ');

        $stmt->execute(['id' => $id]);
        $postRecord = $stmt->fetch(PDO::FETCH_ASSOC);

        return $postRecord ?: null;
    }

    // Update
    public function update(int $id, array $data)
    {
        $fields = [];
        $params = ['id' => $id];

        if (array_key_exists('title', $data)) {
            $title = trim((string) $data['title']);

            if ($title === '') {
                return false;
            }

            $fields[] = 'title = :title';
            $params['title'] = $title;

            if (!array_key_exists('slug', $data)) {
                $fields[] = 'slug = :slug';
                $params['slug'] = $this->uniqueSlug($title, $id);
            }
        }

        if (array_key_exists('slug', $data)) {
            $fields[] = 'slug = :slug';
            $params['slug'] = $this->uniqueSlug((string) $data['slug'], $id);
        }

        if (array_key_exists('content', $data)) {
            $content = trim((string) $data['content']);

            $fields[] = 'content = :content';
            $params['content'] = $content;
        }

        if (array_key_exists('module_id', $data)) {
            $moduleId = (int) $data['module_id'];

            if ($moduleId <= 0) {
                return false;
            }

            $fields[] = 'module_id = :module_id';
            $params['module_id'] = $moduleId;
        }

        if (empty($fields)) {
            return false;
        }

        $fields[] = 'updated_at = NOW()';
        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . ' WHERE id = :id AND deleted_at IS NULL';
        $stmt = $this->db->prepare($sql);

        return $stmt->execute($params);
    }

    public function recordView(int $postId)
    {
        if ($postId <= 0) {
            return false;
        }

        $stmt = $this->db->prepare("UPDATE {$this->table}
                 SET view_count = view_count + 1
                 WHERE id = :post_id AND deleted_at IS NULL");
        $stmt->execute(['post_id' => $postId]);

        return $stmt->rowCount() > 0;
    }

    public function updateActivityTimestamp(int $postId)
    {
        if ($postId <= 0) {
            return false;
        }

        $stmt = $this->db->prepare("SELECT COALESCE(
                (
                    SELECT MAX(COALESCE(r.updated_at, r.created_at))
                    FROM replies r
                    WHERE r.post_id = :reply_post_id AND r.deleted_at IS NULL
                ),
                p.created_at
             ) AS last_activity
             FROM {$this->table} p
             WHERE p.id = :post_id AND p.deleted_at IS NULL
             LIMIT 1");

        $stmt->execute(['reply_post_id' => $postId, 'post_id' => $postId,]);

        $lastActivity = $stmt->fetchColumn();

        if (!$lastActivity) {
            return false;
        }

        $update = $this->db->prepare("UPDATE {$this->table}
             SET updated_at = :last_activity
             WHERE id = :post_id AND deleted_at IS NULL");

        return $update->execute(['last_activity' => $lastActivity, 'post_id' => $postId,]);
    }

    // Delete
    public function delete(int $id)
    {
        if ($id <= 0) {
            return false;
        }

        try {
            // Lock the discussion while cascading soft deletion across its related records.
            $this->db->beginTransaction();

            $postStatement = $this->db->prepare(
                "SELECT id
                 FROM {$this->table}
                 WHERE id = :id AND deleted_at IS NULL
                 FOR UPDATE"
            );
            $postStatement->execute(['id' => $id]);

            if ($postStatement->fetchColumn() === false) {
                $this->db->rollBack();
                return false;
            }

            $this->db->prepare('DELETE m FROM media m
                INNER JOIN replies r ON r.id = m.reply_id
                WHERE r.post_id = :post_id')->execute(['post_id' => $id]);
            $this->db->prepare('DELETE FROM media WHERE post_id = :post_id')->execute(['post_id' => $id]);

            $this->db->prepare('UPDATE replies
                 SET deleted_at = NOW(), is_accepted = 0, updated_at = NOW()
                 WHERE post_id = :post_id AND deleted_at IS NULL')->execute(['post_id' => $id]);

            $stmt = $this->db->prepare("UPDATE {$this->table}
                 SET deleted_at = NOW(), status = \"open\", updated_at = NOW()
                 WHERE id = :id AND deleted_at IS NULL");

            $stmt->execute(['id' => $id]);
            $deleted = $stmt->rowCount() > 0;

            $this->db->commit();

            return $deleted;
        } catch (Throwable $exception) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $exception;
        }
    }

    private function baseSelectSql(string $extraSelect = '')
    {
        $extraSelect = $extraSelect !== '' ? ', ' . $extraSelect : '';
        $userNameSelect = $this->userNameSelectSql();
        $mediaPathSelect = $this->mediaPathSelectSql();
        $mediaTypeSelect = $this->mediaTypeSelectSql();

        return '
            SELECT
                p.id,
                p.title,
                p.slug,
                p.content,
                p.status,
                p.user_id,
                p.module_id,
                p.created_at,
                p.updated_at,
                m.module_code,
                m.module_name,
                u.username,
                ' . $userNameSelect . ' AS full_name,
                u.avatar,
                (
                    SELECT COUNT(*)
                    FROM replies r
                    WHERE r.post_id = p.id AND r.deleted_at IS NULL
                ) AS reply_count,
                p.view_count,
                ' . $mediaPathSelect . ' AS media_path,
                ' . $mediaTypeSelect . ' AS media_type
                ' . $extraSelect . "
            FROM {$this->table} p
            INNER JOIN modules m ON m.id = p.module_id
            INNER JOIN users u ON u.id = p.user_id
            WHERE p.deleted_at IS NULL
        ";
    }

    private function userNameSelectSql()
    {
        // Support both the current schema and earlier coursework database snapshots.
        if ($this->userHasColumn('full_name')) {
            return 'u.full_name';
        }

        if ($this->userHasColumn('first_name') && $this->userHasColumn('last_name')) {
            return "TRIM(CONCAT_WS(' ', u.first_name, u.last_name))";
        }

        return 'u.username';
    }

    private function userHasColumn(string $column)
    {
        if ($this->userColumns === null) {
            $stmt = $this->db->prepare('SHOW COLUMNS FROM users');
            $stmt->execute();
            $this->userColumns = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'Field');
        }

        return in_array($column, $this->userColumns, true);
    }

    private function tableExists(string $table)
    {
        // Cache schema probes because the base select may request several optional projections.
        if (!array_key_exists($table, $this->tableExistsCache)) {
            $stmt = $this->db->prepare('SELECT COUNT(*)
                 FROM information_schema.tables
                 WHERE table_schema = DATABASE()
                   AND table_name = :table');
            $stmt->execute(['table' => $table]);
            $this->tableExistsCache[$table] = $stmt->fetchColumn() > 0;
        }

        return $this->tableExistsCache[$table];
    }

    private function mediaPathSelectSql()
    {
        if (!$this->tableExists('media')) {
            return 'NULL';
        }

        return '(
            SELECT path
            FROM media media_item
            WHERE media_item.post_id = p.id
            ORDER BY media_item.created_at ASC
            LIMIT 1
        )';
    }

    private function mediaTypeSelectSql()
    {
        if (!$this->tableExists('media')) {
            return 'NULL';
        }

        return '(
            SELECT type
            FROM media media_item
            WHERE media_item.post_id = p.id
            ORDER BY media_item.created_at ASC
            LIMIT 1
        )';
    }

    private function discussionFilterSql(array $filters, array &$params)
    {
        $sql = '';
        $query = trim((string) ($filters['q'] ?? ''));
        $status = trim((string) ($filters['status'] ?? ''));
        $module = trim((string) ($filters['module'] ?? ''));

        if ($query !== '') {
            $sql .= '
                AND (
                    p.title LIKE :query_title
                    OR p.content LIKE :query_content
                    OR m.module_code LIKE :query_module_code
                    OR m.module_name LIKE :query_module_name
                )
            ';
            $queryValue = '%' . $query . '%';
            $params['query_title'] = $queryValue;
            $params['query_content'] = $queryValue;
            $params['query_module_code'] = $queryValue;
            $params['query_module_name'] = $queryValue;
        }

        if (in_array($status, ['open', 'solved'], true)) {
            $sql .= ' AND p.status = :status';
            $params['status'] = $status;
        }

        if ($module !== '') {
            $sql .= ' AND m.module_code = :module';
            $params['module'] = $module;
        }

        return $sql;
    }

    private function bindDiscussionParams($stmt, array $params)
    {
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
    }

    private function discussionOrderSql(array $filters)
    {
        if (($filters['sort'] ?? '') === 'popular') {
            return '
                ORDER BY reply_count DESC, view_count DESC, p.created_at DESC
            ';
        }

        return '
            ORDER BY p.created_at DESC
        ';
    }

    private function uniqueSlug(string $value, ?int $ignoreId = null)
    {
        // Deterministic numeric suffixes preserve readable URLs while avoiding collisions.
        $baseSlug = $this->slugify($value);
        $slug = $baseSlug;
        $counter = 2;

        while ($this->slugExists($slug, $ignoreId)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function slugify(string $value)
    {
        $slug = strtolower(trim($value));
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug) ?: '';
        $slug = trim($slug, '-');

        return $slug !== '' ? $slug : 'post';
    }

    private function slugExists(string $slug, ?int $ignoreId = null)
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE slug = :slug";
        $params = ['slug' => $slug];

        if ($ignoreId !== null) {
            $sql .= ' AND id != :id';
            $params['id'] = $ignoreId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchColumn() > 0;
    }

}
