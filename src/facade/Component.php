<?php


namespace Eadmin\facade;


use think\Facade;

/**
 * Class Component
 * @package Eadmin\facade
 * @method string redirect($url,$params) static 跳转重定向
 * @method string fetch($template,$props=[],$vars=[]) static 渲染组件模板
 * @method string view($content) static 渲染视图内容
 * @method \Eadmin\component\Message message() static 消息提示
 * @method \Eadmin\component\Notification notification() static 通知
 */
class Component extends Facade
{
    protected static function getFacadeClass()
    {
        return \Eadmin\Component::class;
    }
}
