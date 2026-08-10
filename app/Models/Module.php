<?php

namespace App\Models;

/**
 * Represents an academic module used to categorise discussions.
 */
class Module
{
    public ?int $id = null;
    public string $module_code = '';
    public string $module_name = '';
    public ?string $description = null;
    public ?string $created_at = null;
    public ?string $updated_at = null;

    public function __construct(array $data = [])
    {
        $this->id = isset($data['id']) ? (int) $data['id'] : null;
        $this->module_code = (string) ($data['module_code'] ?? '');
        $this->module_name = (string) ($data['module_name'] ?? '');
        $this->description = isset($data['description']) ? (string) $data['description'] : null;
        $this->created_at = isset($data['created_at']) ? (string) $data['created_at'] : null;
        $this->updated_at = isset($data['updated_at']) ? (string) $data['updated_at'] : null;
    }
}
