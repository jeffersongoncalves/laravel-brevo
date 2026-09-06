<?php

use Illuminate\Support\Facades\Http;
use JeffersonGoncalves\Brevo\Facades\Brevo;

it('lists senders', function () {
    Http::fake(['*/senders' => Http::response(['senders' => [['id' => 1, 'email' => 'sender@example.com']]])]);

    $result = Brevo::senders()->list();

    expect($result['senders'][0]['email'])->toBe('sender@example.com');
});

it('creates a sender', function () {
    Http::fake(['*/senders' => Http::response(['id' => 1], 201)]);

    $result = Brevo::senders()->create('Acme', 'sender@example.com');

    expect($result['id'])->toBe(1);
    Http::assertSent(fn ($request) => $request['email'] === 'sender@example.com');
});

it('requires both name and email to create a sender', function () {
    Brevo::senders()->create('', 'sender@example.com');
})->throws(InvalidArgumentException::class, 'Both "name" and "email" are required.');
