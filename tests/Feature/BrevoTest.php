<?php

use Illuminate\Support\Facades\Http;
use JeffersonGoncalves\Brevo\Facades\Brevo;

it('gets the account', function () {
    Http::fake(['*/account' => Http::response(['email' => 'me@example.com'])]);

    $result = Brevo::account();

    expect($result['email'])->toBe('me@example.com');
    Http::assertSent(fn ($request) => $request->hasHeader('api-key', 'test-api-key'));
});

it('sends a transactional email', function () {
    Http::fake(['*/smtp/email' => Http::response(['messageId' => 'abc123'], 201)]);

    $result = Brevo::sendEmail(
        senderEmail: 'sender@example.com',
        senderName: 'Sender',
        to: [['email' => 'jane@example.com', 'name' => 'Jane']],
        subject: 'Hello',
        htmlContent: '<p>Hi</p>',
    );

    expect($result['messageId'])->toBe('abc123');
    Http::assertSent(fn ($request) => $request['sender']['email'] === 'sender@example.com'
        && $request['subject'] === 'Hello'
        && ! isset($request['textContent']));
});

it('requires htmlContent or textContent to send an email', function () {
    Brevo::sendEmail(
        senderEmail: 'sender@example.com',
        senderName: 'Sender',
        to: [['email' => 'jane@example.com']],
        subject: 'Hello',
    );
})->throws(InvalidArgumentException::class, 'Either "htmlContent" or "textContent" is required.');

it('sends a transactional sms', function () {
    Http::fake(['*/transactionalSMS/sms' => Http::response(['messageId' => 'sms123'])]);

    $result = Brevo::sendSms(sender: 'MyBrand', recipient: '+15555550100', content: 'Hi there');

    expect($result['messageId'])->toBe('sms123');
    Http::assertSent(fn ($request) => $request['type'] === 'transactional');
});
