<?php

namespace Cann\Admin\OAuth\Controllers;

use Illuminate\Http\Request;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Controllers\AuthController;
use Cann\Admin\OAuth\ThirdAccount\ThirdAccount;

class ThirdAccountController extends AuthController
{
    public function getLogin()
    {
        // 已登录的用户
        if ($this->guard()->check()) {
            if (request('reset')) {
                // 强行退出
                parent::getLogout(request());
            }
            else {
                // 无需访问登录页
                return redirect($this->redirectPath());
            }
        }

        // 强行用密码登录
        if (request('pwd_login')) {
            return parent::getLogin();
        }

        return redirect(admin_url('/oauth/authorize?source=GoldenPassport'));
    }

    public function getLogout(Request $request)
    {
        parent::getLogout($request);

        return redirect('https://sh-passport.wetax.com.cn/logout');
    }

    public function goToAuthorize(Request $request)
    {
        $request->validate([
            'source' => 'required|string|in:' . implode(',', array_keys(ThirdAccount::SOURCES)),
        ]);

        $service = ThirdAccount::factory($request->source);

        $authorizeUrl = $service->getAuthorizeUrl($request->all());

        return redirect($authorizeUrl);
    }

    // 第三方账号授权着陆
    public function oauthCallback(Request $request)
    {
        $posts = $request->validate([
            'source' => 'required|string|in:' . implode(',', array_keys(ThirdAccount::SOURCES)),
        ]);

        // 获取第三方工厂实例
        $service = ThirdAccount::factory($posts['source']);

        // 获取第三方用户信息
        $thirdUser = $service->getThirdUser($request->all());

        // 根据第三方用户信息获取我方用户信息
        $user = $service->getUserByThird($thirdUser);

        // 如果社交账号未绑定我方账号，则前端引导前往绑定
        if (! $user) {

            // 临时存储第三方用户信息
            $request->session()->put('AdminOAuthThirdUser', [
                'source'    => $request->source,
                'user_info' => $thirdUser,
            ]);

            return redirect()->guest(admin_url('oauth/bind-account'));
        }

        else{

            // 同步更新用户信息
            $user->update([
                'name'     => $thirdUser['full_info']['name'],
                'avatar'   => $thirdUser['full_info']['avatar_url'] ?? '',
            ]);
        }

        Admin::guard()->login($user);

        admin_toastr(trans('admin.login_successful'));

        return redirect(admin_url('/'));
    }

    public function bindAccount(Request $request)
    {
        if (! $thirdUser = $request->session()->get('AdminOAuthThirdUser')) {
            throw new \Exception('Not Found Third User Info');
        }

        if ($request->isMethod('GET')) {
            return view('admin-oauth::bind-account', [
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
                return back()->withInput()->withErrors([
                    'username' => '账号或密码不正确',
                ]);
            }

            $user = Admin::guard()->getLastAttempted();

            // 获取第三方工厂实例
            $service = ThirdAccount::factory($thirdUser['source']);

            // 创建绑定关系
            $service->bindUserByThird($user, $thirdUser['user_info']);

            Admin::guard()->login($user);

            admin_toastr('绑定成功');

            return redirect(admin_url('/'));
        }
    }
}
