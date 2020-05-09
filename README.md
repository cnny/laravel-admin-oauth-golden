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

### 加载路由

app\Admin\routes.php 中 用`GoldenAdmin::routes()` 替换 laravel-admin 默认的 `Admin::routes()`

### 配置

path: `config/admin-oauth.php`

```
    'controller' => Cann\Admin\OAuth\Controllers\AuthController::class,

    // 是否允许账号密码登录
    'allowed_password_login' => true,

    // 当第三方登录未匹配到本地账号时，是否允许自动创建本地账号
    'allowed_auto_create_account_by_third' => false,

    // 默认密码
    'default_password' => '',
    
    // 秘钥
    'services' => [
        'golden' => [
            'client_id'     => env('GOLDEN_CLIENT_ID'),
            'client_secret' => env('GOLDEN_CLIENT_SECRET'),
        ],
    ],
```

