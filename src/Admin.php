<?php


namespace Eadmin;


use Eadmin\component\basic\Component;
use Eadmin\component\basic\Message;
use Eadmin\component\basic\Notification;
use Eadmin\controller\ResourceController;
use Eadmin\service\MenuService;
use Eadmin\service\NodeService;
use Eadmin\service\TokenService;
use think\app\Url;
use think\facade\Cache;
use think\facade\Db;
use think\facade\Request;
use think\facade\Route;
use think\facade\View;
use think\route\dispatch\Callback;
use think\route\dispatch\Controller;

class Admin
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
    /**
     * 渲染组件
     * @param $template 模板文件名
     * @param array $vars 模板变量
     * @return Component
     */
    public static function view($template,$vars=[]){
        $content =  View::fetch($template,$vars);
        return Component::create($content);
    }
    public static function notification(){
        return new Notification();
    }
    public static function message(){
        return new Message();
    }
    /**
     * 获取用户角色组
     */
    public static function roles()
    {
        return self::user()->roles;
    }
    /**
     * 获取当前登陆用户id
     * @return string
     */
    public static function id()
    {
        return self::token()->id();
    }
    public static function menus(){
        if (self::id() == config('admin.admin_auth_id')) {
            self::user()->menus();
        }else{
            self::user()->menus();
        }

    }
    /**
     * 验证权限节点
     * @param $class 完整类名
     * @param $function 方法
     * @param string $method 请求方法
     * @return bool
     */
    public static function check($class,$function,$method='get')
    {
        $nodeId = md5($class.$function.strtolower($method));
        $permissions = self::permissions();

        foreach ($permissions as $permission){
            if($permission['id'] == $nodeId){
                if($permission['is_login']){
                    self::token()->auth();
                }
                if(!$permission['is_auth']){
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * 获取权限节点
     * @return mixed
     */
    public static function permissions()
    {

        $permissionsKey = 'eadmin_permissions'.self::id();
        $nodes = Cache::get($permissionsKey);
        if($nodes){
            return $nodes;
        }
        $nodes = self::node()->all();
        if(self::id()){
            $permissions = self::user()->permissions();
            $nodeIds = array_column($permissions,'node_id');
        }else{
            $nodeIds = [];
        }
        if (self::id() != config('admin.admin_auth_id')) {
            foreach ($nodes as $key => &$node) {
                if(!in_array($node['id'],$nodeIds)){
                    $node['is_auth'] = false;
                }
            }
        }

        Cache::tag('eadmin_permissions')->set($permissionsKey,$nodes);
        return $nodes;
    }
    /**
     * 获取当前用户
     * @return mixed
     */
    public static function user()
    {
        return self::token()->user();
    }
    public static function token(){
        return new TokenService();
    }
    /**
     * 菜单服务
     * @return MenuService
     */
    public static function menu(){
        return app('admin.menu');
    }

    /**
     * 权限节点
     * @return NodeService
     */
    public static function node(){
        return new NodeService();
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
     * @param mixed $url
     * @return mixed
     */
    public static function dispatch($url){
        $data = $url;

        if(strpos($url,'/') === false){
            $parse = parse_url($url);
            $path = $parse['path'] ?? '';
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
