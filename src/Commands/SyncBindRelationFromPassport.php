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

    public function handle()
    {
        $goldenUsers = collect(GoldenPassport::allUsers());

        $userModel = config('admin.database.users_model');

        $userModel::chunk(100, function ($users) use ($goldenUsers) {

            $users->each(function ($user) use ($goldenUsers) {

                if ($goldenUser = $goldenUsers->where('username', $user->username)->first()) {

                    // 创建绑定关系
                    AdminUserThirdPfBind::firstOrCreate([
                        'user_id'       => $user->id,
                        'platform'      => 'GoldenPassport',
                        'third_user_id' => $goldenUser['id'],
                    ]);
                }
            });
        });
    }
}
