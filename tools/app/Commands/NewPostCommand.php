<?php

namespace App\Commands;


use App\Data\Models\Author;
use App\Data\Models\Blog;
use App\Exceptions\ValidationException;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use LaravelZero\Framework\Commands\Command;

class NewPostCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'new:post {--blog=} {--title=} {--author=}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Creates an empty post file with the given title.';

    /**
     * Execute the console command.
     * @throws ValidationException
     */
    public function handle(): void
    {
        $blogSlug = $this->option('blog');
        $title = $this->option('title');
        $authorSlug = $this->option('author');


        $blog = $this->getBlog($blogSlug);
        $author = $this->getAuthor($authorSlug);

        if(!$blog || !$blog->get('id')){
            $this->error("You must provide a valid blog.  Check the slug and make sure it exists.  Also makes sure the blog has been synced.");
            $this->line('');
            $this->info("<fg=yellow>USAGE: <fg=white>./blogpop new:post --blog=\"slug-of-blog\" --title=\"Insert title here\" --author=\"slug-of-author\"");
        } else if(!$title){
            $this->error("You must provide a title.");
            $this->line('');
            $this->info("<fg=yellow>USAGE: <fg=white>./blogpop new:post --blog=\"slug-of-blog\" --title=\"Insert title here\" --author=\"slug-of-author\"");
        } else if(!$author || !$author->get('id')){
            $this->error("You must provide an valid author.  Check the slug and make sure it exists.  Also makes sure the author has been synced.");
            $this->line('');
            $this->info("<fg=yellow>USAGE: <fg=white>./blogpop new:post --blog=\"slug-of-blog\" --title=\"Insert title here\" --author=\"slug-of-author\"");
        } else {
            $this->createNewPost($blog, $title, $author);
        }
    }

    public function createNewPost(Blog $blog, string $title, Author $author): void
    {
        $postData = [
            'blog_id' => $blog->get('id'),
            'author_id' => $author->get('id'),
            'slug' => Str::slug($title),
            'title' => $title,
            "excerpt" => null,
            "body" => null,
            "publish_date" => null,
            "featured_image" => null,
            "featured_image_caption" => null,
            "created_at" => Carbon::now()->toDateTimeString(),
            "updated_at" => Carbon::now()->toDateTimeString()
        ];

        if(!Storage::disk('blog')->exists($blog->get('slug') . '/posts/' . data_get($postData, 'slug') . '/post.json')){
            Storage::disk('blog')->put($blog->get('slug') . '/posts/' . data_get($postData, 'slug') . '/post.json', json_encode($postData, JSON_PRETTY_PRINT));
            Storage::disk('blog')->put($blog->get('slug') . '/posts/' . data_get($postData, 'slug') . '/content.md', '');
            $this->info("Post created: ./blogs/" . $blog->get('slug').'/posts/'.data_get($postData, 'slug').'/');
            $this->info("You should open this file and finish filling in the details for it.");
        } else {
            $this->error("Post already exists: ./blogs/" . $blog->get('slug').'/posts/'.data_get($postData, 'slug').'/');
        }
    }

    /**
     * @throws ValidationException
     */
    public function getBlog(?string $blogSlug): ?Blog
    {
        if($blogSlug && Storage::disk('blog')->exists($blogSlug.'/blog.json')){
            return new Blog(json_decode(Storage::disk('blog')->get($blogSlug.'/blog.json'), true) ?? []);
        }
        return null;
    }

    /**
     * @throws ValidationException
     */
    public function getAuthor(?string $authorSlug): ?Author
    {
        if($authorSlug && Storage::disk('author')->exists($authorSlug.'/author.json')){
            return new Author(json_decode(Storage::disk('author')->get($authorSlug.'/author.json'), true) ?? []);
        }
        return null;
    }

}
