<?php


namespace Eadmin\component\basic;


use Eadmin\component\Component;
use Eadmin\component\form\Field;
use Eadmin\component\form\field\Input;
use Eadmin\component\layout\Content;
use think\helper\Str;

/**
 * 抽屉
 * Class Drawer
 * @package Eadmin\component\basic
 * @method $this size(string $width) 宽度
 * @method $this fullscreen(bool $bool) 是否为全屏 Dialog
 * @method $this top(string $top) margin-top 值 15vh
 * @method $this direction(string $direction) Drawer 打开的方向 rtl / ltr / ttb / btt
 * @method $this modal(bool $bool) 是否需要遮罩层
 * @method $this closeOnClickModal(bool $bool) 是否可以通过点击 modal 关闭 Dialog
 * @method $this appendToBody(bool $bool) Dialog 自身是否插入至 body 元素上
 * @method $this closeOnPressEscape(bool $bool) 是否可以通过按下 ESC 关闭 Dialog
 * @method $this showClose(bool $bool) 是否显示关闭按钮
 * @method $this withHeader(bool $bool) 控制是否显示 header 栏, 默认为 true
 * @method $this destroyOnClose(bool $bool) 关闭时销毁 Dialog 中的元素
 */
class Drawer extends Field
{
    protected $setcion;
    protected $name = 'EadminDrawer';

    public static function create($content = null, $field = '')
    {

        $self = new self($field, false);
        $self->content($content, 'reference');
        return $self;
    }

    /**
     * 标题
     * @param string $content
     * @return Drawer
     */
    public function title($content)
    {
        return $this->content($content, 'title');
    }

    public function content($content, $name = 'default')
    {
        if ($content instanceof Component) {
            $content->attr('eadminDrawerVisible', $this->bindAttr('modelValue'));
        }
        $this->attr('drawerContent', $content);
        return parent::content($content, $name);
    }
}
