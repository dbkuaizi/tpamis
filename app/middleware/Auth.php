<?php
declare (strict_types = 1);

namespace app\middleware;

use app\model\AdminMenu;
use app\model\AdminUser;
use think\facade\Session;

// 登录校验中间件
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
            '',                 // 根目录
            '/login',           // 登录页
            '/login/verify',    // 登录验证码
        ];

        // 获取请求路径
        $request_path = $request->baseUrl();

        // 如果是 delete 请求就替换 id
        if($request->isDelete())
        {
            $request_path = substr_replace($request_path,'/*',strrpos($request_path,'/'));
        }


        // 如果是白名单 就直接放行
        if(in_array($request_path,$auth_white))
        {
            return $next($request);
        }

        // 判断登录状态
        if (!Session::has('admin_user'))
        {
            
            // 判断是页面请求还是接口请求，返回不同的登录状态
            if($request->isAjax()) 
            {
                return json(['status'=>401,'msg' => '未登录']);
            } else {
                return redirect('/login');
            }

        }

        // 获取登录用户 uid
        $request->uid = Session::get('admin_user.id');

        // 获取页面不校验权限
        if((stripos($request_path,'/view') === 0))
        {
            return $next($request);
        }
        
        // 判断权限
        $admin_user = AdminUser::find($request->uid);

        // 如果是超级管理员则不判断权限
        if(isset($admin_user->super_admin) && $admin_user->super_admin === 1)
        {
            return $next($request);
        }

        // 获取该用户的授权
        $permissions_arr = $admin_user->getPermissions();
        $admin_menu = AdminMenu::where(function($query) use($permissions_arr) {
            $query->where('id','in',$permissions_arr)->whereOr('verify',0);
        })->where('path','=',$request_path)->findOrEmpty();
        
        // 如果没有权限
        if($admin_menu->isEmpty())
        {
            // 如果是组件权限不足
            if((stripos($request_path,'/com/get') === 0))
            {
                $result = \think\facade\Db::table('sys_com')->where('code','sys_page_not_permission')->find();
                $body = \think\facade\View::display($result['body'],$result);
                return response($body);
            }

            // 如果是接口权限不足
            return json(['status'=> 403,'msg' => '"'. $request_path . '" 权限不足，请联系管理员']);
        }

        // 校验通过 执行下面的逻辑
        return $next($request);
    }
}
