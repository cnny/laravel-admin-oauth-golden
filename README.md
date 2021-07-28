# Laravel-Admin 接入「高灯科技·数据中台」通行证

### 安装

```
composer require cann/laravel-admin-oauth-golden:dev-master
```

### 发布资源

```
php artisan vendor:publish --force --provider="Cann\Admin\OAuth\AdminOAuthServiceProvider"
```

### 数据库迁移

```
php artisan migrate
```

### 参数配置

修改应用的 `.env` 文件，增加以下两个参数：

```
ADMIN_GOLDEN_PASSPORT_CLIENT_ID=
ADMIN_GOLDEN_PASSPORT_CLIENT_SECRET=
```

更多配置请参考 `config/admin-oauth.php` 文件。

### 同步本地账号与高灯 Passport 的绑定关系

```
php artisan golden-passport:sync-bind-relation
```
