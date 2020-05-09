<?php

namespace Cann\Admin\OAuth\Services;

use Cann\Admin\OAuth\Helpers\ApiHelper;

class Golden
{
    const BASE_URL = 'https://api-authorize.wetax.com.cn/api';

    // 获取用户列表
    public static function getUserList(int $page = 1, int $pageSize = 20)
    {
        return self::call('/users', 'GET', [
            'page'      => $page,
            'page_size' => $pageSize,
        ]);
    }

    protected static function call(string $uri, string $method = 'GET', array $params = [])
    {
        $params += [
            'client_id' => config('admin-oauth.services.golden.client_id'),
            'nonce_str' => \Str::random(16),
            'timestamp' => time(),
        ];

        $params['sign'] = self::buildSign($params);

        $result = ApiHelper::guzHttpRequest(self::BASE_URL . $uri, $params, $method);

        if ($result['code'] != 0) {
            throw new \Exception('Godel Error：' . $result['message']);
        }

        return $result['data'];
    }

    // 对数组里的键进行从a到z的顺序排序，若遇到相同字母，则看第二个字母，以此类推
    // 排序完成后，再把所有键值对用“&”字符连接起来
    // 参数值为“空”不参与排序
    public static function buildSign(array $params)
    {
        // 键名升序
        ksort($params);

        $strs = [];

        foreach ($params as $key => $value) {

            // 参数值为空不参与加密、签名
            if (! $value) {
                continue;
            }

            if (is_array($value)) {
                $value = json_encode($value);
            }

            $strs[] = $key . '=' . $value;
        }

        // 拼接待签名字符串
        $paramStr = implode('&', $strs);

        // 返回最终签名
        return md5(strtolower($paramStr . '&app_secret=' . config('admin-oauth.services.golden.client_secret')));
    }
}
