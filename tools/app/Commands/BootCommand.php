<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class BootCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'boot {cmd?} {--token=} {--name=} {--title=} {--blog=} {--author=}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Display an inspiring quote';

    protected $allowedOptions = [
        "auth" => ["--token"],
        "sync" => [],
        "new:author" => ["--name"],
        "new:blog" => ["--title"],
        "new:post" => [
            "--blog",
            "--title",
            "--author"
        ]
    ];

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $command = $this->argument('cmd') ?? "help:me";
        try {
            $this->runCommand($command, $this->allowOptions($command), $this->output);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }

    }

    /**
     * Define the command's schedule.
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }

    private function allowOptions(?string $command): array
    {
        $allowedOptions = data_get($this->allowedOptions, $command, []);


        $options = [];
        foreach ($this->options() as $key => $value) {
            $options['--' . $key] = $value;
        }

        $allowed = [];
        foreach ($options as $key => $value) {
            if (in_array($key, $allowedOptions)) {
                $allowed[$key] = $value;
            }
        }
        return $allowed;
    }

}
