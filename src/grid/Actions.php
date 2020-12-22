<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-04-18
 * Time: 00:18
 */

namespace Eadmin\grid;


use Eadmin\service\AdminService;

class Actions extends Column
{
    //隐藏详情按钮
    protected $hideDetailButton = false;
    //隐藏编辑按钮
    protected $hideEditButton = false;
    //隐藏删除按钮
    protected $hideDelButton = false;

    protected $closure = null;
    protected $detailButton = '<el-button class="hidden-md-and-down" size="small" icon="el-icon-info" @click="handleDetail(data,index)" data-title="详情">详情</el-button><el-button circle size="mini" class="hidden-md-and-up" icon="el-icon-info" @click="handleDetail(data,index)" data-title="详情"></el-button>';
    protected $editButton = '<el-button class="hidden-md-and-down" type="primary" size="small" icon="el-icon-edit" @click="handleEdit(data,index)" data-title="编辑" >编辑</el-button><el-button circle class="hidden-md-and-up" type="primary" size="mini" icon="el-icon-edit" @click="handleEdit(data,index)" data-title="编辑"></el-button>';
    protected $delButton = '<el-button  class="hidden-md-and-down" type="danger" size="small" icon="el-icon-delete" @click="handleDelete(data,index)" >删除</el-button><el-button  circle class="hidden-md-and-up" type="danger" size="mini" icon="el-icon-delete" @click="handleDelete(data,index)"></el-button>';


    protected $prependArr = [];

    protected $appendArr = [];
    protected $mode = 'button';
    public $row = [];

    public function __construct(string $field = '', string $label = '')
    {
        parent::__construct($field, $label);
        $this->setAttr(':fixed', 'actionFixed');
        $this->setAttr(':width','actionWidth');
    }

    //下拉菜单模式
    public function dropdown()
    {
        $this->mode = 'dropdown';
        $this->detailButton = '<el-dropdown-item icon="el-icon-info" @click.native="handleDetail(data,index)">详情</el-dropdown-item>';
        $this->editButton = '<el-dropdown-item icon="el-icon-edit" @click.native="handleEdit(data,index)">编辑</el-dropdown-item>';
        $this->delButton = '<el-dropdown-item icon="el-icon-delete" @click.native="handleDelete(data,index)">删除</el-dropdown-item>';
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
     * @param $val
     */
    public function prepend($val)
    {
        $this->prependArr[] = $val;
    }

    /**
     * 追加尾部
     * @param $val
     */
    public function append($val)
    {
        $this->appendArr[] = $val;
    }

    /**
     * 设置数据
     * @param $data 行数据
     */
    public function setData($data)
    {
        $this->row = $data;
        if (!is_null($this->closure)) {
            if (!empty($data)) {
                call_user_func_array($this->closure, [$this, $data]);
            }
        }
        $html = '';

        $node = $this->getRequestUrl();
        if (!$this->hideDetailButton && AdminService::instance()->check($node . '/:id.rest', 'get')) {
            $html .= $this->detailButton;
        }
        if (!$this->hideEditButton && AdminService::instance()->check($node . '/:id.rest', 'put')) {
            $html .= $this->editButton;
        }
        if (!$this->hideDelButton && AdminService::instance()->check($node . '/:id.rest', 'delete')) {
            $html .= $this->delButton;
        }
        foreach ($this->prependArr as $val) {
            $html = $val . $html;
        }
        foreach ($this->appendArr as $val) {
            $html .= $val;
        }

        $this->appendArr = [];
        $this->prependArr = [];
        if ($this->mode == 'button') {
            $this->display(function () use ($html) {
                return "<span ref='cellAction' style='white-space: nowrap;'>{$html}</span>";
            });
        } elseif ($this->mode == 'dropdown') {
            $this->display(function () use ($html) {
                return '
<span ref="cellAction" style="white-space: nowrap;"><el-dropdown trigger="click">
  <span class="el-dropdown-link">
  <el-button size="mini">
    操作<i class="el-icon-arrow-down el-icon--right"></i>
  </el-button>    
  </span>
  <el-dropdown-menu slot="dropdown">' . $html . '
  </el-dropdown-menu>
</el-dropdown></span>';
            });
        }
        parent::setData($data);
        $this->mode = 'button';
        $this->hideDetailButton = false;
        $this->hideEditButton = false;
        $this->hideDelButton = false;
    }
}
