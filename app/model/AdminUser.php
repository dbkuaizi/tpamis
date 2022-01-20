<?php
declare (strict_types = 1);

namespace app\model;

use think\model\concern\SoftDelete;
use think\Model;

/**
 * @mixin \think\Model
 */
class AdminUser extends Model
{
    use SoftDelete;

    // 返回当前用户拥有的权限id
    public function getPermissions()
    {
        // 如果角色为空直接返回空数组
        if(empty($this->roles))
        {
            return [];
        }
        // 查询角色表
       $admin_role = AdminRole::where('id','in',$this->roles)->field('permissions')->select();
       $permissions_arr = [];
       foreach($admin_role as $role)
       {
            $permissions_arr = array_merge($permissions_arr,explode(',',$role->permissions));
       }
       
       return array_unique($permissions_arr);
    }
}
