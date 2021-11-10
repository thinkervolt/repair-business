<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Mail;
use Illuminate\Support\Facades\File; 
use stdClass;

class WeeklyData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'weekly:data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Weekly Data';

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
        /* MYSQL DUMP */
        $database_plan_path = "/app/database-plan/";
        if(!Storage::exists("database-plan")){
            Storage::makeDirectory("database-plan");
        }
        $filename = "weekly-database-plan-" . Carbon::now()->format('Y-m-d') . ".gz";
        $command = "mysqldump --no-tablespaces --user=" . env('DB_USERNAME') ." --password=" . env('DB_PASSWORD') . " --host=" . env('DB_HOST') . " " . env('DB_DATABASE') . "  | gzip > ". storage_path()  . $database_plan_path . $filename;
        $returnvar = NULL;
        $output  = NULL;
        exec($command, $output, $returnvar);
        /* END MYSQL DUMP */

        /* EMAIL */
        $mail_data = new stdClass();
        $mail_data->title = 'Weekly - Data and Reports';
        $mail_data->preview = 'Weekly - Data and Reports';
        $mail_data->greeting= 'I am a Robot,';
        $mail_data->main_message= 'Please do not Reply, here is some weekly data that I collected for you from your Repair Business App.';
        $mail_data->footer_main_message= 'If you need to restore your System or Database, please send your attachments to your Service Provider.';

        $attachment = storage_path()  . $database_plan_path . $filename;
        Mail::to('rustedchip@gmail.com','ROBOT')->send(new \App\Mail\WeeklyData($attachment,$mail_data));
        /* END EMAIL */

        File::delete(storage_path()  . $database_plan_path . $filename);


    }
}
