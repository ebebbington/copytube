<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class OptimiseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'optimise';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make optimisations to improve speed';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        shell_exec("composer dumpautoload -o && php artisan route:cache && php artisan cache:clear && php artisan api:cache && php artisan config:cache");
    }
}
