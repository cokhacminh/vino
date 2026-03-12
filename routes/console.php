<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Tạo kpi_users cho các KPI Hàng Tháng vào ngày 1 hàng tháng
Schedule::command('kpi:monthly-create')->monthlyOn(1, '01:00');
