<?php

namespace App\Libraries\BlogpopAPI\Entities;



use App\Libraries\BlogpopAPI\Abstractions\APIEntity;
use Illuminate\Support\Facades\Http;

class Posts implements APIEntity
{
    public function __construct()
    {}

    public function list(?int $page = 1, ?string $parentId = null): array
    {
        $response = Http::blogpop()->get("/blogs/$parentId/posts?page=$page");

        if($response->failed()){
            $response->throw();
        }

        return json_decode((string)$response->getBody(), true);
    }

    public function listAll(?string $parentId = null): array
    {
        $currentPage = 1;
        $items = [];

        do{
            $paginated = $this->list($currentPage, $parentId);

            $data = data_get($paginated,'data', []);
            $nextPage = data_get($paginated,'next_page_url');

            foreach($data as $item){
                $items[] = $item;
            }

            $currentPage++;
        } while($nextPage !== null);

        return $items;
    }

    public function create(array $data, ?string $parentId = null): array
    {
        $response = Http::blogpop()->post("/blogs/$parentId/posts", $data);

        if($response->failed()){
            $response->throw();
        }

        return json_decode((string)$response->getBody(), true);
    }

    public function update(string $id, array $data, ?string $parentId = null): array
    {
        $response = Http::blogpop()->put("/blogs/$parentId/posts/$id", $data);

        if($response->failed()){
            $response->throw();
        }

        return json_decode((string)$response->getBody(), true);
    }


}
