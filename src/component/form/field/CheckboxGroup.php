<?php


namespace Eadmin\component\form\field;


use Eadmin\component\form\Field;

/**
 * 多选框组
 * Class CheckboxGroup
 * @link https://element-plus.gitee.io/#/zh-CN/component/checkbox
 * @method $this size(string $size) 多选框组尺寸，仅对按钮形式的 Checkbox 或带有边框的 Checkbox 有效 medium / small / mini
 * @method $this min(int $num) 可被勾选的 checkbox 的最小数量
 * @method $this max(int $num) 可被勾选的 checkbox 的最大数量
 * @method $this textColor(string $color) 按钮形式的 Checkbox 激活时的文本颜色
 * @method $this fill(string $color) 按钮形式的 Checkbox 激活时的填充色和边框色
 * @package Eadmin\component\form\field
 */
class CheckboxGroup extends Field
{
    protected $name = 'ElCheckboxGroup';
    //禁用数据
    protected $disabledData = [];
    public function __construct($field = null, $value = '')
    {
        if(empty($value)){
            $value = [];
        }
        parent::__construct($field, $value);
    }

    /**
     * 禁用选项数据
     * @param array $data 禁用数据
     */
    public function disabledData(array $data)
    {
        $this->disabledData = $data;
    }

    /**
     * 设置选项数据
     * @param array $data 选项数据
     * @param bool $buttonTheme 是否按钮样式
     * @return $this
     */
    public function options(array $data, bool $buttonTheme = false)
    {
        foreach ($data as $value => $label) {
            if (in_array($value, $this->disabledData)) {
                $disabled = true;
            } else {
                $disabled = false;
            }
            $options[] = [
                'value' => $value,
                'label' => $label,
                'disabled' => $disabled,
            ];
        }
        if ($buttonTheme) {
            $checkbox = CheckboxButton::create();
        } else {
            $checkbox = Checkbox::create();
        }
        $field = $checkbox->bindAttr('modelValue');
        $checkbox->removeBind($field);
        $checkbox->removeAttr('modelValue');
        $checkboxOption = $checkbox
            ->map($options)
            ->mapAttr('label', 'value')
            ->mapAttr('key', 'value')
            ->mapAttr('slotDefault', 'label')
            ->mapAttr('disabled', 'disabled');
        return $this->content($checkboxOption);
    }
}