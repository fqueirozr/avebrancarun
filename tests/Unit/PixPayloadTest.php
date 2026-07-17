<?php

use App\Support\PixPayload;

test('it generates a pix payload with key amount transaction and valid crc', function () {
    $payload = (new PixPayload)->generate(
        key: 'financeiro@example.com',
        amount: 25,
        receiverName: 'Associação Ave Branca',
        receiverCity: 'São Paulo',
        transactionId: 'AVR-123456',
    );

    expect($payload)->toBe(
        '00020126440014br.gov.bcb.pix0122financeiro@example.com520400005303986540525.005802BR5921ASSOCIACAO AVE BRANCA6009SAO PAULO62130509AVR1234566304E470',
    );

    expect((new PixPayload)->qrCodeDataUri($payload))
        ->toStartWith('data:image/svg+xml;base64,');
});
