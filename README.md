This is a new library for interacting with MinistryPlatform's SOAP XML API. Several examples for the objects are listed below, and you can also view the /docs/ folder for a complete list of methods and class properties.

# Recent Updates

0.10.x
- Added requirement for Blackpulp getUserRoles stored procedure (see MP Database below)

0.9.x
- Added preliminary support for multi-tenant applications.

0.8.x 
- Fixed inconsistent data delivery from Stored Procedures
- Fixed type casting when processing Stored Procedure results
- Fixed some namespacing issues with some classes
- Corrected some bad programming practices

# Requirements

* [Carbon](https://github.com/briannesbitt/carbon)
* PHP 5.4+

# Installation

## Composer

Require the library from the command line within your project.

`composer require "blackpulp/ministryplatform"`

## MP Database 

There are several stored procedures available in this package that will provide subtle yet
helpful enhancements to the library. These can be found in `src/stored_procedures` and 
should be run on your MP server. The only stored procedure *required* by this library
is `api_blackpulp_GetUserRoles.sql`. The rest are optional. This requirement is to
ensure that a User's Security Roles and the related access to CoreTools will 
always be in scope. The MinistryPlatform version of this Stored Proc 
currently only returns Security Role IDs.

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

If you are writing a multi-tenant application, there is preliminary support. However, 
documentation is still forthcoming on that configuration. 

### Lumen 5.x

Coming ~~soon~~ maybe?

#Usage

Check out the [wiki](https://github.com/BlackpulpDesigns/MinistryPlatform/wiki)!