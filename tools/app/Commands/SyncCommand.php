<?php

namespace App\Commands;


use App\Domain\Sync\SyncAuthors;
use App\Domain\Sync\SyncBlogs;
use App\Libraries\BlogpopAPI\Traits\WithBlogpopAPI;
use LaravelZero\Framework\Commands\Command;

class SyncCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'sync';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Syncs your author, blogs and posts with the blogpop server.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {

        $this->task("Syncing Authors", function () {
            app(SyncAuthors::class)->__invoke($this->output);
            return true;
        });
        $this->task("Syncing Blogs", function () {
            app(SyncBlogs::class)->__invoke($this->output);
            return true;
        });
    }

}
