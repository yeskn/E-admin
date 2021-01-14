<?php


namespace Eadmin;


use Eadmin\controller\ResourceController;
use Eadmin\service\MenuService;

class Admin
{
    /**
     * 菜单服务
     * @return MenuService
     */
    public static function menu(){
        return app('admin.menu');
    }
    public static function tree($data,  $id = 'id',$pid = 'pid',$children = 'children')
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
    public static function registerRoute(){

        app()->route->resource('eadmin',ResourceController::class)->ext('rest');;
    }
}
