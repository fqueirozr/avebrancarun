<?php

namespace App\Support;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use InvalidArgumentException;

class PixPayload
{
    public function generate(string $key, float $amount, string $receiverName, string $receiverCity, string $transactionId): string
    {
        if (blank($key) || $amount <= 0) {
            throw new InvalidArgumentException('A chave Pix e um valor positivo são obrigatórios.');
        }

        $merchantAccount = $this->field('00', 'br.gov.bcb.pix').$this->field('01', trim($key));
        $additionalData = $this->field('05', $this->sanitize($transactionId, 25, '***'));
        $payload = $this->field('00', '01')
            .$this->field('26', $merchantAccount)
            .$this->field('52', '0000')
            .$this->field('53', '986')
            .$this->field('54', number_format($amount, 2, '.', ''))
            .$this->field('58', 'BR')
            .$this->field('59', $this->sanitize($receiverName, 25, 'RECEBEDOR'))
            .$this->field('60', $this->sanitize($receiverCity, 15, 'BRASIL'))
            .$this->field('62', $additionalData)
            .'6304';

        return $payload.$this->crc16($payload);
    }

    public function qrCodeDataUri(string $payload): string
    {
        $svg = (new QRCode(new QROptions([
            'outputType' => 'svg',
            'outputBase64' => false,
            'svgConnectPaths' => true,
        ])))->render($payload);

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }

    private function field(string $id, string $value): string
    {
        $length = strlen($value);

        if ($length > 99) {
            throw new InvalidArgumentException("O campo Pix {$id} excede 99 caracteres.");
        }

        return $id.str_pad((string) $length, 2, '0', STR_PAD_LEFT).$value;
    }

    private function sanitize(string $value, int $maximumLength, string $fallback): string
    {
        $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value) ?: '';
        $sanitized = preg_replace('/[^A-Za-z0-9 ]/', '', $ascii) ?: '';
        $sanitized = trim(preg_replace('/\s+/', ' ', $sanitized) ?: '');

        return mb_strtoupper(mb_substr($sanitized ?: $fallback, 0, $maximumLength));
    }

    private function crc16(string $payload): string
    {
        $crc = 0xFFFF;

        foreach (str_split($payload) as $character) {
            $crc ^= ord($character) << 8;

            for ($bit = 0; $bit < 8; $bit++) {
                $crc = ($crc & 0x8000) !== 0
                    ? (($crc << 1) ^ 0x1021) & 0xFFFF
                    : ($crc << 1) & 0xFFFF;
            }
        }

        return strtoupper(str_pad(dechex($crc), 4, '0', STR_PAD_LEFT));
    }
}
