# Laravel Salesforce Email Transport

[![Latest Version on Packagist](https://img.shields.io/packagist/v/bbs-lab/laravel-salesforce-email-transport.svg?style=flat-square)](https://packagist.org/packages/bbs-lab/laravel-salesforce-email-transport)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/bbs-lab/laravel-salesforce-email-transport.svg?style=flat-square)](https://packagist.org/packages/bbs-lab/laravel-salesforce-email-transport)

A [Salesforce transactionnal Email](https://developer.salesforce.com/docs/marketing/marketing-cloud/guide/sendMessageSingleRecipient.html) transport for Laravel.

## Contents

- [Installation](#installation)
- [Usage](#usage)
- [Changelog](#changelog)
- [Security](#security)
- [Contributing](#contributing)
- [Credits](#credits)
- [License](#license)

## Installation

You can install the package via composer:

``` bash
composer require bbs-lab/laravel-salesforce-email-transport
```

The package will automatically register itself.

You can publish the config-file with:

```bash
php artisan vendor:publish --provider="BbsLab\SalesforceEmailTransport\ServiceProvider" --tag="salesforce-email-transport-config"
```

This is the contents of the published config file:

```php
<?php

declare(strict_types=1);

return [

    'auth' => [
        'url' => env('SALESFORCE_EMAIL_TRANSPORT_AUTH_URL'),
        'client_id' => env('SALESFORCE_EMAIL_TRANSPORT_AUTH_CLIENT_ID'),
        'client_secret' => env('SALESFORCE_EMAIL_TRANSPORT_AUTH_CLIENT_SECRET'),
        'grant_type' => env('SALESFORCE_EMAIL_TRANSPORT_AUTH_GRANT_TYPE'),
        'resource' => env('SALESFORCE_EMAIL_TRANSPORT_AUTH_RESOURCE'),
        'cache' => [
            'enabled' => (bool)env('SALESFORCE_EMAIL_TRANSPORT_AUTH_CACHE_ENABLED', true),
            'key' => env('SALESFORCE_EMAIL_TRANSPORT_AUTH_CACHE_KEY', 'salesforce-email-transport-token'),
        ],
    ],

    'api' => [
        'url' => env('SALESFORCE_EMAIL_TRANSPORT_API_URL'),
        'definition_key' => env('SALESFORCE_EMAIL_TRANSPORT_API_DEFINITION_KEY'),
    ],

];
```

## Usage

This package provides a `salesforce` mailer [custom transport](https://laravel.com/docs/9.x/mail#custom-transports). You may add the transport in your email configuration.

```php
<?php

return [
    // ...
    
    'mailers' => [
        // ...
        
        'salesforce' => [
            'transport' => 'salesforce',
        ],    
    ],
]
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on recent changes.

## Security

If you discover any security related issues, please email paris@big-boss-studio.com instead of using the issue tracker.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Fabien Vautour](https://github.com/fvautour)
- [Mikaël Popowicz](https://github.com/mikaelpopowicz)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
