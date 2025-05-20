<?php

//Shufti Pro account Secret, Client ID and URL Key specified in .env
return [
    'url' => env('SHUFTIPRO_END_POINT'),
    'client_id' => env('SHUFTIPRO_CLIENT_ID'),
    'secret_key' => env('SHUFTIPRO_SECRET_KEY'),
    'shufti_retry_minute' => env('SHUFTI_RETRY_MINUTE', 5)
];
