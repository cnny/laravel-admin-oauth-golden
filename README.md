<p align="center">Laravel-Admin-OAuth-Golden</p>

<p align="center">
  laravel-admin-oauth-Golden 是 <a href="https://laravel-admin.org/">laravel-admin</a> 的 OAuth2.0 登录扩展， 仅支持高灯授权中心OAuth登录，支持扩展其他授权方
  <img src="https://blog-1252314417.cos.ap-shanghai.myqcloud.com/1588825967295.jpg">
  <img src="https://blog-1252314417.cos.ap-shanghai.myqcloud.com/1588826006635.jpg">
</p>


### 依赖
------------
 - laravel-admin >= 1.7
 
 
### 安装
------------
```
composer require cann/laravel-admin-oauth-golden
```

### 发布资源

```
php artisan vendor:publish --provider="Cann\Admin\OAuth\ServiceProvider"
```

执行：
```
php artisan migrate
```

### 配置

path: `config/admin-oauth.php`

```
    'controller' => Cann\Admin\OAuth\Controllers\AuthController::class,

    // 是否允许账号密码登录
    'allowed_password_login' => true,

    // 当第三方登录未匹配到本地账号时，是否允许自动创建本地账号
    'allowed_auto_create_account_by_third' => false,

    // 启用的第三方登录
    'enabled_thirds' => [
        'Golden',
    ],

    // 第三方登录秘钥
    'services' => [
        'golden' => [
            'client_id'  => env('GOLDEN_CLIENT_ID', ''),
            'client_secret' => env('GOLDEN_CLIENT_SECRET', ''),
        ]
    ]
```

### 扩展授权方

继承 `Cann\Admin\OAuth\ThirdAccount\Thirds\ThirdAbstract`, 实现 `getAuthorizeUrl` & `getThirdUser`。 然后在 `AppServerProvider` 中注册：

```
    \Cann\Admin\OAuth\ServiceProvider::extend(\App\Extensions\OAuth\WechatMp::class, 'WechatMp', '微信公众号');
```

修改 `config/admin-oauth.php` 即可:

```
    // 启用的第三方登录
    'enabled_thirds' => [
        'WechatMp',
        'WorkWechat',
    ],

    // 第三方登录秘钥
    'services' => [
        'golden' => [
            'client_id'  => env('GOLDEN_CLIENT_ID', ''),
            'client_secret' => env('GOLDEN_CLIENT_SECRET', ''),
        ]
        'wechat_mp' => [
            'app_id'  => env('WECHAT_OFFICIAL_ACCOUNT_APPID', ''),   // AppID
            'secret'  => env('WECHAT_OFFICIAL_ACCOUNT_SECRET', ''),   // AppSecret
            'token'   => env('WECHAT_OFFICIAL_ACCOUNT_TOKEN', ''),    // Token
            'aes_key' => env('WECHAT_OFFICIAL_ACCOUNT_AES_KEY', ''),  // EncodingAESKey
        ],
    ],
```
