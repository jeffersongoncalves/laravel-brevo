<div class="filament-hidden">

![Laravel Brevo](https://raw.githubusercontent.com/jeffersongoncalves/laravel-brevo/main/art/jeffersongoncalves-laravel-brevo.png)

</div>

# Laravel Brevo

[![Latest Version on Packagist](https://img.shields.io/packagist/v/jeffersongoncalves/laravel-brevo.svg?style=flat-square)](https://packagist.org/packages/jeffersongoncalves/laravel-brevo)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/jeffersongoncalves/laravel-brevo/tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/jeffersongoncalves/laravel-brevo/actions?query=workflow%3ATests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/jeffersongoncalves/laravel-brevo/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/jeffersongoncalves/laravel-brevo/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/jeffersongoncalves/laravel-brevo.svg?style=flat-square)](https://packagist.org/packages/jeffersongoncalves/laravel-brevo)
[![License](https://img.shields.io/packagist/l/jeffersongoncalves/laravel-brevo.svg?style=flat-square)](LICENSE.md)

A PHP/Laravel client for the [Brevo](https://www.brevo.com/) (formerly Sendinblue) REST API v3. Covers account, contacts, lists, transactional email/SMS, email & SMS campaigns and senders through a simple, typed API built on Laravel's `Http` client.

## Features

- Account: get the authenticated account's details
- Contacts: list, get, create, update, delete, bulk import
- Lists: list, get, create, update, delete, list a list's contacts, add/remove contacts
- Transactional email: send via `/smtp/email`
- Transactional SMS: send via `/transactionalSMS/sms`
- Email campaigns: list, get, create, update, delete, send now, send a test
- SMS campaigns: list (filter by status)
- Senders: list, create
- Throws `BrevoException` (with the original API error body) on any non-2xx response
- Throws `InvalidArgumentException` before hitting the API when a required field is missing

## Installation

You can install the package via composer:

```bash
composer require jeffersongoncalves/laravel-brevo
```

Publish the config file:

```bash
php artisan vendor:publish --tag=brevo-config
```

Set your Brevo API key in `.env`:

```env
BREVO_API_KEY=your-api-key
```

Find it under **SMTP & API > API Keys** in your Brevo account.

## Configuration

```php
// config/brevo.php
return [
    'api_url' => env('BREVO_API_URL', 'https://api.brevo.com/v3'),
    'api_key' => env('BREVO_API_KEY', ''),
    'default_limit' => env('BREVO_DEFAULT_LIMIT', 50),
];
```

## Usage

The package is resolved via the `Brevo` facade or by injecting `JeffersonGoncalves\Brevo\Brevo`. Each resource is exposed as a method returning a dedicated resource class; the account and transactional email/SMS endpoints are exposed directly on the manager.

### Account

```php
use JeffersonGoncalves\Brevo\Facades\Brevo;

$account = Brevo::account();
```

### Contacts

```php
// List (supports Brevo's contacts filters plus limit/offset)
$contacts = Brevo::contacts()->list(['limit' => 20]);

$contact = Brevo::contacts()->get('jane@example.com'); // by id or email

$contact = Brevo::contacts()->create([
    'email' => 'jane@example.com',
    'attributes' => ['FIRSTNAME' => 'Jane', 'LASTNAME' => 'Doe'],
    'listIds' => [2, 3],
]);

Brevo::contacts()->update('jane@example.com', [
    'attributes' => ['FIRSTNAME' => 'Janet'],
    'listIds' => [4],
    'unlinkListIds' => [3],
]);

Brevo::contacts()->delete('jane@example.com');

// Bulk import
Brevo::contacts()->import([
    ['email' => 'jane@example.com'],
    ['email' => 'john@example.com'],
], listIds: [2]);
```

### Lists

```php
$lists = Brevo::lists()->list();

$list = Brevo::lists()->create('Newsletter', folderId: 1);

Brevo::lists()->update($list['id'], 'Renamed Newsletter');

$listContacts = Brevo::lists()->contacts($list['id']);

Brevo::lists()->addContacts($list['id'], ['jane@example.com']);
Brevo::lists()->removeContacts($list['id'], ['jane@example.com']);

Brevo::lists()->delete($list['id']);
```

### Transactional email and SMS

```php
Brevo::sendEmail(
    senderEmail: 'sender@example.com',
    senderName: 'Acme',
    to: [['email' => 'jane@example.com', 'name' => 'Jane']],
    subject: 'Welcome!',
    htmlContent: '<p>Hi Jane</p>',
    tags: ['welcome'],
);

Brevo::sendSms(
    sender: 'Acme',
    recipient: '+15555550100',
    content: 'Your code is 123456',
);
```

### Email campaigns

```php
$campaigns = Brevo::campaigns()->list(['status' => 'sent']);

$campaign = Brevo::campaigns()->create(
    name: 'Summer Sale',
    sender: ['name' => 'Acme', 'email' => 'sender@example.com'],
    subject: 'Big Sale',
    htmlContent: '<p>Save 20%</p>',
    listIds: [2, 3],
);

Brevo::campaigns()->update($campaign['id'], ['subject' => 'Updated subject']);

Brevo::campaigns()->sendTest($campaign['id'], ['jane@example.com']);
Brevo::campaigns()->sendNow($campaign['id']);

Brevo::campaigns()->delete($campaign['id']);
```

### SMS campaigns and senders

```php
Brevo::smsCampaigns()->list(['status' => 'sent']);

Brevo::senders()->list();
Brevo::senders()->create('Acme', 'sender@example.com');
```

### Error handling

Any non-2xx API response throws `JeffersonGoncalves\Brevo\Exceptions\BrevoException`, which exposes the decoded error body:

```php
use JeffersonGoncalves\Brevo\Exceptions\BrevoException;

try {
    Brevo::contacts()->get('missing@example.com');
} catch (BrevoException $e) {
    logger()->error($e->getMessage(), $e->errorBody());
}
```

Missing required fields (e.g. `email` on `contacts()->create()`, `name` on `lists()->create()`) throw `InvalidArgumentException` before any HTTP call is made.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Jefferson Gonçalves](https://github.com/jeffersongoncalves)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
