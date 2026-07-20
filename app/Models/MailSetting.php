<?php

namespace App\Models;

use Database\Factories\MailSettingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'mailer',
    'scheme',
    'host',
    'port',
    'username',
    'password',
    'from_address',
    'from_name',
])]
class MailSetting extends Model
{
    /** @use HasFactory<MailSettingFactory> */
    use HasFactory;

    protected $attributes = [
        'mailer' => 'log',
        'host' => '127.0.0.1',
        'port' => 2525,
        'from_address' => 'hello@example.com',
    ];

    protected $hidden = [
        'password',
    ];

    public static function current(): self
    {
        return self::query()->first() ?? new self([
            'from_name' => config('app.name'),
        ]);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'port' => 'integer',
            'password' => 'encrypted',
        ];
    }
}
