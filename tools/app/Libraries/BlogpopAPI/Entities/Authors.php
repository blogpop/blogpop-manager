<?php

namespace App\Libraries\BlogpopAPI\Entities;



use app\Data\Models\Author;
use App\Libraries\BlogpopAPI\Abstractions\APIEntity;
use Illuminate\Support\Facades\Http;

class Authors implements APIEntity
{
    public function list(?int $page = 1, ?string $parentId = null): array
    {
        $response = Http::blogpop()->get("/authors?page=$page");

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
            $paginated = $this->list($currentPage);

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
        $response = Http::blogpop()->post("/authors", $data);

        if($response->failed()){
            $response->throw();
        }

        return json_decode((string)$response->getBody(), true);
    }

    public function update(string $id, array $data, ?string $parentId = null): array
    {
        $response = Http::blogpop()->put("/authors/$id", $data);

        if($response->failed()){
            $response->throw();
        }

        return json_decode((string)$response->getBody(), true);
    }

}
