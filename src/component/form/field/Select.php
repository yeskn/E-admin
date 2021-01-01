<?php


namespace Eadmin\component\form\field;


use Eadmin\component\form\Field;

/**
 * 选择器
 * Class Select
 * @link https://element-plus.gitee.io/#/zh-CN/component/input-number
 * @method $this multiple(bool $bool) 是否多选
 * @method $this size(string $size) 输入框尺寸 	medium / small / mini
 * @method $this clearable(bool $bool) 是否可以清空选项
 * @method $this collapseTags(bool $bool) 多选时是否将选中值按文字的形式展示
 * @method $this multipleLimit(int $num) 多选时用户最多可以选择的项目数，为 0 则不限制
 * @method $this autocomplete(string $complete) select input 的 autocomplete 属性
 * @method $this placeholder(string $text) 占位符
 * @method $this filterable(bool $bool) 是否可搜索
 * @method $this allowCreate(bool $bool) 是否允许用户创建新条目，需配合 filterable 使用
 * @method $this remote(bool $bool) 是否为远程搜索
 * @method $this loading(bool $bool) 是否正在从远程获取数据
 * @method $this loadingText(string $text) 远程加载时显示的文字
 * @method $this noMatchText(string $text) 搜索条件无匹配时显示的文字，也可以使用 #empty 设置
 * @method $this noDataText(string $text) 选项为空时显示的文字，也可以使用 #empty 设置
 * @method $this popperClass(string $text) Select 下拉框的类名
 * @method $this reserveKeyword(bool $bool) 多选且可搜索时，是否在选中一个选项后保留当前的搜索关键词
 * @method $this defaultFirstOption(bool $bool) 在输入框按下回车，选择第一个匹配项。需配合 filterable 或 remote 使用
 * @method $this popperAppendToBody(bool $bool) 是否将弹出框插入至 body 元素。在弹出框的定位出现问题时，可将该属性设置为 false
 * @method $this automaticDropdown(bool $bool) 对于不可搜索的 Select，是否在输入框获得焦点后自动弹出选项菜单
 * @method $this clearIcon(string $icon) 自定义清空图标的类名
 * @package Eadmin\component\form\field
 */
class Select extends Field
{
    protected $name = 'ElSelect';
}