<?php

namespace Cann\Admin\OAuth\ThirdAccount;

use Cann\Admin\OAuth\Models\AdminUserThirdPfBind;

/**
 * 第三方通行证-模型工厂
 *
 * @author Cann
 */

class ThirdAccount
{
    // 所有第三方平台
    const PLATFORMS = [
        'Golden',
    ];

    /**
     * 平台类型定义
     *
     * @var array
     */
    const SOURCES = [
        'Golden' => '高灯',
    ];

    /**
     * 实例工厂
     *
     * @param string $source
     */
    public static function factory(string $source)
    {
        $source = ucfirst($source);

        $sources = self::sources();

        if (! $source || ! isset($sources[$source])) {
            throw new \Exception('Invalid 3rdAccountSource');
        }

        return new $sources[$source]['class']($source);
    }

    public static function sources()
    {
        $sources = [];

        foreach (self::SOURCES as $source => $sourceName) {
            $sources[$source] = [
                'source'     => $source,
                'sourceName' => $sourceName,
                'class'      => __NAMESPACE__ . '\\Thirds\\' . str_replace('_', '\\', $source),
            ];
        }

        return $sources;
    }

    // 解绑绑定
    public static function unbind($user, string $platform)
    {
        if (! in_array($platform, self::PLATFORMS)) {
            throw new \Exception('Invalid 3rdAccountPlatform');
        }

        AdminUserThirdPfBind::where([
            'platform'  => $platform,
            'uid'       => $user->id,
        ])->delete();
    }

    // 解绑所有第三方绑定
    public static function unbindAll($user)
    {
        AdminUserThirdPfBind::where([
            'uid' => $user->id,
        ])->delete();
    }
}
