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
}
