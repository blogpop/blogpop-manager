<?php

namespace app\Data\Models;

use App\Data\Models\Abstractions\FileModel;

class Author extends FileModel
{
    public ?string $entity = 'author';
    protected ?array $keys = [
        'id',
        'name',
        'slug',
        'email',
        'bio',
        'avatar',
        'created_at',
        'updated_at',
    ];


}
