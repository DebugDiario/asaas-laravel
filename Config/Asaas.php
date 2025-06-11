<?php

return [
    'api_key' => env('ASAAS_API_KEY', ''),
    
    'sandbox' => env('ASAAS_SANDBOX', true),
    
    'version' => env('ASAAS_VERSION', 'v3'),
    
    'timeout' => env('ASAAS_TIMEOUT', 30),
    
    'certificate' => env('ASAAS_CERTIFICATE_PATH', null),
    
    'urls' => [
        'sandbox' => 'https://api-sandbox.asaas.com/',
        'production' => 'https://api.asaas.com/',
    ],
];
