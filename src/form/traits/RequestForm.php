<?php


namespace Eadmin\form\traits;


use think\route\Resource;

trait RequestForm
{
    protected function dispatch(){
        return  app()->route->check()->getDispatch();
    }

    /**
     * 返回请求方法
     * @return mixed
     */
    public function action(){
        return $this->dispatch()[1];
    }

    /**
     * 返回提交请求url
     * @return string
     */
    public function requestUrl(){
        $requestUrl = $this->getRequestUrl();
        $controller = $this->dispatch()[0];
        $rules = app()->route->getGroup()->getRules();
        foreach ($rules as $key=>$rule){
            if(isset($rule[1]) && $rule[1] instanceof Resource){
                $resource[] = $rule[1];
                $route = $rule[1]->getRoute();
                if($controller == $route){
                    $requestUrl =  $rule[1]->getName();
                    break;
                }
            }
        }
        return $requestUrl;
    }
}
