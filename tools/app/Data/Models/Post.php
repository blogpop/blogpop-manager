<?php

namespace app\Data\Models;

use App\Data\Enums\SyncDirections;
use App\Data\Models\Abstractions\FileModel;
use App\Interfaces\Comparable;
use App\Libraries\BlogpopAPI\Abstractions\APIEntity;
use Illuminate\Support\Facades\Storage;

class Post extends FileModel
{
    public ?string $entity = 'post';
    protected ?array $keys = [
        'id',
        'blog_id',
        'author_id',
        'slug',
        'title',
        'excerpt',
        'body',
        'published',
        'publish_date',
        'featured_image',
        'featured_image_caption',
        'created_at',
        'updated_at',
    ];

    public function __construct(public Blog $blog, protected array $data){
        parent::__construct($data);
    }

    public function getFilePath() : string
    {
        return $this->blog->get('slug')."/posts/".$this->get('slug')."/$this->entity.json";
    }

    public function getContentPath(): string
    {
        return $this->blog->get('slug')."/posts/".$this->get('slug')."/content.md";
    }

    public function getContent(): string
    {
        return Storage::disk($this->getDisk())->get($this->getContentPath());
    }

    public function getDisk(): string
    {
        return $this->blog->entity;
    }

    public function saveRemote(APIEntity $api): void
    {
        $saveData = [...$this->toArray(), 'body'=>$this->getContent()];

        $remoteData = $this->get('id')
            ? $api->update($this->get('id'),$saveData, $this->blog->get('id'))
            : $api->create($saveData, $this->blog->get('id'));

        $this->merge(app(get_class($this), ['blog' => $this->blog, 'data' => $remoteData ]));
        $this->saveLocal();
    }

    public function saveLocal(): void
    {
        Storage::disk($this->getDisk())
            ->put($this->getContentPath(), $this->get('body'));

        $data = $this->toArray();
        $data['body'] = md5($this->getContent());

        Storage::disk($this->getDisk())
            ->put($this->getFilePath(), json_encode($data, JSON_PRETTY_PRINT));
    }

    public function compare(Comparable $other) : SyncDirections
    {
        $shouldDownload = $this->getComparisonDate()->lt($other->getComparisonDate());
        $comparisonKeys = array_keys($this->toArray());
        unset($comparisonKeys[array_search('body', $comparisonKeys)]);

        if ($shouldDownload)
        {
            return SyncDirections::DOWNLOAD;
        } else {
            foreach($comparisonKeys as $key)
            {
                if(data_get($this->getComparisonData(), $key) !== data_get($other->getComparisonData(), $key)){
                    return SyncDirections::UPLOAD;
                }
            }
            if($this->get('body') !== md5($this->getContent())){
                return SyncDirections::UPLOAD;
            }

            return SyncDirections::NO_ACTION;
        }
    }
}

