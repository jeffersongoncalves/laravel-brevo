<?php

use Illuminate\Support\Facades\Http;
use JeffersonGoncalves\Brevo\Facades\Brevo;

it('lists all lists', function () {
    Http::fake(['*/contacts/lists*' => Http::response(['lists' => [['id' => 1, 'name' => 'Newsletter']]])]);

    $result = Brevo::lists()->list();

    expect($result['lists'][0]['name'])->toBe('Newsletter');
});

it('gets a single list', function () {
    Http::fake(['*/contacts/lists/1' => Http::response(['id' => 1, 'name' => 'Newsletter'])]);

    $result = Brevo::lists()->get(1);

    expect($result['name'])->toBe('Newsletter');
});

it('creates a list', function () {
    Http::fake(['*/contacts/lists' => Http::response(['id' => 1], 201)]);

    $result = Brevo::lists()->create('Newsletter', 2);

    expect($result['id'])->toBe(1);
    Http::assertSent(fn ($request) => $request['name'] === 'Newsletter' && $request['folderId'] === 2);
});

it('requires a name to create a list', function () {
    Brevo::lists()->create('', 2);
})->throws(InvalidArgumentException::class, 'The "name" attribute is required.');

it('updates a list', function () {
    Http::fake(['*/contacts/lists/1' => Http::response([])]);

    Brevo::lists()->update(1, 'Renamed');

    Http::assertSent(fn ($request) => $request->method() === 'PUT' && $request['name'] === 'Renamed');
});

it('deletes a list', function () {
    Http::fake(['*/contacts/lists/1' => Http::response([])]);

    Brevo::lists()->delete(1);

    Http::assertSent(fn ($request) => $request->method() === 'DELETE');
});

it('gets the contacts of a list', function () {
    Http::fake(['*/contacts/lists/1/contacts*' => Http::response(['contacts' => [['email' => 'jane@example.com']]])]);

    $result = Brevo::lists()->contacts(1);

    expect($result['contacts'][0]['email'])->toBe('jane@example.com');
});

it('adds contacts to a list', function () {
    Http::fake(['*/contacts/lists/1/contacts/add' => Http::response(['success' => ['jane@example.com']])]);

    Brevo::lists()->addContacts(1, ['jane@example.com']);

    Http::assertSent(fn ($request) => $request['emails'] === ['jane@example.com']);
});

it('removes contacts from a list', function () {
    Http::fake(['*/contacts/lists/1/contacts/remove' => Http::response(['success' => ['jane@example.com']])]);

    Brevo::lists()->removeContacts(1, ['jane@example.com']);

    Http::assertSent(fn ($request) => $request['emails'] === ['jane@example.com']);
});
