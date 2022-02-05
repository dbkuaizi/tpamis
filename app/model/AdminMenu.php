<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class AdminMenu extends Model
{
    //

    public static function getUrlName($path)
    {
        return self::where('path',$path)->value('label');
    }
}
