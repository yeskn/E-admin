<?php


namespace Eadmin\component\form\field;


use Eadmin\component\form\Field;

/**
 * 级联选择器
 * Class Cascader
 * @link https://element-plus.gitee.io/#/zh-CN/component/cascader
 * @method $this options(array $option) 可选项数据源，键名可通过 Props 属性配置
 * @method $this props($option) 配置选项，具体见下表
 * @method $this size(string $size) 尺寸 medium / small / mini
 * @method $this placeholder(string $text) 输入框占位文本
 * @method $this clearable(bool $value=true) 是否支持清空选项
 * @method $this showAllLevels(bool $value=true) 输入框中是否显示选中值的完整路径
 * @method $this collapseTags(bool $value=true) 多选模式下是否折叠Tag
 * @method $this separator(string $symbol) 选项分隔符
 * @method $this filterable(bool $value=true) 是否可搜索选项
 * @method $this filterMethod($method) 自定义搜索逻辑，第一个参数是节点node，第二个参数是搜索关键词keyword，通过返回布尔值表示是否命中 function(node, keyword)
 * @method $this debounce(int $num) 搜索关键词输入的去抖延迟，毫秒
 * @method $this popperClass(string $class) 自定义浮层类名
 * @package Eadmin\component\form\field
 */
class Cascader extends Field
{
    protected $name = 'ElCascader';
}