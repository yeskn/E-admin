<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-04-18
 * Time: 00:18
 */

namespace Eadmin\grid;

use Eadmin\component\basic\Button;
use Eadmin\component\basic\Confirm;
use Eadmin\component\basic\Dropdown;
use Eadmin\component\basic\DropdownItem;
use Eadmin\component\basic\Html;
use Eadmin\component\Component;
use Eadmin\component\grid\Column;

/**
 * Class Actions
 * @package Eadmin\grid
 * @property Grid $grid
 */
class Actions extends Html
{

    //隐藏详情按钮
    protected $hideDetailButton = false;
    //隐藏编辑按钮
    protected $hideEditButton = false;
    //隐藏删除按钮
    protected $hideDelButton = false;
    protected $closure = null;
    protected $prependArr = [];
    protected $appendArr = [];
    protected $row = [];
    protected $column;
    protected $id;
    protected $grid;
    //是否下拉菜单
    protected $isDropdown = false;
    protected $dropdown;
    protected $editText = '编辑';
    protected $detailText = '详情';
    protected $delText = '删除';

    public function __construct($grid)
    {
        parent::__construct();
        $this->grid = $grid;
        $this->attr('class', 'EadminAction');
        $this->attr('style', ['whiteSpace' => 'nowrap']);
        $this->column = new Column('EadminAction', '', $grid);
        $this->column->fixed('right');
    }

    public function row($data)
    {
        $this->row = $data;
        //如果有id设置id标示
        $pk = $this->grid->drive()->getPk();
        if (isset($data[$pk])) {
            $this->id = $data[$pk];
        }
        //自定义内容显示处理
        if (!is_null($this->closure)) {
            call_user_func_array($this->closure, [$this, $data]);
        }
        //前面追加
        foreach ($this->prependArr as $content) {
            $this->content($content);
        }
        if ($this->isDropdown) {
            $this->actionDropdown();
        } else {
            $this->actionButton();
        }
        //追加尾部
        foreach ($this->appendArr as $content) {
            $this->content($content);
        }
    }

    protected function actionDropdown()
    {
        //是否隐藏详情
        if (!$this->hideDetailButton && !is_null($this->grid->detailAction())) {
            $text = '<i class="el-icon-info"> ' . $this->detailText;
            $detail = $this->grid->detailAction()->detail();
            $action = clone $this->grid->detailAction()->component();
            if ($action instanceof Html) {
                $button = $action->content($text)->redirect("eadmin/{$this->id}.rest", $detail->getCallMethod());
                $this->dropdown->item($button);
            } else {
                $button = $action->title($this->detailText)->bindValue(null, false)->url("/eadmin/{$this->id}.rest")->params(['eadmin_layout' => true] + $detail->getCallMethod());
                $visible = $button->bindAttr('modelValue');
                $this->dropdown->content($button, 'reference');
                $item = $this->dropdown->item($text);
                $item->event('click', [$visible => true]);
            }
        }
        //是否隐藏编辑
        if (!$this->hideEditButton && !is_null($this->grid->formAction())) {
            $text = '<i class="el-icon-edit"> ' . $this->editText;
            $form = $this->grid->formAction()->form()->renderable();
            $action = clone $this->grid->formAction()->component();
            if ($action instanceof Html) {
                $button = $action->content($text)->redirect("eadmin/{$this->id}/edit.rest", ['eadmin_description' => $this->editText] + $form->getCallMethod());
                $this->dropdown->item($button);
            } else {
                $button = $action->bindValue(null, false)->title($this->editText)->url("/eadmin/{$this->id}/edit.rest")->params($form->getCallMethod());
                $visible = $button->bindAttr('modelValue');
                $this->dropdown->content($button, 'reference');
                $item = $this->dropdown->item($text);
                $item->event('click', [$visible => true]);
            }

        }
        //是否隐藏删除
        if (!$this->hideDelButton) {
            $text = '<i class="el-icon-help"> ' . $this->delText;
            $params = $this->grid->getCallMethod();
            if (request()->has('eadmin_deleted')) {
                $text = '<i class="el-icon-help"> 恢复数据 ';
                $url = "/eadmin/batch.rest";
                $confirm = Confirm::create($text)->message('确认恢复？')
                    ->url($url)
                    ->type('warning')
                    ->params($params + ['delete_time' => null, 'eadmin_ids' => [$this->id]])
                    ->method('put');
                $this->dropdown->item($confirm);
                $params['trueDelete'] = true;
                $text = '<i class="el-icon-delete"> 彻底删除';
            }
            $url = '/eadmin/' . $this->id . '.rest';
            $confirm = Confirm::create($text)->message('确认删除？')
                ->url($url)
                ->type('error')
                ->params($params)
                ->method('DELETE');
            $this->dropdown->item($confirm);
        }
        $this->content($this->dropdown);
    }

