<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'bscscan' => [
        'key' => env('BSCSCAN_API_KEY'),
        // Can be either:
        // - V1-style: https://api.bscscan.com/api
        // - V2-style: https://api.bscscan.com/v2/api (requires chainid)
        // - Etherscan V2 multichain: https://api.etherscan.io/v2/api (requires chainid and plan support)
        'base' => env('BSCSCAN_API_BASE', 'https://api.bscscan.com/api'),
        // BSC mainnet is 56 (used for V2 multichain endpoints).
        'chainid' => (int) env('BSCSCAN_CHAIN_ID', 56),
        'usdt_contract' => env('USDT_BEP20_CONTRACT', '0x55d398326f99059fF775485246999027B3197955'),
    ],

];
