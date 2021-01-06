<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2021-01-01
 * Time: 09:45
 */

namespace Eadmin\component\form\field;


use Eadmin\component\form\Field;

/**
 *
 * Class FormMany
 * @link https://element-plus.gitee.io/#/zh-CN/component/form
 * @package Eadmin\component\form\field
 * @property Form $form
 */
class FormMany extends Field
{
    protected $name = 'EadminManyItem';
    protected $form;
    public function __construct($field = null, $value = '',$form = null)
    {
        parent::__construct($field, $value);
        $this->form = $form;
        if($this->form){
            $this->bindAttr('modelValue', $this->form->bindAttr('model') . '.' . $field);
            $this->form->bind[$this->form->bindAttr('model')][$field] = $value;
        }
    }
    /**
     * 创建
     * @param string $field 字段
     * @param string $value 数组数据
     * @param Form $form
     * @return static
     */
    public static function create($field = '', $value = '', $form = null)
    {
        return new static($field, $value, $form);
    }
}
