<?php


namespace Eadmin\form\field;


use think\facade\View;
use Eadmin\form\Field;

class TableText extends Field
{
    public function columns($data){
        $this->setAttr(':columns',json_encode($data));
        return $this;
    }
    public function render()
    {
        //vue视图组件
        $this->template = 'tableText';
        $component = parent::render();
        //返回组件
        return $this->buildComponent($component);
    }
}
