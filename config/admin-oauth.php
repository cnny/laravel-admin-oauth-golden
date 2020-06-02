<?php

return [

    'controller' => Cann\Admin\OAuth\Controllers\ThirdAccountController::class,

    // 当第三方登录未匹配到本地账号时，是否允许自动创建本地账号
    'allowed_auto_create_account' => true,

    // OAuth 秘钥
    'services' => [
        'golden_passport' => [
            'client_id'     => env('GOLDEN_CLIENT_ID'),
            'client_secret' => env('GOLDEN_CLIENT_SECRET'),
        ],
    ],
];
