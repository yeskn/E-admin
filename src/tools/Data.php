<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-05-30
 * Time: 15:33
 */

namespace Eadmin\tools;

use think\facade\Db;

class Data
{
    /**
     * 配置系统参数
     * @param string $name 参数名称
     * @param boolean $value 无值为获取
     * @return string|boolean
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public static function sysconf($name, $value = null)
    {
        if (is_null($value)) {
            $value = Db::name('SystemConfig')->where('name', $name)->value('value');
            if (is_null($value)) {
                return '';
            } else {
                return $value;
            }
        } else {
            $sysconfig = Db::name('SystemConfig')->where('name', $name)->find();
            if ($sysconfig) {
                return Db::name('SystemConfig')->where('name', $name)->update(['value' => $value]);
            } else {
                return Db::name('SystemConfig')->insert([
                    'name' => $name,
                    'value' => $value,
                ]);
            }
        }
    }

    public static function generateTree($data,  $id = 'id',$pid = 'pid',$children = 'children')
    {
        $items = array();
        foreach($data as $v){
            $items[$v[$id]] = $v;
        }
        $tree = array();
        foreach($items as $k => $item){
            if(isset($items[$item[$pid]])){
                $items[$item[$pid]][$children][] = &$items[$k];
            }else{
                $tree[] = &$items[$k];
            }
        }
        return $tree;

    }
}
