<?php

return [

  /*
  |--------------------------------------------------------------------------
  | Domain GUID
  |--------------------------------------------------------------------------
  |
  | Every MinistryPlatform installation will have a Domain GUID. Open the 
  | .env file in your core Laravel installation and add the MP_DOMAIN_GUID:
  | 
  | MP_DOMAIN_GUID= <your_guid_here>
  |
  */

  'guid' => env('MP_DOMAIN_GUID'),

  /*
  |--------------------------------------------------------------------------
  | WSDL
  |--------------------------------------------------------------------------
  |
  | The absolute URL to your MinistryPlatform API endpoint.
  | 
  | MP_WSDL= https://www.example.com/ministryplatformapi/api.svc?WSDL
  |
  */

  'wsdl' => env('MP_WSDL'),

  /*
  |--------------------------------------------------------------------------
  | PASSWORD
  |--------------------------------------------------------------------------
  |
  | Your API password is located below your API GUID in most web.config 
  | files.
  | 
  | MP_API_PASSWORD= abc123
  |
  */

  'password' => env('MP_API_PASSWORD'),

  /*
  |--------------------------------------------------------------------------
  | SERVER NAME
  |--------------------------------------------------------------------------
  |
  | The server name running your instance of MinistryPlatform. Do not
  | include the HTTP protocol.
  | 
  | MP_SERVER_NAME= my.example.com
  |
  */

  'name' => env('MP_SERVER_NAME')

  

  ];