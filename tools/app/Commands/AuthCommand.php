<?php

namespace App\Commands;

use Illuminate\Support\Str;
use LaravelZero\Framework\Commands\Command;

class AuthCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'auth {--token=}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Sets the blogpop API Token';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $token = $this->option('token');

        if(!$token){
            $this->error("You must provide a token.");
            $this->line('');
            $this->info("<fg=yellow>USAGE: <fg=white>./blogpop auth --token=YOUR_TOKEN");
        } else {
            $this->replaceToken($token);
        }


    }

    private function replaceToken(string $token): void
    {
        if($this->validateToken($token)){
            $env = file_get_contents(base_path('.env'));

            if (preg_match("/^BLOGPOP_API_TOKEN=(.*)$/m", $env, $matches)) {
                $newEnv = str_replace("BLOGPOP_API_TOKEN={$matches[1]}", "BLOGPOP_API_TOKEN={$token}", $env);
                file_put_contents(base_path('.env'), $newEnv);
            }
        }
    }

    private function validateToken(string $token): bool
    {
        foreach (["\n", "="] as $char) {
            if (Str::contains($token, $char)) {
                $this->error('Invalid token provided.');
                return false;
            }
        }
        return true;
    }
}
