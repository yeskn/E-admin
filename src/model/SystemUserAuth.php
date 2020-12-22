<?php


namespace Eadmin\model;


use think\facade\Cache;
use think\Model;
use think\model\Pivot;

class SystemUserAuth extends Pivot
{
    public static function onAfterInsert(Model $model)
    {
        Cache::tag('eadmin_permissions')->clear();
    }
    public static function onAfterUpdate(Model $model)
    {
        Cache::tag('eadmin_permissions')->clear();
    }
}
