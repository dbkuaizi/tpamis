<?php
declare (strict_types = 1);

namespace app\middleware;
use think\exception\HttpException;
use think\facade\Session;
// 登录与权限校验中间件
class Auth
{
    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param \Closure       $next
     * @return Response
     */
    public function handle($request, \Closure $next)
    {
        // 白名单,不校验登录状态
        $auth_white = [
            '/login',
            '/login/verify'
        ];
        // dd($request->baseUrl());
        // 如果没有登陆 并且不是白名单
        if (!Session::has('admin_user') && !in_array($request->baseUrl(),$auth_white))
        {
            
            // 判断是页面请求还是接口请求，返回不同的登录状态
            if($request->isAjax()) 
            {
                return json(['status'=>401,'msg' => '未登录']);
            } else {
                return redirect('/login');
            }

        }

        // 权限校验 判断当前操作是否有执行权限
        $request->uid = Session::get('admin_user.id');
        // 校验通过 执行下面的逻辑
        return $next($request);
    }
}
