<?php

namespace Cann\Admin\OAuth\Services;

use Cann\Admin\OAuth\Helpers\ApiHelper;

class GoldenPassport
{
    const BASE_URL = 'https://sh-passport.wetax.com.cn';

    // 获取用户列表
    public static function getUserList(int $page = 1, int $pageSize = 20)
    {
        $result = self::request('/api/users', 'GET', [
            'page'      => $page,
            'page_size' => $pageSize,
        ]);

        return $result['users']['data'];
    }

    protected static function request(string $uri, string $method = 'POST', array $params = [])
    {
        $result = ApiHelper::guzHttpRequest(self::BASE_URL . $uri, $params, $method, null, [
            'Authorization' => 'Bearer ' . self::getAccessToken(),
        ]);

        if ($result['code'] != 0) {
            throw new \Exception('GoldenPassport Error：' . $result['message']);
        }

        return $result['data'];
    }

    protected static function getAccessToken()
    {
        $cacheKey = 'GoldenPassport:Client:AccessToken';

        if ($accessToken = \Cache::get($cacheKey)) {
            return $accessToken;
        }

        $result = ApiHelper::guzHttpRequest(self::BASE_URL . '/oauth/token', [
            'grant_type'    => 'client_credentials',
            'client_id'     => config('admin-oauth.services.golden.client_id'),
            'client_secret' => config('admin-oauth.services.golden.client_secret'),
        ]);

        \Cache::put($cacheKey, $result['access_token'], $result['expires_in']);

        return $result['access_token'];
    }
}
