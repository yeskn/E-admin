<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-04-26
 * Time: 21:46
 */

namespace Eadmin\middleware;


use Eadmin\Admin;
use think\facade\App;
use think\Request;
use Eadmin\service\AdminService;
use Eadmin\service\NodeService;
use Eadmin\service\TokenService;

class Permission
{
    public function handle(Request $request, \Closure $next)
    {
        $moudel = app('http')->getName();
        $eadmin_class = $request->param('eadmin_class');
        $eadmin_function = $request->param('eadmin_function');
        if(empty($eadmin_class)|| empty($eadmin_function)){
            list($eadmin_class,$eadmin_function) = app()->route->check()->getDispatch();
        }
        //验证权限
        $authNodules = array_keys(config('admin.authModule'));
        if (in_array($moudel,$authNodules) && !Admin::check($eadmin_class,$eadmin_function, $request->method())) {
            return json(['code'=>44000,'message'=>'没有访问该操作的权限']);
        }
        return $next($request);
    }
}
