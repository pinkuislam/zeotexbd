<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('migrate-all {fresh?} {seed?}', function ($fresh = null, $seed = null) {
    $fresh = $fresh == 'fresh' ? ':fresh' : '';
    Artisan::call('migrate' . $fresh);
    Artisan::call('migrate --path=database/migrations/hr');

    $this->info("All Migration Done!");

    if ($seed == 'seed') {
        Artisan::call('db:seed');
        $this->info("Seeder run successfully!");
    }
});
