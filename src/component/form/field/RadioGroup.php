<?php


namespace Eadmin\component\form\field;


use Eadmin\component\form\Field;
use think\helper\Str;

/**
 * 单选框组
 * Class RadioGroup
 * @link https://element-plus.gitee.io/#/zh-CN/component/radio
 * @method $this textColor(string $color) 按钮形式的 Radio 激活时的文本颜色    #ffffff
 * @method $this fill(string $color) 按钮形式的 Radio 激活时的填充色和边框色 #409EFF
 * @method $this size(string $size) 单选框组尺寸，仅对按钮形式的 Radio 或带有边框的 Radio 有效 medium / small / mini
 * @package Eadmin\component\form\field
 */
class RadioGroup extends Field
{

    protected $name = 'ElRadioGroup';
    //禁用数据
    protected $disabledData = [];

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
                'value'    => $value,
                'label'    => $label,
                'disabled' => $disabled,
            ];
        }
        if ($buttonTheme) {
            $radio = RadioButton::create();
        } else {
            $radio = Radio::create();
        }
        $field = $radio->bindAttr('modelValue');
        $radio->removeBind($field);
        $radio->removeAttr('modelValue');
        $optionBindField = Str::random(30, 3);
        $this->bindValue($options, 'options', $optionBindField);
        $radioOption = $radio
            ->map($options,$optionBindField)
            ->mapAttr('label', 'value')
            ->mapAttr('key', 'value')
            ->mapAttr('slotDefault', 'label')
            ->mapAttr('disabled', 'disabled');
        return $this->content($radioOption);
    }
}
