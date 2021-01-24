<?php


namespace Eadmin\component\basic;


use Eadmin\Admin;
use Eadmin\component\Component;
use Eadmin\component\form\Field;
use Eadmin\component\form\field\Input;
use Eadmin\component\layout\Content;
use Eadmin\form\Form;
use think\app\Url;
use think\facade\Route;
use think\helper\Str;

/**
 * 对话框
 * Class Dialog
 * @package Eadmin\component\basic
 * @method $this width(string $width) 宽度
 * @method $this fullscreen(bool $value=true) 是否为全屏 Dialog
 * @method $this top(string $top) margin-top 值 15vh
 * @method $this modal(bool $value=true) 是否需要遮罩层
 * @method $this appendToBody(bool $value=true) Dialog 自身是否插入至 body 元素上
 * @method $this lockScroll(bool $value=true) 是否在 Dialog 出现时将 body 滚动锁定
 * @method $this openDelay(int $num) Dialog 打开的延时时间，单位毫秒
 * @method $this closeDelay(int $num) Dialog 关闭的延时时间，单位毫秒
 * @method $this closeOnClickModal(bool $value=true) 是否可以通过点击 modal 关闭 Dialog
 * @method $this closeOnPressEscape(bool $value=true) 是否可以通过按下 ESC 关闭 Dialog
 * @method $this showClose(bool $value=true) 是否显示关闭按钮
 * @method $this center(bool $value=true) 是否对头部和底部采用居中布局
 * @method $this destroyOnClose(bool $value=true) 关闭时销毁 Dialog 中的元素
 * @method $this url(string $value) 异步加载数据url
 * @method $this params(array $value)  异步附加请求数据
 */
class Dialog extends Field
{
    protected $setcion;
    protected $name = 'EadminDialog';

    public static function create($content = null, $field = '')
    {
        $self = new self($field, false);
        $self->closeOnClickModal(false);
        $self->appendToBody();
        $self->width('35%');
        if(!is_null($content)){
            $self->content($content, 'reference');
        }
        return $self;
    }

    /**
     * 表单异步加载
     * @param Form $form
     * @return Dialog
     */
    public function form(Form $form){
        $this->url('/eadmin.rest');
        $oarams = array_merge($form->getCallMethod(),$form->getCallParams());
        $this->params($oarams);
        return $this;
    }
    public function reference($content){
        return $this->content($content,'reference');
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
