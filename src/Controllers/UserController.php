<?php

namespace Cann\Admin\OAuth\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Cann\Admin\OAuth\Services\GoldenPassport;
use Cann\Admin\OAuth\Models\AdminUserThirdPfBind;
use Encore\Admin\Controllers\UserController as BaseUserController;
use Encore\Admin\Form;

class UserController extends BaseUserController
{
    const GOLDEN_PLATFORM = 'GoldenPassport';

    public function form()
    {
        $userModel       = config('admin.database.users_model');
        $permissionModel = config('admin.database.permissions_model');
        $roleModel       = config('admin.database.roles_model');

        $form = new Form(new $userModel());

        $form->display('id', 'ID');

        $goldenUsers = self::fetchGoldenPassportUids();

        if ($form->isCreating()) {
            $form->select('golden_uid', '高灯账号')
                ->options($goldenUsers->pluck('name', 'id'))
                ->rules('required');
            $form->hidden('name');
            $form->hidden('username');
            $form->hidden('avatar');
            $form->hidden('password');
            $form->ignore(['golden_uid']);
        }
        else {
            $form->display('name', trans('admin.name'));
        }

        $form->multipleSelect('roles', trans('admin.roles'))
            ->options($roleModel::all()->pluck('name', 'id'));

        $form->multipleSelect('permissions', trans('admin.permissions'))
            ->options($permissionModel::all()->pluck('name', 'id'));

        $form->display('created_at', trans('admin.created_at'));

        $form->saving(function (Form $form) use ($goldenUsers) {

            if ($goldenUid = request('golden_uid')) {

                // 检测重复绑定
                if (AdminUserThirdPfBind::getBindRelation(self::GOLDEN_PLATFORM, $goldenUid)) {
                    return back()->withInput()->withErrors(new MessageBag([
                        'golden_uid' => '该高灯账号已被其他账号绑定',
                    ]));
                }

                // 同步用户资料
                $form->name     = $goldenUsers[$goldenUid]['name'];
                $form->username = $goldenUsers[$goldenUid]['username'];
                $form->avatar   = $goldenUsers[$goldenUid]['avatar_url'];
                $form->password = '';
            }

        });

        $form->saved(function (Form $form) {

            // 创建绑定关系
            if ($goldenUid = request('golden_uid')) {
                AdminUserThirdPfBind::create([
                    'user_id'       => $form->model()->id,
                    'platform'      => self::GOLDEN_PLATFORM,
                    'third_user_id' => request('golden_uid'),
                ]);
            }

        });

        return $form;
    }

    // 获取所有高灯员工列表
    protected static function fetchGoldenPassportUids()
    {
        $users = GoldenPassport::allUsers();

        $goldenUsers = collect($goldenUsers)->keyBy('id');

        $bindedThirdUids = AdminUserThirdPfBind::getBindUids(self::GOLDEN_PLATFORM);

        // 去除已绑定的第三方账号
        foreach ($bindedThirdUids as $thirdUid) {
            unset($goldenUsers[$thirdUid]);
        }

        return $goldenUsers;
    }
}
