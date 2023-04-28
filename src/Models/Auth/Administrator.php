<?php

namespace Cann\Admin\OAuth\Models\Auth;

use Illuminate\Notifications\Notifiable;
use Encore\Admin\Auth\Database\Administrator as BaseAdministrator;

class Administrator extends BaseAdministrator
{
    use Notifiable;

    protected $guarded = [];

    protected $appends = [
        'is_boss',
    ];

    public function getIsBossAttribute()
    {
        return $this->inRoles(['administrator', 'boss']);
    }

    public function canViewMenuItem(array $menu)
    {
        return $this->visible($menu['roles']) && (empty($menu['permission']) ?: $this->can($menu['permission']));
    }
}
