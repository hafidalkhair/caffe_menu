<?php

return [
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false), // False untuk Sandbox
    'server_key' => env('MIDTRANS_SERVER_KEY'),
    'client_key' => env('MIDTRANS_CLIENT_KEY'),
    'snap_url' => env('MIDTRANS_SNAP_URL', 'https://app.sandbox.midtrans.com/snap/v1/transactions'),
];