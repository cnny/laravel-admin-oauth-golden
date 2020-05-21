<?php

namespace Cann\Admin\OAuth\ThirdAccount\Thirds;

use Cann\Admin\OAuth\Helpers\ApiHelper;

class Golden extends ThirdAbstract
{
    const BASE_URL = 'https://sh-passport.wetax.com.cn';

    public function getAuthorizeUrl(array $params)
    {
        $paramsStr = http_build_query([
            'client_id'     => $this->config['client_id'],
            'redirect_uri'  => $this->redirectUrl,
            'response_type' => 'code',
            'scope'         => '',
            'state'         => \Str::random(16),
        ]);

        return self::BASE_URL . '/oauth/authorize?' . $paramsStr;
    }

    public function getThirdUser(array $params)
    {
        \Validator::make($params, [
            'code' => 'required|string',
        ])->validate();

        $result = $this->getAccessToken($params['code']);

        $params += [
            'access_token' => $result['access_token'],
        ];

        $userInfo = $this->getUserInfo($params);

        return [
            'id'        => $userInfo['id'],
            'name'      => $userInfo['name'],
            'full_info' => $userInfo,
        ];
    }

    private function getAccessToken(string $code)
    {
        return ApiHelper::guzHttpRequest(self::BASE_URL . '/oauth/token', [
            'grant_type'    => 'authorization_code',
            'client_id'     => $this->config['client_id'],
            'client_secret' => $this->config['client_secret'],
            'redirect_uri'  => $this->redirectUrl,
            'code'          => $code,
        ], 'POST');
    }

    private function getUserInfo(array $params)
    {
        $result = ApiHelper::guzHttpRequest(self::BASE_URL . '/api/user', [], 'GET', null, [
            'Authorization' => 'Bearer ' . $params['access_token'],
        ]);

        if ($result['code'] != 0) {
            throw new \Exception('Golden：获取用户信息失败：' . $result['message']);
        }

        return $result['data']['user'];
    }
}
