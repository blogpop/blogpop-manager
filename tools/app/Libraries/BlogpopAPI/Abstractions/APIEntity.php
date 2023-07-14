<?php

namespace App\Libraries\BlogpopAPI\Abstractions;

use app\Data\Models\Author;
use Illuminate\Support\Facades\Http;

interface APIEntity
{
    public function list(?int $page = 1, ?string $parentId = null): array;

    public function listAll(?string $parentId = null): array;

    public function create(array $data, ?string $parentId = null);

    public function update(string $id, array $data, ?string $parentId = null);
}
