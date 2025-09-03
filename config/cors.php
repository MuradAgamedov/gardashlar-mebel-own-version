<?php

return [
    'paths' => ['api/', 'storage/'], // burda icazə verilən route-ları yaz
    'allowed_methods' => ['*'],        // bütün HTTP metodlara icazə
    'allowed_origins' => ['*'],        // istənilən domainə icazə (productionda dəqiq domain yazmaq yaxşıdır)
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
