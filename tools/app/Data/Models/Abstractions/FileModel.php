<?php

namespace App\Data\Models\Abstractions;

use App\Exceptions\ValidationException;
use App\Interfaces\Comparable;
use App\Libraries\BlogpopAPI\Abstractions\APIEntity;
use App\Traits\WithComparable;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Storage;

/**
 *
 */
abstract class FileModel implements Arrayable, Comparable {
    use WithComparable;

    /**
     * @var string|null
     */
    public ?string $entity = null;
    /**
     * @var array|null
     */
    protected ?array $keys = null;

    /**
     * @throws ValidationException
     */
    public function __construct(protected array $data){
        $this->validate();
    }

    public function getDisk(): string
    {
        return $this->entity;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_intersect_key($this->data, array_flip($this->keys));
    }

    /**
     * @param string $key
     * @return array|mixed
     */
    public function get(string $key) : mixed
    {
        return data_get($this->data, $key);
    }

    public function getFilePath() : string
    {
        return $this->get('slug')."/$this->entity.json";
    }

    /**
     * @return null|array
     */
    public function getFile(): ?array
    {
        $fileContent = Storage::disk($this->getDisk())->get($this->getFilePath());

        return $this->fileExists() ? json_decode($fileContent, true) : null;
    }

    /**
     * @return bool
     */
    public function fileExists(): bool
    {
        return Storage::disk($this->getDisk())->exists($this->getFilePath());
    }

    public function merge(FileModel $other): void
    {
        $this->data = [
            ...$this->data,
            ...$other->toArray()
        ];
    }

    public function saveLocal(): void
    {
        Storage::disk($this->getDisk())
            ->put($this->getFilePath(), json_encode($this->toArray(), JSON_PRETTY_PRINT));
    }

    public function saveRemote(APIEntity $api): void
    {
        $remoteData = $this->get('id')
            ? $api->update($this->get('id'), $this->toArray())
            : $api->create($this->toArray());

        $this->merge(app(get_class($this), ['data'=>$remoteData]));
        $this->saveLocal();
    }

    /**
     * @return Carbon
     */
    public function getComparisonDate(): Carbon
    {
        return Carbon::parse($this->get('updated_at'));
    }

    /**
     * @return array
     */
    public function getComparisonData(): array
    {
        return $this->toArray();
    }

    /**
     * @return void
     * @throws ValidationException
     */
    private function validate() : void {
        if(!isset($this->entity)) {
            throw new ValidationException('Entity not set');
        }

        if(!isset($this->keys)) {
            throw new ValidationException('Keys not set');
        }

        if(!isset($this->data['slug'])) {
            throw new ValidationException('Slug not set');
        }
    }
}
