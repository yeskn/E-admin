<?php


namespace Eadmin\component\basic;


use Eadmin\component\Component;

/**
 * Class Router
 * @package Eadmin\component\basic
 */
class Router extends Component
{
    protected $name = 'RouterLink';
    protected $params = [];
    
    public static function create()
    {
        return new self();
    }

    /**
     * 跳转路径
     * @param string $url
     * @param array  $params
     * @return Router|mixed|null
     */
    public function to(string $url,$params){
        return $this->attr('to',['path'=>$url,'query'=>$params]);
    }
}
