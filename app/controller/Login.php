<?php
declare (strict_types=1);

namespace app\controller;

use app\model\AdminUser;
use think\captcha\facade\Captcha;
use think\facade\Config;
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

        // 获取校验字段
        $login_field = Config::get('amis.login_mode') ?: 'username';
        if(is_array($login_field)) {
            $login_field = implode('|',$login_field);
        }
        
        $admin_user = AdminUser::where($login_field, $data['username'])->find();
        // 如果用户名不存在
        if (empty($admin_user))
        {
            return $this->error('用户名不存在');
        }

        // 验证密码
        if (!password_verify($data['password'],$admin_user->password))
        {
            return $this->error('登录密码错误');
        }

        // 验证账号状态
        if(!$admin_user->status)
        {
            return $this->error('账号已被锁定');
        }
        
        // 更新登录信息
        $admin_user->login_time = date('Y-m-d H:i:s');
        $admin_user->login_ip = $request->ip();
        $admin_user->save();

        // 写入session
        Session::set('admin_uid', $admin_user->id);
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
