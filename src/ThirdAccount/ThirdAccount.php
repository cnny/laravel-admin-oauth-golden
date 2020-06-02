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
        'GoldenPassport',
    ];

    /**
     * 平台类型定义
     *
     * @var array
     */
    const SOURCES = [
        'GoldenPassport' => '高灯创新-通行证',
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
                'class'      => __NAMESPACE__ . '\\Thirds\\' . $source,
            ];
        }

        return $sources;
    }
}
