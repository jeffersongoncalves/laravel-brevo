<?php

namespace JeffersonGoncalves\Brevo\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \JeffersonGoncalves\Brevo\Brevo
 */
class Brevo extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \JeffersonGoncalves\Brevo\Brevo::class;
    }
}
