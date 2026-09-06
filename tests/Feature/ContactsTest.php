<?php

use Illuminate\Support\Facades\Http;
use JeffersonGoncalves\Brevo\Exceptions\BrevoException;
use JeffersonGoncalves\Brevo\Facades\Brevo;

it('lists contacts', function () {
    Http::fake([
        '*/contacts*' => Http::response(['contacts' => [['id' => 1, 'email' => 'jane@example.com']]]),
    ]);

    $result = Brevo::contacts()->list();

    expect($result['contacts'][0]['email'])->toBe('jane@example.com');
    Http::assertSent(fn ($request) => str_contains((string) $request->url(), '/v3/contacts?')
        && $request->hasHeader('api-key', 'test-api-key'));
});

it('gets a single contact', function () {
    Http::fake(['*/contacts/jane@example.com' => Http::response(['id' => 1, 'email' => 'jane@example.com'])]);

    $result = Brevo::contacts()->get('jane@example.com');

    expect($result['email'])->toBe('jane@example.com');
});

it('creates a contact', function () {
    Http::fake(['*/contacts' => Http::response(['id' => 1], 201)]);

    $result = Brevo::contacts()->create(['email' => 'jane@example.com']);

    expect($result['id'])->toBe(1);
    Http::assertSent(fn ($request) => $request['email'] === 'jane@example.com');
});

it('requires an email to create a contact', function () {
    Brevo::contacts()->create([]);
})->throws(InvalidArgumentException::class, 'The "email" attribute is required.');

it('updates a contact', function () {
    Http::fake(['*/contacts/1' => Http::response([])]);

    Brevo::contacts()->update(1, ['attributes' => ['FIRSTNAME' => 'Jane']]);

    Http::assertSent(fn ($request) => $request->method() === 'PUT');
});

it('deletes a contact', function () {
    Http::fake(['*/contacts/1' => Http::response([])]);

    Brevo::contacts()->delete(1);

    Http::assertSent(fn ($request) => $request->method() === 'DELETE');
});

it('imports contacts in bulk', function () {
    Http::fake(['*/contacts/import' => Http::response(['processId' => 42], 202)]);

    $result = Brevo::contacts()->import([['email' => 'jane@example.com']], [3]);

    expect($result['processId'])->toBe(42);
    Http::assertSent(fn ($request) => $request['listIds'] === [3]);
});

it('throws a BrevoException on a failed request', function () {
    Http::fake(['*/contacts/1' => Http::response(['message' => 'Contact not found'], 404)]);

    Brevo::contacts()->get(1);
})->throws(BrevoException::class, 'Contact not found');
