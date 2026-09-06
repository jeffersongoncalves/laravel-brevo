<?php

namespace JeffersonGoncalves\Brevo;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class BrevoServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('brevo')
            ->hasConfigFile();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(Brevo::class, function () {
            return new Brevo(
                (string) config('brevo.api_url'),
                (string) config('brevo.api_key'),
                (int) config('brevo.default_limit', 50),
            );
        });
    }
}
