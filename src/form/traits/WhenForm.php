<?php


namespace Eadmin\form\traits;

use Eadmin\component\form\FormItem;

/**
 * Trait WhenForm
 * @package Eadmin\form\traits
 * @property FormItem $formItem
 */
trait WhenForm
{
    public function when($operator, $value, $closure = null)
    {
        if (func_num_args() == 2) {
            $closure = $value;
            $value = $operator;
            $operator = '=';
        }
        if($operator == 'in'){
            $operator = '=';
        }
        $formItems = $this->formItem->form()->collectFields($closure);
        $formField = $this->formItem->form()->bindAttr('model');
        $field =  $this->bindAttr('modelValue');
        foreach ($formItems as $formItem) {
            if(is_array($value)){
                foreach ($value as $val){
                    if($operator == 'notIn'){
                        $formItem->where($formField.'.'.$field, $operator, $val);
                    }else{
                        $formItem->whereOr($formField.'.'.$field, $operator, $val);
                    }
                }
            }else{
                $formItem->where($formField.'.'.$field, $operator, $value);
            }
            $this->formItem->form()->push($formItem);
        }
        return $this;
    }
}
