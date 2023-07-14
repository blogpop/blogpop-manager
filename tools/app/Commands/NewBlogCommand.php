<?php

namespace App\Commands;


use App\Domain\Sync\SyncAuthors;
use App\Domain\Sync\SyncBlogs;
use App\Libraries\BlogpopAPI\Traits\WithBlogpopAPI;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use LaravelZero\Framework\Commands\Command;

class NewBlogCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'new:blog {--title=}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Creates an empty blog file with the given title.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $title = $this->option('title');

        if(!$title){
            $this->error("You must provide a title.");
            $this->line('');
            $this->info("<fg=yellow>USAGE: <fg=white>./blogpop new:blog --title=\"Insert title here\"");
        } else {
            $this->createNewBlog($title);
        }
    }

    public function createNewBlog(string $title): void
    {
        $blog = [
            'slug' => Str::slug($title),
            'title' => $title,
            "description" => null,
            "banner" => null,
            "created_at" => Carbon::now()->toDateTimeString(),
            "updated_at" => Carbon::now()->toDateTimeString()
        ];

        if(!Storage::disk('blog')->exists(data_get($blog, 'slug') . '/blog.json')){
            Storage::disk('blog')
                ->put(data_get($blog, 'slug') . '/blog.json', json_encode($blog, JSON_PRETTY_PRINT)
                );
            $this->info("Blog created: ./blogs/" . data_get($blog, 'slug').'/');
            $this->info("You should open this file and finish filling in the details for it.");
        } else {
            $this->error("Blog already exists: ./blogs/" . data_get($blog, 'slug').'/');
        }
    }

}
