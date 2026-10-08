<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; // <-- 1. นำเข้า URL Facade

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // 2. บังคับให้ Laravel สร้าง URL ทุกประเภท (รวมถึง route และ asset) เป็น https เสมอ
        if (config('app.env') === 'production' || app()->environment('production')) {
            URL::forceScheme('https');
        } else {
            URL::forceScheme('https');
        }
    }
}