<?php

namespace App\Domain\Sync;

use App\Data\Enums\SyncDirections;
use App\Data\Models\Author;
use App\Exceptions\ValidationException;
use App\Libraries\BlogpopAPI\Traits\WithBlogpopAPI;
use Illuminate\Console\Concerns\InteractsWithIO;
use Illuminate\Console\OutputStyle;
use Illuminate\Support\Facades\Storage;

class SyncAuthors
{
    use InteractsWithIO;
    use WithBlogpopAPI;

    /**
     * @throws ValidationException
     */
    public function __invoke(OutputStyle $output): void
    {
        $this->output = $output;

        $remoteAuthors = $this->blogpop()->authors()->listAll();
        $this->syncList($remoteAuthors);

        $directories = Storage::disk('author')->directories();
        $localAuthors = array_map(function($directory){
            return json_decode(Storage::disk('author')->get($directory.'/author.json'), true);
        }, $directories);

        $this->syncList($localAuthors);
    }

    /**
     * @throws ValidationException
     */
    private function syncList(array $authors = []): void
    {
        if(count($authors)){
            foreach($authors as $author){
                try {
                    $this->sync(new Author($author));
                } catch (\Exception $e) {
                    $this->error($e->getMessage());
                    continue;
                }
            }
        }
    }

    /**
     * @throws ValidationException
     */
    private function sync(Author $author): void
    {
        if(!$author->get('id')) {
            $this->saveRemote($author);
            return;
        }

        if(!$author->fileExists()) {
            $this->saveLocal($author);
            return;
        }

        $localAuthor = new Author($author->getFile());
        $direction = $localAuthor->compare($author);

        if($direction === SyncDirections::DOWNLOAD){
            $localAuthor->merge($author);
            $this->saveLocal($localAuthor);
            return;
        }

        if($direction === SyncDirections::UPLOAD){
            $author->merge($localAuthor);
            $this->saveRemote($author);
            return;
        }
    }

    /**
     * @throws ValidationException
     */
    private function saveRemote(Author $author): void
    {
        $this->info('       - [Uploading] '.$author->get('name'));
        $author->saveRemote($this->blogpopAPI->authors());
    }

    /**
     * @param Author $author
     * @return void
     */
    private function saveLocal(Author $author): void
    {
        $this->info('       - [Downloading] '.$author->get('name'));
        $author->saveLocal();
    }
}
