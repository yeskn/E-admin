<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-03-25
 * Time: 21:43
 */

namespace Eadmin;


use think\facade\Db;
use think\route\Resource;
use think\Service;
use Eadmin\controller\Backup;
use Eadmin\controller\Log;
use Eadmin\controller\Menu;
use Eadmin\controller\Notice;
use Eadmin\controller\Plug;
use Eadmin\middleware\Permission;
use Eadmin\service\FileService;
use Eadmin\service\PlugService;
use Eadmin\service\TableViewService;

class ServiceProvider extends Service
{
    public function register()
    {

        //注册上传路由
        FileService::instance()->registerRoute();
        //注册表格视图路由
        TableViewService::instance()->registerRoute();
        //注册插件
        PlugService::instance()->register();
        $this->registerView();
        $this->app->middleware->route( \Eadmin\middleware\Permission::class);
    }

    protected function registerView(){
        //入口加载
        $this->app->route->get('/',function (){
            $view = file_get_contents(__DIR__.'/view/index.vue');
            $view =  str_replace('E-Admin',sysconf('web_name'),$view);
            return $view;
        });
        //菜单管理
        $this->app->route->resource('menu',Menu::class);
        //日志调试
        $this->app->route->post('log/logData',Log::class.'@logData');
        $this->app->route->get('log/debug',Log::class.'@debug');
        $this->app->route->post('log/remove',Log::class.'@remove');
        //插件
        $this->app->route->get('plug/add',Plug::class.'@add');
        $this->app->route->get('plug',Plug::class.'@index');
        $this->app->route->put('plug/enable',Plug::class.'@enable');
        $this->app->route->put('plug/install',Plug::class.'@install');
        $this->app->route->resource('plug',Plug::class);
        //消息通知
        $this->app->route->get('notice/notification',Notice::class.'@notification');
        $this->app->route->post('notice/system',Notice::class.'@system');
        $this->app->route->post('notice/reads',Notice::class.'@reads');
        $this->app->route->delete('notice/clear',Notice::class.'@clear');
        //数据库备份
        $this->app->route->get('backup/config',Backup::class.'@config');
        $this->app->route->put('backup/add',Backup::class.'@add');
        $this->app->route->put('backup/reduction',Backup::class.'@reduction');
        $this->app->route->resource('backup',Backup::class);

        $this->app->route->resource(':controller',':controller')->ext('rest');
        $rules = $this->app->route->getGroup()->getRules();
        foreach ($rules as $key=>$rule){
            if(isset($rule[1]) && $rule[1] instanceof Resource){
                if($rule[1]->getRoute() !=':controller'){
                    $this->app->route->get($rule[1]->getName(),$rule[1]->getRoute().'@index');
                }
                $rule[1]->ext('rest');
            }
        }
    }
    public function boot()
    {
        $this->commands([
            'Eadmin\command\BuildView',
            'Eadmin\command\Publish',
            'Eadmin\command\Plug',
            'Eadmin\command\Migrate',
            'Eadmin\command\Seed',
            'Eadmin\command\Install',
        ]);
    }
}
