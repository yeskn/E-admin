<?php


namespace Eadmin;


use Eadmin\controller\ResourceController;
use Eadmin\service\MenuService;
use think\app\Url;
use think\facade\Request;
use think\facade\Route;
use think\route\dispatch\Callback;
use think\route\dispatch\Controller;

class Admin
{
    /**
     * 菜单服务
     * @return MenuService
     */
    public static function menu(){
        return app('admin.menu');
    }

    /**
     * 树形
     * @param array $data 数据
     * @param string $id 
     * @param string $pid
     * @param string $children
     * @return array
     */
    public static function tree(array $data,  $id = 'id',$pid = 'pid',$children = 'children')
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

    /**
     * 解析url并执行返回
     * @param string $url 
     * @return mixed
     */
    public static function dispatch(string $url){
        $data = $url;
        if(strpos($url,'/') !== false){
            $parse = parse_url($url);
            $path = $parse['path'];
            $vars = [];
            $request = app()->request;
            if (isset($parse['query'])){
                $querys = explode('&',$parse['query']);
                foreach ($querys as $query){
                    list($name,$value) = explode('=',$query);
                    $vars[$name] = $value;
                }
            }
            $pathinfo = array_filter(explode('/', $path));
            $name = current($pathinfo);
            if($name == app('http')->getName()){
                array_shift($pathinfo);
            }
            $url = implode('/',$pathinfo);
            $dispatch = Request::rule()->check(request(),$url);
            if($dispatch === false){
                try{
                    $dispatch = Route::url($url);
                }catch (\Exception $exception){

                }
            }
            if($dispatch){
                $dispatch->init(app());
                try{
                    $get = $request->get();
                    $request->withGet($vars);
                    if($dispatch instanceof Controller) {
                        list($controller,$action) = $dispatch->getDispatch();
                        $instance = $dispatch->controller($controller);
                        $reflect = new \ReflectionMethod($instance, $action);
                        $data =  app()->invokeReflectMethod($instance, $reflect, $vars);
                    }elseif ($dispatch instanceof Callback){
                        $data = app()->invoke($dispatch->getDispatch(), $vars);
                    }
                    $request->withGet($get);
                }catch (\Exception $exception){

                }
            }
        }
        return $data;
    }
    public static function registerRoute(){
        app()->route->resource('eadmin',ResourceController::class)->ext('rest');;
    }
}
