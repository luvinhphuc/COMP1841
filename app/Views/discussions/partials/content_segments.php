<?php
/**
 * Renders text and code segments created by DiscussionController.
 *
 * @var array<int, array{type?: string, language?: string, content?: string}> $contentSegments
 */

$contentSegmentsData = is_array($contentSegments ?? null) ? $contentSegments : [];
$contentSegmentLanguages = ['markup', 'css', 'javascript', 'php', 'sql', 'python', 'java', 'json', 'none'];
?>

<?php foreach ($contentSegmentsData as $contentSegment): ?>
<?php
    $contentSegmentType = trim((string)($contentSegment['type'] ?? 'text'));
    $contentSegmentValue = (string)($contentSegment['content'] ?? '');
    $contentSegmentLanguage = strtolower(trim((string)($contentSegment['language'] ?? 'none')));

    if (!in_array($contentSegmentLanguage, $contentSegmentLanguages, true)) {
        $contentSegmentLanguage = 'none';
    }

    if (trim($contentSegmentValue) === '') {
        continue;
    }
    ?>
<?php if ($contentSegmentType === 'code'): ?>
<pre
    class="language-<?= $contentSegmentLanguage ?> overflow-x-auto rounded-xl px-4 py-3 text-sm leading-6"><code
                    class="language-<?= $contentSegmentLanguage ?>"><?= htmlspecialchars($contentSegmentValue, ENT_QUOTES, 'UTF-8') ?></code></pre>
<?php else: ?>
<p class="whitespace-pre-wrap" dir="auto"><?= htmlspecialchars($contentSegmentValue, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>
<?php endforeach; ?>