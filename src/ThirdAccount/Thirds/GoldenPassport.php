<?php

namespace Cann\Admin\OAuth\ThirdAccount\Thirds;

use Cann\Admin\OAuth\Helpers\ApiHelper;

class GoldenPassport extends ThirdAbstract
{
    const BASE_URL = 'https://sh-passport.wetax.com.cn';

    public function getAuthorizeUrl(array $params)
    {
        $config = config('admin-oauth.services.golden_passport');

        $paramsStr = http_build_query([
            'client_id'     => $config['client_id'],
            'redirect_uri'  => $this->getRedirectUrl(),
            'response_type' => 'code',
            'scope'         => '',
            'state'         => \Str::random(16),
        ]);

        // 返回跳转 URL
        return self::BASE_URL . '/oauth/authorize?' . $paramsStr;
    }

    public function getThirdUser(array $params)
    {
        \Validator::make($params, [
            'code' => 'required|string',
        ])->validate();

        $response = $this->getAccessToken($params['code']);

        $userInfo = $this->getUserInfo($response['access_token']);

        return [
            'id'        => $userInfo['id'],
            'name'      => $userInfo['name'],
            'full_info' => $userInfo,
        ];
    }

    protected function getAccessToken(string $code)
    {
        $config = config('admin-oauth.services.golden_passport');

        $result = ApiHelper::guzHttpRequest(self::BASE_URL . '/oauth/token', [
            'grant_type'    => 'authorization_code',
            'client_id'     => $config['client_id'],
            'client_secret' => $config['client_secret'],
            'redirect_uri'  => $this->getRedirectUrl(),
            'code'          => $code,
        ]);

        if (! isset($result['access_token'])) {
            throw new \Exception('获取高灯 AccessToken 失败');
        }

        return $result;
    }

    protected function getUserInfo(string $accessToken)
    {
        $result = ApiHelper::guzHttpRequest(self::BASE_URL . '/api/user', [], 'GET', 'JSON', [
            'Authorization' => 'Bearer ' . $accessToken,
        ]);

        if (isset($result['code']) && $result['code'] != 0) {
            throw new \Exception('获取高灯用户信息失败：' . $result['message']);
        }

        return $result;
    }
}
