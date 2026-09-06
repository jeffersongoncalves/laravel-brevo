<?php

use JeffersonGoncalves\Brevo\Brevo as BrevoManager;
use JeffersonGoncalves\Brevo\Facades\Brevo;

it('merges the default config', function () {
    expect(config('brevo.default_limit'))->toBe(50)
        ->and(config('brevo.api_url'))->toBe('https://api.brevo.com/v3');
});

it('resolves the facade to the manager singleton', function () {
    expect(Brevo::getFacadeRoot())->toBeInstanceOf(BrevoManager::class);
});

it('always resolves the same manager instance', function () {
    expect(app(BrevoManager::class))->toBe(app(BrevoManager::class));
});
