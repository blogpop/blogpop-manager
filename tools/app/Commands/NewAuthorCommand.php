<?php

namespace App\Commands;


use App\Domain\Sync\SyncAuthors;
use App\Domain\Sync\SyncBlogs;
use App\Libraries\BlogpopAPI\Traits\WithBlogpopAPI;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use LaravelZero\Framework\Commands\Command;

class NewAuthorCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'new:author {--name=}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Creates an empty author file with the given name.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $name = $this->option('name');

        if(!$name){
            $this->error("You must provide a name.");
            $this->line('');
            $this->info("<fg=yellow>USAGE: <fg=white>./blogpop new:author --name=\"Insert name here\"");
        } else {
            $this->createNewAuthor($name);
        }
    }

    public function createNewAuthor(string $name): void
    {
        $author = [
            'slug' => Str::slug($name),
            'name' => $name,
            "email" => null,
            "bio" => null,
            "avatar" => null,
            "created_at" => Carbon::now()->toDateTimeString(),
            "updated_at" => Carbon::now()->toDateTimeString()
        ];

        if(!Storage::disk('author')->exists(data_get($author, 'slug') . '/author.json')){
            Storage::disk('author')
                ->put(data_get($author, 'slug') . '/author.json', json_encode($author, JSON_PRETTY_PRINT)
                );
            $this->info("Author created: ./authors/" . data_get($author, 'slug').'/');
            $this->info("You should open this file and finish filling in the details for it.");
        } else {
            $this->error("Author already exists: ./authors/" . data_get($author, 'slug').'/');
        }
    }

}
