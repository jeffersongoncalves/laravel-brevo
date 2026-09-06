<?php

use Illuminate\Support\Facades\Http;
use JeffersonGoncalves\Brevo\Facades\Brevo;

it('lists sms campaigns', function () {
    Http::fake(['*/smsCampaigns*' => Http::response(['campaigns' => [['id' => 1, 'name' => 'Flash Sale']]])]);

    $result = Brevo::smsCampaigns()->list(['status' => 'sent']);

    expect($result['campaigns'][0]['name'])->toBe('Flash Sale');
    Http::assertSent(fn ($request) => str_contains((string) $request->url(), 'status=sent'));
});
