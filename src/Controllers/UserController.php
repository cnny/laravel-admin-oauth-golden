<?php

namespace Cann\Admin\OAuth\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Cann\Admin\OAuth\ThirdAccount\ThirdAccount;
use Cann\Admin\OAuth\Services\Golden;
use Cann\Admin\OAuth\Models\AdminUserThirdPfBind;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Grid\Tools;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Grid\Displayers\Actions;
use Encore\Admin\Grid\Tools\BatchActions;
use Encore\Admin\Controllers\AuthController as BaseAuthController;
use Encore\Admin\Controllers\UserController as BaseUserController;

class UserController extends BaseUserController
{
    public function form()
    {
        $form = parent::form();

        $form->display('id', 'ID');

        $goldenUsers = self::fetchGoldenPassportUids();

        if ($form->isCreating()) {
            $form->select('golden_uid', '高灯账号')->options($goldenUsers)->rules('required');
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

        $form->ignore(['golden_uid']);

        $form->saving(function (Form $form) use($goldenUsers) {

            $goldenUid = \Request::input('golden_uid');

            if (! $form->model()->id) {

                $bind = AdminUserThirdPfBind::where([
                    'platform'      => 'Golden',
                    'third_user_id' => $goldenUid,
                ])->first();

                if ($bind && $bind->user) {
                    return back()->withInput()->withErrors(new MessageBag([
                        'golden_uid' => '该账号已存在',
                    ]));
                }

                $form->username = \Str::random(16);
                $form->name = $goldenUsers[$goldenUid];
            }
        });

        $form->saved(function (Form $form) use($goldenUsers) {

            $goldenUid = \Request::input('golden_uid');

            // Golden账号和本地账号创建绑定关系
            AdminUserThirdPfBind::updateOrCreate([
                'platform'      => 'Golden',
                'third_user_id' => $goldenUid,
            ], [
                'user_id' => $form->model()->id,
            ]);
        });

        return $form;
    }

    protected static function fetchGoldenPassportUids()
    {
        $page     = 1;
        $pageSize = 2000;
        $return    = [];

        while (true) {

            $users = Golden::getUserList($page, $pageSize);

            $return = array_merge($return, \Arr::pluck($users, 'name', 'id'));

            if (count($users) < $pageSize) {
                break;
            }
        }

        return $return;
    }
}
