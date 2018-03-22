<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Storage;

class Kernel extends ConsoleKernel
{

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule){
        $schedule->command('expire')->hourly();
        $schedule->command('pull-technicians')->hourly();

        if(Storage::disk('local')->exists('cron.json')){
            $clusters = json_decode(Storage::disk('local')->get('cron.json'));
            foreach($clusters as $cluster){
                $schedule->call(function () use ($cluster) {
                    file_get_contents(env('APP_URL').'/api/technician/fetchEMSTechnicians/' . $cluster->id);
                })->cron($cluster->run);
            }
        }

    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands(){
        require base_path('routes/console.php');
        $this->load(__DIR__.'/Commands');
    }
}
