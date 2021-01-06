<?php


namespace Eadmin\component\basic;


use Eadmin\component\Component;
use Eadmin\component\form\Field;
use Eadmin\component\form\field\Input;
use Eadmin\component\layout\Content;
use think\helper\Str;

/**
 * 对话框
 * Class Dialog
 * @package Eadmin\component\basic
 * @method $this width(string $width) 宽度
 * @method $this fullscreen(bool $bool) 是否为全屏 Dialog
 * @method $this top(string $top) margin-top 值 15vh
 * @method $this modal(bool $bool) 是否需要遮罩层
 * @method $this appendToBody(bool $bool) Dialog 自身是否插入至 body 元素上
 * @method $this lockScroll(bool $bool) 是否在 Dialog 出现时将 body 滚动锁定
 * @method $this openDelay(int $num) Dialog 打开的延时时间，单位毫秒
 * @method $this closeDelay(int $num) Dialog 关闭的延时时间，单位毫秒
 * @method $this closeOnClickModal(bool $bool) 是否可以通过点击 modal 关闭 Dialog
 * @method $this closeOnPressEscape(bool $bool) 是否可以通过按下 ESC 关闭 Dialog
 * @method $this showClose(bool $bool) 是否显示关闭按钮
 * @method $this center(bool $bool) 是否对头部和底部采用居中布局
 * @method $this destroyOnClose(bool $bool) 关闭时销毁 Dialog 中的元素
 */
class Dialog extends Field
{
    protected $setcion;
    protected $name = 'EadminDialog';

    public static function create($content = null, $field = '')
    {
        $self = new self($field, false);
        $self->closeOnClickModal(false);
        $self->width('35%');
        $self->content($content, 'reference');
        return $self;
    }
    /**
     * 标题
     * @param string $content
     * @return Dialog
     */
    public function title($content){
        return $this->content($content,'title');
    }
}
