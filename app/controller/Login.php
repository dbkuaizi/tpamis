<?php
declare (strict_types=1);

namespace app\controller;

use app\model\AdminUser;
use think\captcha\facade\Captcha;
use think\facade\Validate;
use think\Request;
use think\facade\Session;

class Login
{
    // 后台登录
    public function index(Request $request)
    {
        // 加载页面
        if ($request->isGet()) {
            return view('amis/login');
        }
        // 获取提交的参数
        $data['username'] = $request->post('name');
        $data['password'] = $request->post('pwd');
        $data['code'] = $request->post('code');

        $validate = Validate::rule([
            'username|用户名' => 'require',
            'password|密码' => 'require',
            'code|验证码' => 'require|captcha',
        ]);

        // 验证
        if (!$validate->check($data)) {
            return $this->error($validate->getError());
        }

        $admin_user = AdminUser::where('username', $data['username'])->find();
        // 如果用户名不存在
        if (empty($admin_user))
        {
            return $this->error('用户名不存在');
        }

        // 验证密码
        if (!password_verify($data['password'],$admin_user->passowrd))
        {
            return $this->error('登录密码错误');
        }

        $admin_user->login_time = date('Y-m-d H:i:s');
        $admin_user->login_ip = $request->ip();
        $admin_user->save();

        // 写入session
        Session::set('admin_user', $admin_user['id']);
        // 验证登录
        return $this->success('登录成功');

    }

    // 获取验证码
    public function verify()
    {
        return Captcha::create();
    }

    /**
     * 退出登录
     */
    public function out()
    {
        Session::clear();
        return redirect('/login');
    }

    /**
     * 执行成功返回
     * @param $msg
     * @param array $data
     */
    public function success($msg, $data = [], $status = 0)
    {
        // 如果传递的 msg 是数组则 赋值给 data
        if (is_array($msg)) {
            $data = $msg;
            $msg = '';
        }
        return ['status' => $status, 'msg' => $msg, 'data' => $data];
    }

    // 执行错误返回
    public function error($msg, $status = 500)
    {
        return ['status' => $status, 'msg' => $msg];
    }
}
