<?php


namespace Eadmin\component\form;


use Eadmin\component\basic\Button;
use Eadmin\component\Component;
use Eadmin\form\Form;

/**
 * Class FormAction
 * @package Eadmin\component\form\field
 * @property Form $form
 */
class FormAction extends Component
{
    protected $form;
    //提交按钮
    protected $submitButton;
    //重置按钮
    protected $resetButton;
    //隐藏重置按钮
    protected $hideResetButton = false;
    //隐藏提交按钮
    protected $hideSubmitButton = false;

    public function __construct($form)
    {
        $this->form         = $form;
        $this->submitButton = Button::create('保存')->type('primary');
        $this->resetButton  = Button::create('重置');
    }

    /**
     * 提交按钮
     * @return Button
     */
    public function submitButton()
    {
        return $this->submitButton;
    }

    /**
     * 重置按钮
     * @return Button
     */
    public function resetButton()
    {
        return $this->resetButton;
    }

    /**
     * 隐藏提交按钮
     */
    public function hideSubmitButton()
    {
        $this->hideSubmitButton = true;
    }

    /**
     * 隐藏重置按钮
     */
    public function hideResetButton()
    {
        $this->hideResetButton = true;
    }

    /**
     * 添加左边自定义内容
     * @param string $content
     */
    public function addLeftAction($content)
    {
        $this->form->content($content, 'leftAction');
    }

    /**
     * 添加右边自定义内容
     * @param string $content
     */
    public function addRightAction($content)
    {
        $this->form->content($content, 'rightAction');
    }

    public function render()
    {
        if ($this->hideResetButton) {
            $this->resetButton = null;
        }
        if ($this->hideSubmitButton) {
            $this->submitButton = null;
        }
        $this->form->attr('action', [
            'submit' => $this->submitButton,
            'reset'  => $this->resetButton,
        ]);
    }
}
