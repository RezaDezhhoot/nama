<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

\Illuminate\Support\Facades\Schedule::command("statistic:calculator")->hourly();

\Illuminate\Support\Facades\Schedule::command("app:auto-accept-limit")->everyTenMinutes();
\Illuminate\Support\Facades\Schedule::command("app:auto-accept-limit --target=reports")->everyTenMinutes();

\Illuminate\Support\Facades\Schedule::command("app:send-notify-limit")->everyThirtyMinutes();
\Illuminate\Support\Facades\Schedule::command("app:send-notify-limit --target=reports")->everyThirtyMinutes();


\Illuminate\Support\Facades\Schedule::command("app:prepare-daily-accounting-record-command mosque --subType=brothers")->dailyAt("12:00");
\Illuminate\Support\Facades\Schedule::command("app:prepare-daily-accounting-record-command mosque --subType=sisters")->dailyAt("12:05");
\Illuminate\Support\Facades\Schedule::command("app:prepare-daily-accounting-record-command school --subType=male")->dailyAt("12:10");
\Illuminate\Support\Facades\Schedule::command("app:prepare-daily-accounting-record-command school --subType=female")->dailyAt("12:15");
\Illuminate\Support\Facades\Schedule::command("app:prepare-daily-accounting-record-command school --subType=support")->dailyAt("12:20");
\Illuminate\Support\Facades\Schedule::command("app:prepare-daily-accounting-record-command center")->dailyAt("12:25");
\Illuminate\Support\Facades\Schedule::command("app:prepare-daily-accounting-record-command university")->dailyAt("12:30");
