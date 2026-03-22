<?php

return [

    'wsdl_cache_key_prefix'=>[
        'value' => 'soap_wsdl_file_'
    ],
    'wsdl_cache_ttl' => [
        'value' => 600,

    ],
    'credentials' => [
        'value' => [
            'login' => env('NAV_SOAP_LOGIN'),
            'password' => env('NAV_SOAP_PASSWORD'),
        ],
    ],
    'base_url' => [
        'value' => [
            'zao' => env('ZAO_NAV_SOAP_BASE_URL'),
            'nano' => env('NANO_NAV_SOAP_BASE_URL'),
            'alabuga' => env('ALABUGA_NAV_SOAP_BASE_URL'),
            'danafilms' => env('DANAFILMS_NAV_SOAP_BASE_URL'),
        ],
    ],

];
