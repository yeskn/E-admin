<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-04-26
 * Time: 21:46
 */

namespace Eadmin\middleware;


use think\facade\App;
use think\Request;
use Eadmin\service\AdminService;
use Eadmin\service\NodeService;
use Eadmin\service\TokenService;

class Permission
{
    public function handle(Request $request, \Closure $next)
    {

        $pathinfo = '';
        $url = request()->url();
        $url = parse_url($url);
        $node = substr($url['path'],1);
        if($request->has('submitFromMethod')) {
            $method = $request->param('submitFromMethod');
            if($method != 'form'){
                $node = preg_replace("/(\/(\d+)\.rest)$/U",'',$node);
                $pathinfo = '/' . $method;
            }
        }
        $node = $node.$pathinfo;
        if (empty($node) || $request->method() == 'options') {
            return $next($request);
        }
        $moudel = app('http')->getName();
        //验证权限
        $authNodules = array_keys(config('admin.authModule'));
        if (in_array($moudel,$authNodules) && !AdminService::instance()->check($node, $request->method())) {
            return json(['code'=>44000,'message'=>'没有访问该操作的权限']);
        }
        return $next($request);
    }
}
