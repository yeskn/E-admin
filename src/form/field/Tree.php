<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-05-05
 * Time: 11:53
 */

namespace Eadmin\form\field;

use Eadmin\form\Field;

/**
 * 树形组件
 * Class Tree
 * @package Eadmin\form
 */
class Tree extends Field
{
    protected $html = '<el-tree ref="tree" @check="handleCheckChange" %s></el-tree>';
    protected $styleHorizontal = '';
    protected $attrs = [
        'data',
        'props',
        'show-checkbox',
        'default-expand-all',
        'default-checked-keys'
    ];

    public function __construct($field, $label, $arguments = [])
    {
        parent::__construct($field, $label, $arguments);
        $this->setAttr('default-expand-all', true);
        $this->setAttr('show-checkbox', true);
        $this->setAttr('field', $field);
        $this->setAttr('props', [
            'children' => 'children',
            'label' => 'label',
        ]);
    }

    /**
     * 设置选中
     * @param $fieldKey 选中对比字段
     * @param array $data 选中数据
     */
    public function setChecked($fieldKey, $data = [])
    {
        $this->setAttr('node-key', $fieldKey);
        $this->setAttr('default-checked-keys', $data);
        return $this;
    }

    /**
     * 设置树形数据
     * @param $data
     */
    public function data($data)
    {
        $this->setAttr('data', $data);
        return $this;
    }

    /**
     * 横行排版
     */
    public function horizontal()
    {
        $this->styleHorizontal = '.el-tree-node__content{
        margin-bottom:10px;
    }
    .el-tree-node__content span.levelname{
        float:left;

    }
    .el-tree-node__children .el-tree-node__children .el-tree-node__content{
        float:left;

    }';
        $this->setVar('styleHorizontal', $this->styleHorizontal);
        return $this;
    }

    public function styleHorizontal()
    {
        return $this->styleHorizontal;
    }

    /**
     * 返回视图
     * @return string
     */
    public function render()
    {
        list($attrStr, $scriptVar) = $this->parseAttr();
        $this->html = sprintf($this->html, $attrStr);
        return $this->html;
    }
}
