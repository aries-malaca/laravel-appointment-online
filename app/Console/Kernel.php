<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\BranchCluster;
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
        $schedule->command('expire')->dailyAt('07:00');
        $schedule->command('auditing:clean')->everyFiveMinutes();
        $schedule->command('backup:mysql-dump')->cron(env("DB_BACKUP_SCHEDULE", "* * * * *"));

        //loop through all the clusters
        foreach( BranchCluster::get()->toArray() as $cluster){
            $cluster_data = json_decode($cluster['cluster_data']);
            if(!$cluster_data->ems_supported)
                continue;

            $schedule->command('fetch-technicians', [$cluster['id']] )->cron($cluster_data->ems_cron);
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