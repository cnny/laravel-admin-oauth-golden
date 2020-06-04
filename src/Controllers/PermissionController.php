<?php

namespace Cann\Admin\OAuth\Controllers;

use Encore\Admin\Controllers\PermissionController as BasePermissionController;

class PermissionController extends BasePermissionController
{
    protected function grid()
    {
        $grid = parent::grid();

        $grid->paginate(500);
        $grid->model()->orderBy('slug');

        $grid->column('id')->hide();
        $grid->column('created_at')->hide();

        return $grid;
    }
}
