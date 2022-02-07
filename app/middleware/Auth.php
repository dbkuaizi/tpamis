<?php
declare (strict_types = 1);

namespace app\middleware;

use app\model\AdminLog;
use app\model\AdminMenu;
use app\model\AdminUser;
use think\facade\Session;

// 登录校验中间件
class Auth
{
    /**
     * 前置中间件
     *
     * @param \think\Request $request
     * @param \Closure       $next
     * @return Response
     */
    public function handle($request, \Closure $next)
    {
        // 白名单,不校验登录状态
        $auth_white = [
            '/',                 // 根目录
            '/login',           // 登录页
            '/login/verify',    // 登录验证码
        ];

        // 获取请求路径
        $request->path = $request->baseUrl();

        // 如果是 delete 请求就替换 id
        if($request->isDelete())
        {
            $request->path = substr_replace($request->path,'/*',strrpos($request->path,'/'));
        }


        // 如果是白名单 就直接放行
        if(in_array($request->path,$auth_white))
        {
            return $next($request);
        }

        // 判断登录状态
        if (!Session::has('admin_uid'))
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
        $request->uid = Session::get('admin_uid');

        // 获取页面不校验权限
        if((stripos($request->path,'/view') === 0))
        {
            return $next($request);
        }
        
        // 查询请求的接口
        $admin_menu = AdminMenu::where('path','=',$request->path)->findOrEmpty();
        
        // 权限不存在
        if($admin_menu->isEmpty())
        {
            return json(['status'=> 403,'msg' => '"'. $request->path . '" 权限不存在,请检查请求地址']);
        }

        // 设置菜单标题，用于写日志
        $request->menu_title = $admin_menu->label;

        // 日志开关，如果是行为 且开启了日志
        $request->log_switch = ($admin_menu->type == 'action' && $admin_menu->log);

        // 获取登录用户信息
        $admin_user = AdminUser::find($request->uid);

        // 获取该用户的授权
        $permissions_arr = $admin_user->getPermissions();
        
        // 权限校验, 不是超级管理员 AND URL校验权限 AND 没有授权
        if((!$admin_user->super_admin) && (!$admin_menu->verify) && in_array($admin_menu->id,$permissions_arr))
        {
            // 如果是组件权限不足
            if((stripos($request->path,'/com/get') === 0))
            {
                $result = \think\facade\Db::table('sys_com')->where('code','sys_page_not_permission')->find();
                $body = \think\facade\View::display($result['body'],$result);
                return response($body);
            }

            // 如果是接口权限不足
            return json(['status'=> 403,'msg' => '"'. $request->path . '" 权限不足，请联系管理员']);
        }

        // 校验通过 执行下面的逻辑
        return $next($request);
    }

    // 后置行文
    public function end(\think\Response $response)
    {
       if(request()->log_switch)
       {
            AdminLog::write();
       }
       return $response;
    }
}
