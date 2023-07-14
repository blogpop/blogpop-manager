<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use function Termwind\{render};

class HelpCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'help:me';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Display the help message.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info("<fg=white>
==================================================
    __     __
   / /_   / / ___   ____   ____   ____   ____
  / __ \ / // __ \ / __ \// __ \// __ \// __ \
 / /_/ // // /_/ // /_/ // /_/ // /_/ // /_/ /
/_.___//_/ \____/ \__, // .___/ \____// .___/
               /____/ //_/           /_/
==================================================
                    An API based blogging platform
");

        $this->info("<fg=green>".config('app.version'));
        $this->line('');
        $this->info("<fg=yellow>USAGE: <fg=white>./blogpop <command> [options]");
        $this->line('');
        $this->info("<fg=green>     help:me         <fg=white>Print out the help screen");
        $this->line('');
        $this->info("<fg=green>     sync            <fg=white>Syncs your blogs with the blogpop server");
        $this->line('');
        $this->info("<fg=green>     new:author      <fg=white>Creates a new author");
        $this->info("<fg=green>     new:blog        <fg=white>Creates a new blog");
        $this->info("<fg=green>     new:post        <fg=white>Creates a new post");
        $this->line('');
    }

}
