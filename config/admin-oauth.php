<?php

return [

    // 当第三方登录未匹配到本地账号时，是否允许自动创建本地账号
    'allowed_auto_create_account' => true,

    // OAuth 秘钥
    'services' => [
        'golden_passport' => [
            'client_id'     => env('ADMIN_GOLDEN_PASSPORT_CLIENT_ID'),
            'client_secret' => env('ADMIN_GOLDEN_PASSPORT_CLIENT_SECRET'),
        ],
    ],

];
