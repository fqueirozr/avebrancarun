<?php

namespace App\Providers;

use App\Models\MailSetting;
use App\Payments\PaymentGateway;
use App\Payments\PaymentGatewayManager;
use App\Settings\ApplyMailSettings;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PaymentGateway::class, PaymentGatewayManager::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(ApplyMailSettings $applyMailSettings): void
    {
        if (Schema::hasTable('mail_settings')) {
            $applyMailSettings->handle(MailSetting::current());
        }
    }
}
