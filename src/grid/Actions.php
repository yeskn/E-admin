<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-04-18
 * Time: 00:18
 */

namespace Eadmin\grid;

use Eadmin\component\basic\Button;
use Eadmin\component\basic\Router;
use Eadmin\component\Component;
use Eadmin\component\grid\Column;

/**
 * Class Actions
 * @package Eadmin\grid
 * @property Grid $grid
 */
class Actions extends Component
{
    protected $name = 'html';
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
    public function __construct($grid)
    {
        $this->grid = $grid;
        $this->column = new Column('EadminAction','',$grid);
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
        //是否隐藏详情
        if (!$this->hideDetailButton) {
            $button = Button::create('详情')
                ->size('small')
                ->icon('el-icon-info');
            $detail = $this->grid->detailAction()->detail()->renderable();
            $action = clone $this->grid->detailAction()->component();
            if($action instanceof Router){
                $button = $action->content($button)->to("/eadmin/{$this->id}.rest",$detail->getCallMethod());
            }else{
                $button = $action->bindValue(null,false)->reference($button)->url("/eadmin/{$this->id}.rest")->params($detail->getCallMethod());
            }
            $this->content($button);
        }
        //是否隐藏编辑
        if (!$this->hideEditButton) {
            $button = Button::create('编辑')
                ->type('primary')
                ->size('small')
                ->icon('el-icon-edit');
            $form = $this->grid->formAction()->form()->renderable();
            $action = clone $this->grid->formAction()->component();
            if($action instanceof Router){
                $button = $action->content($button)->to("/eadmin/{$this->id}/edit.rest",$form->getCallMethod());
            }else{
                $button = $action->bindValue(null,false)->title($form->bind('eadmin_title'))->reference($button)->url("/eadmin/{$this->id}/edit.rest")->params($form->getCallMethod());
            }
            $this->content($button);
        }
        //是否隐藏删除
        if (!$this->hideDelButton) {
            $url = '/eadmin/' . $this->id.'.rest';
            $this->content(
                Button::create('删除')
                ->type('danger')
                ->size('small')
                ->icon('el-icon-delete')
                ->confirm('确认删除？', $url)
                    ->type('error')
                    ->params($this->grid->getCallMethod())
                    ->method('DELETE')
            );
        }
        //追加尾部
        foreach ($this->appendArr as $content) {
            $this->content($content);
        }
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
