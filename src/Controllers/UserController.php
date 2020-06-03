<?php

namespace Cann\Admin\OAuth\Controllers;

use Illuminate\Http\Request;
use Cann\Admin\OAuth\Services\GoldenPassport;
use Cann\Admin\OAuth\Models\AdminUserThirdPfBind;
use Encore\Admin\Form;
use Encore\Admin\Controllers\UserController as BaseUserController;

class UserController extends BaseUserController
{
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
        }
        else {
            $form->display('name', trans('admin.name'));
        }

        $form->multipleSelect('roles', trans('admin.roles'))
            ->options($roleModel::all()->pluck('name', 'id'));

        $form->multipleSelect('permissions', trans('admin.permissions'))
            ->options($permissionModel::all()->pluck('name', 'id'));

        $form->display('created_at', trans('admin.created_at'));
        $form->display('updated_at', trans('admin.updated_at'));

        $form->hidden('name');
        $form->hidden('username');
        $form->hidden('avatar');
        $form->hidden('password');

        $form->ignore(['golden_uid']);

        $form->saving(function (Form $form) use ($goldenUsers) {
            $goldenUid = request('golden_uid');
            $form->name     = $goldenUsers[$goldenUid]['name'];
            $form->username = $goldenUsers[$goldenUid]['username'];
            $form->avatar   = $goldenUsers[$goldenUid]['avatar_url'];
            $form->password = '';
        });

        $form->saved(function (Form $form) {

            // 创建绑定关系
            AdminUserThirdPfBind::create([
                'user_id'       => $form->model()->id,
                'platform'      => 'GoldenPassport',
                'third_user_id' => request('golden_uid'),
            ]);

        });

        return $form;
    }

    protected static function fetchGoldenPassportUids()
    {
        $page     = 1;
        $pageSize = 2000;

        $goldenUsers = [];

        while (true) {

            $users = GoldenPassport::getUserList($page, $pageSize);

            $goldenUsers = array_merge($goldenUsers, $users);

            if (count($users) < $pageSize) {
                break;
            }
        }

        $goldenUsers = collect($goldenUsers)->keyBy('id');

        $bindedThirdUids = AdminUserThirdPfBind::getBindedUids('GoldenPassport');

        // 去除已绑定的第三方账号
        foreach ($bindedThirdUids as $thirdUid) {
            unset($goldenUsers[$thirdUid]);
        }

        return $goldenUsers;
    }
}
