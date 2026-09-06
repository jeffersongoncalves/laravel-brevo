<?php

namespace JeffersonGoncalves\Brevo\Tests;

use JeffersonGoncalves\Brevo\BrevoServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            BrevoServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('brevo.api_url', 'https://api.brevo.com/v3');
        $app['config']->set('brevo.api_key', 'test-api-key');
    }
}
