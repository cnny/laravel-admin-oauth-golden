<?php

return [

    'controller' => Cann\Admin\OAuth\Controllers\AuthController::class,

    // 是否允许账号密码登录
    'allowed_password_login' => false,

    // 当第三方登录未匹配到本地账号时，是否允许自动创建本地账号
    'allowed_auto_create_account_by_third' => true,

    // 启用的第三方登录
    'enabled_thirds' => [
        'Golden',
    ],

    // 第三方登录秘钥
    'services' => [
        'golden' => [
            'client_id'     => env('GOLDEN_CLIENT_ID'),
            'client_secret' => env('GOLDEN_CLIENT_SECRET'),
        ],
    ],
];
