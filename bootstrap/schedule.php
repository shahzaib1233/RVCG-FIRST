<?php

use Illuminate\Console\Scheduling\Schedule;

return function (Schedule $schedule) {
    // Define your scheduled tasks here
    $schedule->command('notifications:send-scheduled')->everyMinute();
};
