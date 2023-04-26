<?php

namespace Cann\Admin\OAuth\Models\Auth;

use Encore\Admin\Auth\Database\Permission as BasePermission;
use Illuminate\Http\Request;

class Permission extends BasePermission
{
    public function shouldPassThrough(Request $request): bool
    {
        if (empty($this->http_method) && empty($this->http_path)) {
            return true;
        }

        $method = $this->http_method;

        $matches = array_map(function ($path) use ($method) {

            if (\Str::contains($path, ':')) {
                [$method, $path] = explode(':', $path);
                $method = explode(',', $method);
            }

            // 修复了作者近一年都没修的 BUG
            // @see https://github.com/z-song/laravel-admin/issues/3491
            $path = trim(config('admin.route.prefix'), '/') . $path;

            return compact('method', 'path');

        }, explode("\n", $this->http_path));

        foreach ($matches as $match) {
            if ($this->matchRequest($match, $request)) {
                return true;
            }
        }

        return false;
    }
}
