<?php

namespace Eadmin;

use Eadmin\component\basic\Notification;
use think\exception\HttpResponseException;
use think\facade\View;

/**
 * 前端组件类
 * Class Component
 * @package Eadmin\component
 * @method \Eadmin\component\basic\Message message() 消息提示
 * @method \Eadmin\component\basic\Notification notification() 通知
 */
class Component
{
    /**
     * 跳转重定向
     * @param string $url 跳转链接
     * @param array $params 参数
     */
    public function redirect($url, array $params = [])
    {
        $query = http_build_query($params);
        if ($query) {
            $url .= '?' . $query;
        }
        throw new HttpResponseException(json([
            'code' => 40021,
            'url' => $url
        ]));
    }

    /**
     * 渲染视图内容
     * @param $content
     */
    public function view($content){
        if(is_string($content)){
            $content = base64_encode(gzcompress($content));
        }
        throw new HttpResponseException(json([
            'code' => 50000,
            'data' => $content,
            'eadmin_component'=>request()->has('eadmin_component')
        ]));
    }
    public function display($content){
        return "<eadmin-component data='" . rawurlencode($content) . "'></eadmin-component>";
    }
    /**
     * 渲染组件模板
     * @param $template 模板文件名
     * @param array $props 组件参数
     * @param array $vars 模板变量
     * @return string
     * @throws \Exception
     */
    public function fetch($template,$props = [],$vars = []){
        if(is_file($template)){
            $view = View::display(file_get_contents($template),$vars);
        }else{
            $view = View::fetch($template,$vars);
        }
        $componentProps = [];
        foreach ($props as $prop=>$value){
            if(is_string($value)){
                $componentProps[] = "{$prop}='{$value}'";
            }elseif (is_array($value)){
                $value = json_encode($value,JSON_UNESCAPED_UNICODE);
                $componentProps[] = ":{$prop}='{$value}'";
            }elseif (is_bool($value)){
                if($value){
                    $componentProps[] = ":{$prop}='true'";
                }else{
                    $componentProps[] = ":{$prop}='false'";
                }
            }else{
                $componentProps[] = ":{$prop}='{$value}'";
            }
        }
        $componentProps = implode(' ',$componentProps);
        return "<eadmin-component data='" . rawurlencode($view) . "' $componentProps></eadmin-component>";
    }
    
    
}
