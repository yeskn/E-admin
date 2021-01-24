<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2021-01-01
 * Time: 09:45
 */

namespace Eadmin\component\form;


use Eadmin\component\form\Field;

/**
 * form表单
 * Class FormItem
 * @link https://element-plus.gitee.io/#/zh-CN/component/form
 * @package Eadmin\component\form\field
 * @method $this size(string $value) 尺寸 medium / small / mini
 * @method $this prop(string $value) 表单域 model 字段
 * @method $this label(string $value) 标签文本
 * @method $this labelWidth(string $value) 表单域标签的的宽度，例如 '50px'。支持 auto
 * @method $this rules(array $value) 表单验证规则
 * @method $this error(string $value) 表单域验证错误信息, 设置该值会使表单验证状态变为error，并显示该错误信息
 * @method $this showMessage(bool $value=true) 是否显示校验错误信息
 * @method $this inlineMessage(bool $value=true) 以行内形式展示校验信息
 * @property Form $form
 */
class FormItem extends Field
{
    protected $name = 'ElFormItem';
    protected $form;

    public function __construct($prop, $label, $form = null)
    {
        $this->prop($prop);
        $this->label($label);
        $this->form = $form;
    }

    /**
     * 是否必填
     * @return $this
     */
    public function required()
    {
        $this->rules([
            [
                'required' => true,
                'trigger' => 'blur',
                'message' => '请输入' . $this->attr('label'),
            ]
        ]);
        return $this;
    }

    /**
     * 添加内容
     * @param mixed $content
     * @param string $name 插槽名称默认即可default
     * @return static
     */
    public function content($content, $name = 'default')
    {
        if ($content instanceof Field && $content->bindAttr('modelValue') && $this->form) {
            $this->form->setItemComponent($content);
        }
        return parent::content($content, $name);
    }

    /**
     * 创建输入框
     * @param string $prop 表单域 model 字段
     * @param string $label 标签文本
     * @param Form $form
     * @return static
     */
    public static function create($prop = '', $label = '', $form = null)
    {
        return new static($prop, $label, $form);
    }
}
