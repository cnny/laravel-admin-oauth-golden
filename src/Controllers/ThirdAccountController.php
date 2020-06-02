<?php

namespace Cann\Admin\OAuth\Controllers;

use Illuminate\Http\Request;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Controllers\AuthController as BaseAuthController;
use Cann\Admin\OAuth\ThirdAccount\ThirdAccount;

class ThirdAccountController extends BaseAuthController
{
    public function getLogin()
    {
        if ($this->guard()->check()) {
            return redirect($this->redirectPath());
        }

        $sources = \Arr::pluck(ThirdAccount::sources(), 'sourceName', 'source');

        return view('oauth::login', compact('sources'));
    }

    public function goToAuthorize(Request $request)
    {
        $request->validate([
            'source' => 'required|string|in:' . implode(',', array_keys(ThirdAccount::SOURCES)),
        ]);

        $thirdService = ThirdAccount::factory($request->source);

        $authorizeUrl = $thirdService
            ->setRedirectUrl(admin_url('/oauth/callback?source=' . $request->source))
            ->getAuthorizeUrl($request->all());

        return redirect($authorizeUrl);
    }

    // 第三方账号授权着陆
    public function oauthCallback(Request $request)
    {
        $posts = $request->validate([
            'source' => 'required|string|in:' . implode(',', array_keys(ThirdAccount::SOURCES)),
        ]);

        // 获取第三方工厂实例
        $thirdService = ThirdAccount::factory($posts['source']);

        // 获取第三方用户信息
        $thirdUser = $thirdService->getThirdUser($request->all());

        // 根据第三方用户信息获取我方用户信息
        $user = $thirdService->getUserByThird($thirdUser);

        // 如果社交账号未绑定我方账号，则前端引导前往绑定
        if (! $user) {

            // 临时存储第三方用户信息
            $request->session()->put('Admin-OAuth-ThirdUser', [
                'source'    => $request->source,
                'user_info' => $thirdUser,
            ]);

            return redirect()->guest(admin_url('oauth/bind-account'));
        }

        Admin::guard()->login($user);

        admin_toastr(trans('admin.login_successful'));

        return redirect(admin_url('/'));
    }

    public function bindAccount(Request $request)
    {
        if (! $thirdUser = $request->session()->get('Admin-OAuth-ThirdUser')) {
            throw new \Exception('Not Found Third User Info');
        }

        if ($request->isMethod('GET')) {
            return view('oauth::bind-account', [
                'sourceName' => ThirdAccount::sources()[$thirdUser['source']]['sourceName'],
            ]);
        }

        else {

            $request->validate([
                'username' => 'required',
                'password' => 'required',
            ]);

            $credentials = $request->only(['username', 'password']);

            if (! Admin::guard()->validate($credentials)) {
                return redirect()->withInput()->withErrors([
                    'username' => '绑定失败，请检查账号或密码',
                ]);
            }

            $user = Admin::guard()->getLastAttempted();

            // 获取第三方工厂实例
            $thirdService = ThirdAccount::factory($thirdUser['source']);

            // 创建绑定关系
            $thirdService->bindUserByThird($user, $thirdUser['user_info']);

            Admin::guard()->login($user);

            admin_toastr('绑定成功');

            return redirect(admin_url('/'));
        }
    }
}
