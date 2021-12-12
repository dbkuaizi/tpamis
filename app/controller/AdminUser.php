<?php
declare (strict_types = 1);

namespace app\controller;
use think\facade\Validate;
use app\BaseController;
use app\model\AdminUser as ModelAdminUser;
use app\Request;

/**
 * 后端账号管理
 */
class AdminUser extends BaseController
{
    /**
     * 管理员账户的新增与保存
     *
     * @param Request $request
     * @return void
     */
    public function save(Request $request)
    {
        // 获取传参
        $data = $request->post();
        // 设置校验规则
        $rule = [];
        // 如果是新增就校验密码必填项
        if(empty($data['id']))
        {
            $rule['password|密码'] = ['require','length:8,30'];
        } else {
            $rule['password|密码'] = 'length:8,30';
            // 如果是修改 就先过滤掉自己
            $check_where[] = ['id','<>',$data['id']];
        }

        $validate = Validate::rule($rule);
    
        // 验证
        if (!$validate->check($data))
        {
            var_dump($data);
            return $this->error($validate->getError());
        }
        
        // 如果有传递密码就加密
        if(!empty($data['password']))
        {
            $data['password'] = password_hash($data['password'],PASSWORD_BCRYPT);
        }

        // 判断用户名是否冲突
        $check_where[] = ['username','=',$data['username']];
        $ModelAdminUser = ModelAdminUser::where($check_where)->find();
        if(!is_null($ModelAdminUser))
        {
            return $this->error("用户名已存在");
        }

        if(empty($data['id']))
        {
            ModelAdminUser::create($data);
        } else {
            ModelAdminUser::update($data);
        }

        return $this->success('保存成功');

    }
}
