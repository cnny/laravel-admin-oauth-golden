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
    protected function grid()
    {
        $userModel = config('admin.database.users_model');

        $grid = new Grid(new $userModel());

        $grid->column('id', 'ID')->sortable();
        $grid->column('name', trans('admin.name'));
        $grid->column('roles', trans('admin.roles'))->pluck('name')->label();
        $grid->column('created_at', trans('admin.created_at'));
        $grid->column('updated_at', trans('admin.updated_at'));

        $grid->actions(function (Actions $actions) {
            if ($actions->getKey() == 1) {
                $actions->disableDelete();
            }
        });

        $grid->tools(function (Tools $tools) {
            $tools->batch(function (BatchActions $actions) {
                $actions->disableDelete();
            });
        });

        return $grid;
    }

    public function form()
    {
        $userModel = config('admin.database.users_model');
        $permissionModel = config('admin.database.permissions_model');
        $roleModel = config('admin.database.roles_model');

        $form = new Form(new $userModel());

        $userTable = config('admin.database.users_table');
        $connection = config('admin.database.connection');

        $form->display('id', 'ID');

        $goldenUsers = self::buildGoldenUserOptions();

        if ($form->isCreating()) {
            $form->select('golden_uid', '高灯账号')->options($goldenUsers)->rules('required');
        }

        else {
            $form->display('name', trans('admin.name'));
        }

        $form->image('avatar', trans('admin.avatar'));

        // $form->text('username', trans('admin.username'))
        //     ->creationRules(['required', "unique:{$connection}.{$userTable}"])
        //     ->updateRules(['required', "unique:{$connection}.{$userTable},username,{{id}}"]);

        // $form->password('password', trans('admin.password'))->rules('required|confirmed');
        // $form->password('password_confirmation', trans('admin.password_confirmation'))->rules('required')
        //     ->default(function ($form) {
        //         return $form->model()->password;
        //     });

        // $form->ignore(['password_confirmation']);


        $form->multipleSelect('roles', trans('admin.roles'))->options($roleModel::all()->pluck('name', 'id'));
        $form->multipleSelect('permissions', trans('admin.permissions'))->options($permissionModel::all()->pluck('name', 'id'));

        $form->display('created_at', trans('admin.created_at'));
        $form->display('updated_at', trans('admin.updated_at'));

        $form->hidden('username');
        $form->hidden('password');
        $form->ignore(['golden_uid']);

        $form->saving(function (Form $form) use($goldenUsers) {

            if (! $form->password || ($form->password && $form->model()->password != $form->password)) {
                $form->password = bcrypt(config('admin-oauth.default_password', 'admin'));
            }

            $goldenUid = request()->golden_uid;

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

            $goldenUid = request()->golden_uid;

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

    protected static function buildGoldenUserOptions()
    {
        $page     = 1;
        $pageSize = 2000;
        $users    = [];

        while (true) {

            $result = Golden::getUserList($page, $pageSize);

            $users += \Arr::pluck($result['users'], 'name', 'id');

            if (count($result['users']) < $pageSize) {
                break;
            }
        }

        return $users;
    }
}
