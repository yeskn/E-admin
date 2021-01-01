<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-12-27
 * Time: 21:28
 */

namespace Eadmin\component\layout;


use Eadmin\component\Component;

class Content extends Component
{
    protected $isComponent = false;
    protected $name = 'div';
    /**
     * 添加一行
     * @param Closure|String  $content 内容
     * @param $span 栅格占据的列数,默认24
     */
    public function row($content,$span = 24){
        $row = new Row();
        if($content instanceof \Closure){
            call_user_func($content,$row);
        }else{
            $row->column($content,$span);
        }
        return $this->content($row);
    }
}
