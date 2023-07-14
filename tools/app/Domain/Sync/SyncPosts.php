<?php

namespace App\Domain\Sync;

use App\Data\Enums\SyncDirections;
use App\Data\Models\Post;
use app\Data\Models\Blog;
use App\Exceptions\ValidationException;
use App\Libraries\BlogpopAPI\Traits\WithBlogpopAPI;
use Illuminate\Console\Concerns\InteractsWithIO;
use Illuminate\Console\OutputStyle;
use Illuminate\Support\Facades\Storage;

class SyncPosts
{
    use InteractsWithIO;
    use WithBlogpopAPI;

    protected Blog $blog;
    /**
     * @throws ValidationException
     */
    public function __invoke(Blog $blog, OutputStyle $output): void
    {
        $this->blog = $blog;
        $this->output = $output;

        $remotePosts = $this->blogpop()->posts()->listAll($this->blog->get('id'));
        $this->syncList($remotePosts);


        $directories = Storage::disk('blog')->directories("{$this->blog->get('slug')}/posts");
        $localPosts = array_map(function($directory){
            return json_decode(Storage::disk('blog')->get($directory.'/post.json'), true);
        }, $directories);

        $this->syncList($localPosts);
    }

    /**
     * @throws ValidationException
     */
    private function syncList(array $posts = []): void
    {
        if(count($posts)){
            foreach($posts as $post){
                try {
                    $this->sync(new Post($this->blog, $post));
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
    private function sync(Post $post): void
    {
        if(!$post->get('id')) {
            $this->saveRemote($post);
            return;
        }

        if(!$post->fileExists()) {
            $this->saveLocal($post);
            return;
        }

        $localPost = new Post($this->blog, $post->getFile());
        $direction = $localPost->compare($post);

        if($direction === SyncDirections::DOWNLOAD){
            $localPost->merge($post);
            $this->saveLocal($localPost);
            return;
        }

        if($direction === SyncDirections::UPLOAD){
            $post->merge($localPost);
            $this->saveRemote($post);
            return;
        }
    }

    private function saveRemote(Post $post): void
    {
        $this->info('       - [Uploading] '.$post->get('title'));
        $post->saveRemote($this->blogpopAPI->posts());
    }

    /**
     * @param Post $post
     * @return void
     */
    private function saveLocal(Post $post): void
    {
        $this->info('       - [Downloading] '.$post->get('title'));
        $post->saveLocal();
    }

}
