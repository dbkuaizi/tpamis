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

    /**
     * 重置账号密码
     *
     * @param Request $request
     * @return void
     */
    public function reset_pwd(Request $request)
    {

        // 获取传参
        $data = $request->post();

        if($data['new_pwd'] !== $data['repeat_pwd'])
        {
            return $this->error('两次新密码不一致');
        }

        // 验证密码规则
        $rule['new_pwd|密码'] = 'length:8,30';
        $validate = Validate::rule($rule);
        if (!$validate->check($data))
        {
            return $this->error($validate->getError());
        }

        // 获取数据库中当前用户信息
        $ModelAdminUser = ModelAdminUser::find($request->uid);

        // 验证旧密码
        if (!password_verify($data['old_pwd'],$ModelAdminUser->password))
        {
            return $this->error('旧密码不正确');
        }

        // 修改密码
        $ModelAdminUser->password = password_hash($data['new_pwd'],PASSWORD_BCRYPT);
        $ModelAdminUser->save();
        return $this->success('修改成功,下次登陆请使用新密码');
    }
}
