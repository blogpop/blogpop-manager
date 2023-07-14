<?php

namespace App\Domain\Sync;

use App\Data\Enums\SyncDirections;
use app\Data\Models\Author;
use App\Data\Models\Blog;
use App\Exceptions\ValidationException;
use App\Libraries\BlogpopAPI\Traits\WithBlogpopAPI;
use Illuminate\Console\Concerns\InteractsWithIO;
use Illuminate\Console\OutputStyle;
use Illuminate\Support\Facades\Storage;

class SyncBlogs
{
    use InteractsWithIO;
    use WithBlogpopAPI;

    protected string $type = 'blog';

    /**
     * @throws ValidationException
     */
    public function __invoke(OutputStyle $output): void
    {
        $this->output = $output;

        $remoteBlogs = $this->blogpop()->blogs()->listAll();
        $this->syncList($remoteBlogs);

        $directories = Storage::disk($this->type)->directories();
        $localBlogs = array_map(function($directory){
            return json_decode(Storage::disk($this->type)->get($directory."/$this->type.json"), true);
        }, $directories);

        $this->syncList($localBlogs);
    }

    /**
     * @throws ValidationException
     */
    private function syncList(array $blogs = []): void
    {
        if(count($blogs)){
            foreach($blogs as $blogData){
                try {
                    $blog = new Blog($blogData);
                    $this->sync($blog);
                    app(SyncPosts::class)->__invoke($blog, $this->output);
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
    private function sync(Blog $blog): void
    {
        if(!$blog->get('id')) {
            $this->saveRemote($blog);
            return;
        }

        if(!$blog->fileExists()) {
            $this->saveLocal($blog);
            return;
        }

        $localBlog = new Blog($blog->getFile());
        $direction = $localBlog->compare($blog);

        if($direction === SyncDirections::DOWNLOAD){
            $localBlog->merge($blog);
            $this->saveLocal($localBlog);
            return;
        }

        if($direction === SyncDirections::UPLOAD){
            $blog->merge($localBlog);
            $this->saveRemote($blog);
            return;
        }
    }

    /**
     * @throws ValidationException
     */
    private function saveRemote(Blog $blog): void
    {
        $this->info('       - [Uploading] '.$blog->get('title'));
        $blog->saveRemote($this->blogpopAPI->blogs());
    }

    /**
     * @param Blog $blog
     * @return void
     */
    private function saveLocal(Blog $blog): void
    {
        $this->info('       - [Downloading] '.$blog->get('title'));
        $blog->saveLocal();
    }
}
