<?php


namespace Eadmin\component\form\field;


use Eadmin\component\form\Field;

/**
 * Class Spec
 * @package Eadmin\component\form\field
 * @method $this data(array $data) 规格数据
 */
class Spec extends Field
{
    protected $name = 'EadminSpec';

    /**
     * @param \Closure $closure
     * @param string $field1 规格组保存字段
     * @param string $field2 规格分组id
     * @return $this
     */
    public function table(\Closure $closure,$field1 = 'specs',$field2 = 'spec_id'){
        $formItems = $this->formItem->form()->collectFields($closure);
        $columns = [];
        foreach ($formItems as $formItem) {
            $columns[] = [
                'label'=>$formItem->attr('label'),
                'prop'=>$formItem->attr('prop'),
                'component'=>$formItem->content['default'][0]
            ];
        }
        $this->attr('columns',$columns);
        $this->bindAttr('specs',$field1,true);
        $this->bindAttr('specId',$field2,true);
        return $this;
    }
}
