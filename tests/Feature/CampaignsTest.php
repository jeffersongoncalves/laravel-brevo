<?php

use Illuminate\Support\Facades\Http;
use JeffersonGoncalves\Brevo\Facades\Brevo;

it('lists email campaigns', function () {
    Http::fake(['*/emailCampaigns*' => Http::response(['campaigns' => [['id' => 1, 'name' => 'Promo']]])]);

    $result = Brevo::campaigns()->list(['status' => 'sent']);

    expect($result['campaigns'][0]['name'])->toBe('Promo');
});

it('gets a single email campaign', function () {
    Http::fake(['*/emailCampaigns/1' => Http::response(['id' => 1, 'name' => 'Promo'])]);

    $result = Brevo::campaigns()->get(1);

    expect($result['name'])->toBe('Promo');
});

it('creates an email campaign', function () {
    Http::fake(['*/emailCampaigns' => Http::response(['id' => 1], 201)]);

    $result = Brevo::campaigns()->create(
        name: 'Promo',
        sender: ['name' => 'Acme', 'email' => 'sender@example.com'],
        subject: 'Big Sale',
        htmlContent: '<p>Hi</p>',
        listIds: [2, 3],
    );

    expect($result['id'])->toBe(1);
    Http::assertSent(fn ($request) => $request['recipients']['listIds'] === [2, 3]);
});

it('requires a name to create an email campaign', function () {
    Brevo::campaigns()->create(
        name: '',
        sender: ['name' => 'Acme', 'email' => 'sender@example.com'],
        subject: 'Big Sale',
        htmlContent: '<p>Hi</p>',
        listIds: [2],
    );
})->throws(InvalidArgumentException::class, 'The "name" attribute is required.');

it('updates an email campaign', function () {
    Http::fake(['*/emailCampaigns/1' => Http::response([])]);

    Brevo::campaigns()->update(1, ['subject' => 'Updated']);

    Http::assertSent(fn ($request) => $request->method() === 'PUT');
});

it('deletes an email campaign', function () {
    Http::fake(['*/emailCampaigns/1' => Http::response([])]);

    Brevo::campaigns()->delete(1);

    Http::assertSent(fn ($request) => $request->method() === 'DELETE');
});

it('sends an email campaign now', function () {
    Http::fake(['*/emailCampaigns/1/sendNow' => Http::response([])]);

    Brevo::campaigns()->sendNow(1);

    Http::assertSent(fn ($request) => $request->method() === 'POST');
});

it('sends a test email campaign', function () {
    Http::fake(['*/emailCampaigns/1/sendTest' => Http::response([])]);

    Brevo::campaigns()->sendTest(1, ['jane@example.com']);

    Http::assertSent(fn ($request) => $request['emailTo'] === ['jane@example.com']);
});
