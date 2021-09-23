<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class DataBasePlan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:plan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'mysql-dump';

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
        $database_plan_path = "/app/database-plan/";
    

        if(!Storage::exists("database-plan")){
            Storage::makeDirectory("database-plan");
        }

        $filename = "database-plan-" . Carbon::now()->format('Y-m-d') . ".gz";

        echo storage_path()  . $database_plan_path . $filename;
        $command = "mysqldump --no-tablespaces --user=" . env('DB_USERNAME') ." --password=" . env('DB_PASSWORD') . " --host=" . env('DB_HOST') . " " . env('DB_DATABASE') . "  | gzip > ". storage_path()  . $database_plan_path . $filename;
        $returnVar = NULL;
        $output  = NULL;

        exec($command, $output, $returnVar);
    }
}
