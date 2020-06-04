<?php
namespace Cann\Admin\OAuth\Commands;

use Illuminate\Console\Command;
use Cann\Admin\OAuth\Services\GoldenPassport;
use Cann\Admin\OAuth\Models\AdminUserThirdPfBind;

/**
 * 同步本地账号与高灯 Passport 的绑定关系
 */
class SyncBindRelationFromPassport extends Command
{
    protected $signature = 'golden-passport:sync-bind-relation';

    protected $description = '同步本地账号与高灯 Passport 的绑定关系';

    const GOLDEN_PLATFORM = 'GoldenPassport';

    public function handle()
    {
        $goldenUsers = collect(GoldenPassport::allUsers());

        $userModel = config('admin.database.users_model');

        $userModel::chunk(100, function ($users) use ($goldenUsers) {

            $users->each(function ($user) use ($goldenUsers) {

                // 该本地账号已绑定高灯账号
                if (AdminUserThirdPfBind::getBindRelationByUid(self::GOLDEN_PLATFORM, $user->id)) {
                    return true;
                }

                if ($goldenUser = $goldenUsers->where('username', $user->username)->first()) {

                    // 检测重复绑定
                    if ($bindRelation = AdminUserThirdPfBind::getBindRelation(self::GOLDEN_PLATFORM, $goldenUser['id'])) {
                        $this->line('高灯账号「' . $goldenUser['username'] . '」已有绑定关系');
                        return true;
                    }

                    // 创建绑定关系
                    AdminUserThirdPfBind::create([
                        'user_id'       => $user->id,
                        'platform'      => self::GOLDEN_PLATFORM,
                        'third_user_id' => $goldenUser['id'],
                    ]);

                    $this->info('本地账号「' . $user->username . '」绑定关系创建成功');
                }

                else {
                    $this->error('本地账号「' . $user->username . '」未找到匹配的高灯账号');
                }
            });
        });
    }
}
