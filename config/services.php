<?php

return [

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

    'smsc' => [
        'login' => env('SMSC_API_LOGIN'),
        'password' => env('SMSC_API_PASSWORD'),
    ],

    'cloudpayments' => [
        'id' => env('CLOUDPAYMENTS_API_ID'),
        'key' => env('CLOUDPAYMENTS_API_KEY'),
    ],

    'major_express' => [
        'api_key' => env('MAJOR_EXPRESS_API_KEY'),
    ],

];
