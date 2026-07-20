<?php

namespace App\Settings;

use App\Models\MailSetting;

class ApplyMailSettings
{
    public function handle(MailSetting $settings): void
    {
        config([
            'mail.default' => $settings->mailer,
            'mail.mailers.smtp.scheme' => $settings->scheme,
            'mail.mailers.smtp.host' => $settings->host,
            'mail.mailers.smtp.port' => $settings->port,
            'mail.mailers.smtp.username' => $settings->username,
            'mail.mailers.smtp.password' => $settings->password,
            'mail.from.address' => $settings->from_address,
            'mail.from.name' => $settings->from_name,
        ]);
    }
}
