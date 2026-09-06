<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Brevo API URL
    |--------------------------------------------------------------------------
    */
    'api_url' => env('BREVO_API_URL', 'https://api.brevo.com/v3'),

    /*
    |--------------------------------------------------------------------------
    | Brevo API Key
    |--------------------------------------------------------------------------
    |
    | Find it under SMTP & API > API Keys in your Brevo account.
    |
    */
    'api_key' => env('BREVO_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Default Pagination Limit
    |--------------------------------------------------------------------------
    |
    | Used as the default "limit" for list endpoints when none is given.
    |
    */
    'default_limit' => env('BREVO_DEFAULT_LIMIT', 50),

];
