<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

\Illuminate\Support\Facades\Schedule::command("statistic:calculator")->hourly();
\Illuminate\Support\Facades\Schedule::command("app:auto-accept-limit")->everyTenMinutes();
\Illuminate\Support\Facades\Schedule::command("app:auto-accept-limit --target=reports")->everyTenMinutes();
