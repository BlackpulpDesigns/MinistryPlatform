This is a new library for interacting with MinistryPlatform's SOAP XML API. Several examples for the objects are listed below, and you can also view the /docs/ folder for a complete list of methods and class properties.

# Requirements

* [Carbon](https://github.com/briannesbitt/carbon)
* PHP 5.4+

# Installation

## Composer

Require the library from the command line within your project.

`composer require "blackpulp/ministryplatform"`

## One Time Setup
### Laravel 5.x

If you are using Laravel, you can take advantage of our built in Service Provider. 

Open /config/app.php and paste the following line at the bottom of your service providers array.

`'Blackpulp\MinistryPlatform\Laravel\MinistryPlatformServiceProvider'`

Publish the config file via the following artisan command.

`php artisan vendor:publish`

Open your project's .env file and add the following items along with their values.

```php
MP_DOMAIN_GUID={{domain guid}}
MP_WSDL=https://my.church.org/ministryplatformapi/api.svc?WSDL
MP_API_PASSWORD={{api password}}
MP_SERVER_NAME=my.church.org
```

### Lumen 5.x

Coming soon

#Usage

Check out the [wiki](https://github.com/BlackpulpDesigns/MinistryPlatform/wiki)!