    protected function actionButton()
    {
        //是否隐藏详情
        if (!$this->hideDetailButton && !is_null($this->grid->detailAction())) {
            $button = Button::create($this->detailText)
                ->size('small')
                ->icon('el-icon-info');
            $detail = $this->grid->detailAction()->detail();
            $action = clone $this->grid->detailAction()->component();
            if ($action instanceof Html) {
                $button = $action->content($button)->redirect("eadmin/{$this->id}.rest", $detail->getCallMethod());
            } else {
                $button = $action->bindValue(null, false)->title($this->detailText)->reference($button)->url("/eadmin/{$this->id}.rest")->params(['eadmin_layout' => true] + $detail->getCallMethod());
            }
            $this->content($button);
        }
        //是否隐藏编辑
        if (!$this->hideEditButton && !is_null($this->grid->formAction())) {
            $button = Button::create($this->editText)
                ->type('primary')
                ->size('small')
                ->icon('el-icon-edit');
            $form = $this->grid->formAction()->form();
            $action = clone $this->grid->formAction()->component();
            if ($action instanceof Html) {
                $button = $action->content($button)->redirect("eadmin/{$this->id}/edit.rest", ['eadmin_description' => $this->editText] + $form->getCallMethod());
            } else {
                $button = $action->bindValue(null, false)->title($this->editText)->reference($button)->url("/eadmin/{$this->id}/edit.rest")->params($form->getCallMethod());
            }
            $this->content($button);
        }
        //是否隐藏删除
        if (!$this->hideDelButton) {
            $text = $this->delText;
            $params = $this->grid->getCallMethod();
            if (request()->has('eadmin_deleted')) {
                $this->content(
                    Button::create('恢复数据')
                        ->size('small')
                        ->icon('el-icon-s-help')
                        ->save($params + ['delete_time' => null, 'eadmin_ids' => [$this->id]], "/eadmin/batch.rest", '确认恢复?')->method('put')
                );
                $params['trueDelete'] = true;
                $text = '彻底删除';
            }
            $url = '/eadmin/' . $this->id . '.rest';
            $this->content(
                Button::create($text)
                    ->type('danger')
                    ->size('small')
                    ->icon('el-icon-delete')
                    ->confirm('确认删除？', $url)
                    ->type('error')
                    ->params($params)
                    ->method('DELETE')
            );
        }
    }

    /**
     * @return Dropdown
     */
    public function dropdown()
    {
        $this->isDropdown = true;
        if (is_null($this->dropdown)) {
            $this->dropdown = Dropdown::create(Button::create('操作 <i class="el-icon-arrow-down">')->size('mini'));
        }
        return $this->dropdown;
    }

    public function column()
    {
        return $this->column;
    }

    public function __get($name)
    {
        return $this->row[$name];
    }

    public function setClosure(\Closure $closure)
    {
        $this->closure = $closure;
    }

    //隐藏详情按钮
    public function hideDetail()
    {
        $this->hideDetailButton = true;
    }

    //隐藏编辑按钮
    public function hideEdit()
    {
        $this->hideEditButton = true;
    }

    //隐藏删除按钮
    public function hideDel()
    {
        $this->hideDelButton = true;
    }

    /**
     * 前面追加
     * @param mixed $val
     */
    public function prepend($val)
    {
        $this->prependArr[] = $val;
        return $this;
    }

    /**
     * 追加尾部
     * @param mixed $val
     */
    public function append($val)
    {
        $this->appendArr[] = $val;
        return $this;
    }

}
