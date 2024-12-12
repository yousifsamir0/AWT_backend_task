<?php

use App\Jobs\FetchRandomUserJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();


Schedule::command('delete:old-posts')->daily();
Schedule::job(new FetchRandomUserJob, connection: 'sync')->everySixHours();
