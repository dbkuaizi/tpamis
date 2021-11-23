<?php
declare (strict_types = 1);

namespace app\Admin\controller;
use think\captcha\facade\Captcha;
use think\Request;
class Login
{
    // 后台登录
    public function index(Request $request)
    {
        // 加载页面
        if ($request->isGet()) {
            return view('amis/login');
        }

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

    }

    /**
     * 执行成功返回
     * @param $msg
     * @param array $data
     */
    public function success($msg,$data = [],$status = 0)
    {
        // 如果传递的 msg 是数组则 赋值给 data
        if (is_array($msg)) {
            $data = $msg;
            $msg = '';
        }
        return ['status' => $status,'msg' => $msg,'data' => $data];
    }

    // 执行错误返回
    public function error($msg,$status = 500)
    {
        return ['status' => $status,'msg' => $msg];
    }
}
