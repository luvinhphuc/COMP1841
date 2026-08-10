<?php

namespace App\Helpers;

/**
 * Normalises the configuration shared by discussion and reply content editors.
 */
class ContentInputHelper
{
    private const PRESETS = [
        'discussion' => [
            'id' => 'content',
            'label' => 'Description',
            'placeholder' => 'Body text',
            'rows' => 7,
            'maxlength' => 5000,
            'required' => false,
            'upload_profile' => 'discussion',
            'toolbar' => true,
            'submit_label' => '',
        ],
        'reply' => [
            'id' => 'reply-content',
            'label' => 'Comment content',
            'placeholder' => 'Share a clear explanation, useful resource, or next step.',
            'rows' => 6,
            'maxlength' => 5000,
            'required' => true,
            'upload_profile' => 'reply',
            'toolbar' => true,
            'submit_label' => 'Comment',
        ],
        'edit-discussion' => [
            'id' => 'discussion-edit-content',
            'label' => 'Description',
            'placeholder' => 'Body text',
            'rows' => 8,
            'maxlength' => 5000,
            'required' => true,
            'upload_profile' => 'none',
            'toolbar' => true,
            'submit_label' => '',
        ],
        'edit-reply' => [
            'id' => 'reply-edit-content',
            'label' => 'Reply content',
            'placeholder' => 'Share a clear explanation, useful resource, or next step.',
            'rows' => 8,
            'maxlength' => 5000,
            'required' => true,
            'upload_profile' => 'none',
            'toolbar' => true,
            'submit_label' => '',
        ],
    ];

    private const UPLOAD_PROFILES = [
        'discussion' => [
            'accept' => 'image/jpeg,image/png,image/gif,image/webp,video/mp4,video/webm,video/quicktime,.zip,.txt,.php,.js,.css,.html,.htm,.json,.xml,.sql,.py,.java,.c,.cpp,.cs,.md,.pdf,.doc,.docx',
            'help' => 'Up to 5 files (100 MB total). Images and documents: 10 MB; videos: 50 MB each.',
        ],
        'reply' => [
            'accept' => 'image/jpeg,image/png,image/gif,image/webp',
            'help' => 'Drag and drop or upload up to 5 images, 10 MB each and 100 MB total.',
        ],
        'none' => ['accept' => '', 'help' => ''],
    ];
    
    public static function normalise(array $config): array
    {
        $variant = trim((string)($config['variant'] ?? 'discussion'));

        if (!isset(self::PRESETS[$variant])) {
            $variant = 'discussion';
        }

        $preset = self::PRESETS[$variant];
        $fieldId = trim((string)($config['id'] ?? $preset['id']));
        $fieldId = $fieldId !== '' ? $fieldId : $preset['id'];
        $errors = is_array($config['errors'] ?? null) ? $config['errors'] : [];
        $uploadProfile = (string)$preset['upload_profile'];

        return [
            'variant' => $variant,
            'field_id' => $fieldId,
            'value' => (string)($config['value'] ?? ''),
            'label' => (string)$preset['label'],
            'placeholder' => (string)$preset['placeholder'],
            'rows' => (int)$preset['rows'],
            'maxlength' => $preset['maxlength'],
            'required' => (bool)$preset['required'],
            'upload_profile' => $uploadProfile,
            'show_toolbar' => (bool)$preset['toolbar'],
            'submit_label' => array_key_exists('submit_label', $config)
                ? trim((string)$config['submit_label'])
                : (string)$preset['submit_label'],
            'content_error' => trim((string)($errors['content'] ?? '')),
            'attachments_error' => trim((string)($errors['attachments'] ?? '')),
            'show_attachments' => $uploadProfile !== 'none',
            'allow_video' => $uploadProfile === 'discussion',
            'attachment_accept' => (string)self::UPLOAD_PROFILES[$uploadProfile]['accept'],
            'attachment_help' => (string)self::UPLOAD_PROFILES[$uploadProfile]['help'],
        ];
    }
}
