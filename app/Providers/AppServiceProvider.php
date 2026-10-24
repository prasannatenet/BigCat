<?php

namespace App\Providers;

use App\Models\MailSetting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (! Schema::hasTable('mail_settings')) {
            return;
        }

        $mailSettings = MailSetting::query()->first();
        if ($mailSettings === null) {
            return;
        }

        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp' => [
                'transport' => 'smtp',
                'scheme' => $mailSettings->encryption === 'ssl' ? 'smtps' : 'smtp',
                'auto_tls' => $mailSettings->encryption === 'tls',
                'host' => $mailSettings->host,
                'port' => $mailSettings->port,
                'username' => $mailSettings->username,
                'password' => $mailSettings->password,
                'timeout' => 15,
                'local_domain' => parse_url((string) config('app.url'), PHP_URL_HOST),
            ],
            'mail.from' => [
                'address' => $mailSettings->from_email,
                'name' => $mailSettings->from_name,
            ],
        ]);
    }
}
