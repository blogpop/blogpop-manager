<?php

namespace app\Data\Models;

use App\Data\Models\Abstractions\FileModel;

class Blog extends FileModel
{
    public ?string $entity = 'blog';
    protected ?array $keys = [
        'id',
        'title',
        'slug',
        'description',
        'banner',
        'created_at',
        'updated_at',
    ];
}

