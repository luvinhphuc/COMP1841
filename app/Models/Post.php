<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Post
{
    private PDO $db;
    private ?array $userColumns = null;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function getAll(int $limit = 20, int $offset = 0)
    {
        $stmt = $this->db->prepare($this->baseSelectSql() . '
            ORDER BY p.created_at DESC
            LIMIT :limit OFFSET :offset
        ');

        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getLatest(int $limit = 10)
    {
        $stmt = $this->db->prepare($this->baseSelectSql() . '
            ORDER BY p.created_at DESC
            LIMIT :limit
        ');

        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getByModuleId(int $moduleId, int $limit = 20, int $offset = 0)
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

        return $stmt->fetchAll();
    }

    public function find(int $id)
    {
        $stmt = $this->db->prepare($this->baseSelectSql() . '
            AND p.id = :id
            LIMIT 1
        ');

        $stmt->execute(['id' => $id]);
        $post = $stmt->fetch();

        return $post ?: null;
    }

    public function findBySlug(string $slug)
    {
        $stmt = $this->db->prepare($this->baseSelectSql() . '
            AND p.slug = :slug
            LIMIT 1
        ');

        $stmt->execute(['slug' => $slug]);
        $post = $stmt->fetch();

        return $post ?: null;
    }

    public function create(array $data)
    {
        $title = trim((string) ($data['title'] ?? ''));
        $content = trim((string) ($data['content'] ?? ''));
        $userId = (int) ($data['user_id'] ?? 0);
        $moduleId = (int) ($data['module_id'] ?? 0);
        $status = $this->normaliseStatus((string) ($data['status'] ?? 'open'));
        $slug = trim((string) ($data['slug'] ?? ''));

        if ($title === '' || $content === '' || $userId <= 0 || $moduleId <= 0) {
            return 0;
        }

        $stmt = $this->db->prepare(
            'INSERT INTO posts (title, slug, content, status, user_id, module_id)
             VALUES (:title, :slug, :content, :status, :user_id, :module_id)'
        );

        $stmt->execute([
            'title' => $title,
            'slug' => $this->uniqueSlug($slug !== '' ? $slug : $title),
            'content' => $content,
            'status' => $status,
            'user_id' => $userId,
            'module_id' => $moduleId,
        ]);

        return (int) $this->db->lastInsertId();
    }

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

            if ($content === '') {
                return false;
            }

            $fields[] = 'content = :content';
            $params['content'] = $content;
        }

        if (array_key_exists('status', $data)) {
            $fields[] = 'status = :status';
            $params['status'] = $this->normaliseStatus((string) $data['status']);
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
        $sql = 'UPDATE posts SET ' . implode(', ', $fields) . ' WHERE id = :id AND deleted_at IS NULL';
        $stmt = $this->db->prepare($sql);

        return $stmt->execute($params);
    }

    public function delete(int $id)
    {
        $stmt = $this->db->prepare(
            'UPDATE posts
             SET deleted_at = NOW()
             WHERE id = :id AND deleted_at IS NULL'
        );

        return $stmt->execute(['id' => $id]);
    }

    public function setStatus(int $id, string $status)
    {
        $stmt = $this->db->prepare(
            'UPDATE posts
             SET status = :status, updated_at = NOW()
             WHERE id = :id AND deleted_at IS NULL'
        );

        return $stmt->execute([
            'id' => $id,
            'status' => $this->normaliseStatus($status),
        ]);
    }

    public function recordView(int $postId, ?int $userId = null)
    {
        $stmt = $this->db->prepare(
            'INSERT INTO post_views (post_id, user_id)
             VALUES (:post_id, :user_id)'
        );

        return $stmt->execute([
            'post_id' => $postId,
            'user_id' => $userId,
        ]);
    }

    public function getTrendingModules(int $limit = 5)
    {
        $stmt = $this->db->prepare(
            'SELECT
                m.id,
                m.module_code AS code,
                m.module_name AS name,
                COUNT(p.id) AS post_count
             FROM modules m
             INNER JOIN posts p ON p.module_id = m.id AND p.deleted_at IS NULL
             GROUP BY m.id, m.module_code, m.module_name
             ORDER BY post_count DESC, m.module_code ASC
             LIMIT :limit'
        );

        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getRecentViews(?int $userId = null, int $limit = 5)
    {
        $userNameSelect = $this->userNameSelectSql();
        $sql = '
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
                pv.viewed_at,
                (
                    SELECT COUNT(*)
                    FROM replies r
                    WHERE r.post_id = p.id AND r.deleted_at IS NULL
                ) AS reply_count,
                (
                    SELECT COUNT(*)
                    FROM post_views post_view_count
                    WHERE post_view_count.post_id = p.id
                ) AS view_count,
                (
                    SELECT path
                    FROM media media_item
                    WHERE media_item.post_id = p.id
                    ORDER BY media_item.created_at ASC
                    LIMIT 1
                ) AS media_path
            FROM post_views pv
            INNER JOIN posts p ON p.id = pv.post_id
            INNER JOIN modules m ON m.id = p.module_id
            INNER JOIN users u ON u.id = p.user_id
            WHERE p.deleted_at IS NULL
        ';
        $params = [];

        if ($userId !== null) {
            $sql .= ' AND pv.user_id = :user_id';
            $params['user_id'] = $userId;
        }

        $sql .= '
            ORDER BY pv.viewed_at DESC
            LIMIT :limit
        ';

        $stmt = $this->db->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_INT);
        }

        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    private function baseSelectSql(string $extraSelect = '')
    {
        $extraSelect = $extraSelect !== '' ? ', ' . $extraSelect : '';
        $userNameSelect = $this->userNameSelectSql();

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
                (
                    SELECT COUNT(*)
                    FROM post_views pv
                    WHERE pv.post_id = p.id
                ) AS view_count,
                (
                    SELECT path
                    FROM media media_item
                    WHERE media_item.post_id = p.id
                    ORDER BY media_item.created_at ASC
                    LIMIT 1
                ) AS media_path
                ' . $extraSelect . '
            FROM posts p
            INNER JOIN modules m ON m.id = p.module_id
            INNER JOIN users u ON u.id = p.user_id
            WHERE p.deleted_at IS NULL
        ';
    }

    private function userNameSelectSql()
    {
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

    private function normaliseStatus(string $status)
    {
        return in_array($status, ['open', 'solved'], true) ? $status : 'open';
    }

    private function uniqueSlug(string $value, ?int $ignoreId = null)
    {
        $baseSlug = $this->slugify($value);
        $slug = $baseSlug;
        $counter = 2;

        while ($this->slugExists($slug, $ignoreId)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function slugExists(string $slug, ?int $ignoreId = null)
    {
        $sql = 'SELECT COUNT(*) FROM posts WHERE slug = :slug';
        $params = ['slug' => $slug];

        if ($ignoreId !== null) {
            $sql .= ' AND id != :id';
            $params['id'] = $ignoreId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn() > 0;
    }

    private function slugify(string $value)
    {
        $slug = strtolower(trim($value));
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug) ?: '';
        $slug = trim($slug, '-');

        return $slug !== '' ? $slug : 'post';
    }
}
