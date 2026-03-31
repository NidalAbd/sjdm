<?php

return [
    'smmcpan' => [
        'url' => env('SMMP_URL', 'https://smmcpan.com/api/v2'),
        'key' => env('SMMP_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    /*
    |--------------------------------------------------------------------------
    | SEO Services
    |--------------------------------------------------------------------------
    */

    'openai' => [
        'key' => env('OPENAI_API_KEY'),
    ],

    'google_search_console' => [
        'site_url' => env('GSC_SITE_URL', 'https://smmjd.com'),
        'credentials_path' => env('GSC_CREDENTIALS_PATH'),
    ],

    'serpapi' => [
        'key' => env('SERPAPI_KEY'),
    ],

    'google_trends' => [
        'enabled' => env('GOOGLE_TRENDS_ENABLED', true),
    ],

];
