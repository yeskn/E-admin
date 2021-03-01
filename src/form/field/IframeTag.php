<?php


namespace Eadmin\form\field;


use think\facade\Db;
use think\facade\Request;
use think\Model;
use Eadmin\ApiJson;
use Eadmin\form\Field;

class IframeTag extends Field
{
    use ApiJson;

    protected $attrs = [
        'data',
        'multiple',
    ];
    protected $table = null;
    protected $tagField = null;
    protected $multiple = 0;
    protected $url = '';
    protected $text = '';

    /**
     * 多选
     */
    public function multiple()
    {
        $this->setAttr('multiple', true);
        return $this;
    }

    /**
     * 设置数据源
     * @param string $table 表名或模型
     * @param string $tagField 显示标签字段
     * @param string $url url
     * @param string $text 按钮文本
     * @return $this
     */
    public function data($table, $tagField, $url, $text = '请选择')
    {
        $this->setAttr('url', $url);
        $this->setAttr('tag-field', $tagField);
        $this->tagField = $tagField;
        $this->setAttr('text', $text);
        $this->table = $table;
        $submitUrl   = Request::url();
        $this->setAttr('remote-url', $submitUrl);
        return $this;
    }

    //回显数据
    protected function tagData()
    {
        if (Request::method() == 'GET' && Request::has('iframeTagData') && Request::get('field') == $this->field) {
            $ids = Request::get('ids');
            if ($ids) {
                if ($this->table instanceof Model) {
                    $pk   = $this->table->getPk();
                    $data = $this->table->whereIn($pk, $ids)->field("$pk as id,$this->tagField as label")->select()->toArray();
                } else {
                    $pk   = Db::name($this->table)->getPk();
                    $data = Db::name($this->table)->whereIn($pk, $ids)->field("$pk as id,$this->tagField as label")->select()->toArray();
                }
                $this->successCode($data);
            } else {
                $this->successCode();
            }
        }
    }

    public function render()
    {
        $this->tagData();
        $this->setAttr('field', $this->field);
        list($attrStr, $tableScriptVar) = $this->parseAttr();
        $html = "<eadmin-tag-iframe {$attrStr}></eadmin-tag-iframe>";
        return $html;
    }
}
