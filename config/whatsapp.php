<?php

return [
    /*
    |--------------------------------------------------------------------------
    | WhatsApp Service URL
    |--------------------------------------------------------------------------
    | The URL of the self-hosted whatsapp-web.js Node.js service.
    | Run: cd whatsapp-service && npm install && node server.js
    */
    'service_url' => env('WA_SERVICE_URL', 'http://localhost:3001'),

    /*
    |--------------------------------------------------------------------------
    | Auth Token
    |--------------------------------------------------------------------------
    | Must match the WA_TOKEN set in whatsapp-service/.env
    */
    'service_token' => env('WA_SERVICE_TOKEN', ''),
];
