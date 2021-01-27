<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-04-18
 * Time: 20:32
 */

namespace Eadmin\form;


use think\exception\HttpResponseException;
use think\facade\Db;
use think\facade\Log;
use think\facade\Request;
use think\facade\Validate;
use think\helper\Str;
use think\Model;
use think\model\relation\BelongsTo;
use think\model\relation\BelongsToMany;
use think\model\relation\HasMany;
use think\model\relation\HasOne;
use think\model\relation\MorphMany;
use think\model\relation\MorphOne;
use Eadmin\facade\Component;
use Eadmin\form\field\Cascader;
use Eadmin\form\field\Input;
use Eadmin\form\field\Radio;
use Eadmin\form\field\Select;
use Eadmin\form\field\Tree;
use Eadmin\form\traits\RequestForm;
use Eadmin\form\traits\ValidatorForm;
use Eadmin\form\traits\WatchForm;
use Eadmin\model\SystemConfig;
use Eadmin\View;

/**
 * Class Form
 * @package Eadmin\form
 * @method \Eadmin\form\field\Input text($field, $label) 文本输入框
 * @method \Eadmin\form\field\Input hidden($field) 隐藏输入框
 * @method \Eadmin\form\field\Input textarea($field, $label) 多行文本输入框
 * @method \Eadmin\form\field\Input password($field, $label) 密码输入框
 * @method \Eadmin\form\field\Input number($field, $label) 数字输入框
 * @method \Eadmin\form\field\Select select($field, $label) 下拉选择器
 * @method \Eadmin\form\field\Radio radio($field, $label) 单选框
 * @method \Eadmin\form\field\Switchs switch ($field, $label) switch开关
 * @method \Eadmin\form\field\Tree tree($field, $label) 树形
 * @method \Eadmin\form\field\DateTime datetime($field, $label) 日期时间
 * @method \Eadmin\form\field\DateTime datetimeRange($startFiled, $endField, $label) 日期时间范围时间
 * @method \Eadmin\form\field\DateTime dateRange($startFiled, $endField, $label) 日期范围时间
 * @method \Eadmin\form\field\DateTime timeRange($startFiled, $endField, $label) 日期范围时间
 * @method \Eadmin\form\field\DateTime date($field, $label) 日期
 * @method \Eadmin\form\field\DateTime dates($field, $label) 多选日期
 * @method \Eadmin\form\field\DateTime time($field, $label) 时间
 * @method \Eadmin\form\field\DateTime year($field, $label) 年
 * @method \Eadmin\form\field\DateTime month($field, $label) 月
 * @method \Eadmin\form\field\Checkbox checkbox($field, $label) 多选框
 * @method \Eadmin\form\field\File file($field, $label) 文件上传
 * @method \Eadmin\form\field\File image($field, $label) 图片上传
 * @method \Eadmin\form\field\Editor editor($field, $label) 富文本编辑器
 * @method \Eadmin\form\field\Slider slider($field, $label) 滑块
 * @method \Eadmin\form\field\Color color($field, $label) 颜色选择器
 * @method \Eadmin\form\field\Rate rate($field, $label) 评分组件
 * @method \Eadmin\form\field\Cascader cascader(...$field, $label) 级联选择器
 * @method \Eadmin\form\field\Transfer transfer($field, $label) 穿梭框
 * @method \Eadmin\form\field\Icon icon($field, $label) 图标选择器
 * @method \Eadmin\form\field\IframeTag iframeTag($field, $label) 弹窗选择框
 * @method \Eadmin\form\field\Map map($lng, $lat, $address, $label) 高德地图
 * @method \Eadmin\form\field\TableText tableText($field, $label) 表格编辑
 */
class Form extends View
{
    use WatchForm, ValidatorForm,RequestForm;

    protected $attrs = [
        'model',
        'rules',
        'inline',
        'hide-required-asterisk',
        'show-message',
        'inline-message',
        'status-icon',
        'validate-on-rule-change',
        'disabled',
        'unlink-panels',
    ];
    //表单元素组件
    protected $formItem = [];


    protected $tabs = null;


    protected $scriptArr = [];
    //当前模型
    protected $model;

    //表单附加参数
    protected $extraData = [];

    //保存前回调
    protected $beforeSave = null;

    //保存后回调
    protected $afterSave = null;

    protected $data = [];
    protected $formData = ['empty' => 0];

    //是否编辑表单
    protected $isEdit = false;

    protected $layoutTags = [];
    protected $formTags = [];

    protected $formWhenItem = [];

    //表单互动事件js
    protected $interactChangeJs = null;
    protected $interactChangeInitJs = null;
    //表字段
    protected $tableFields = [];

    protected $saveData = [];

    protected $hasManyRelation = null;

    protected $hasManyRowData = [];

    protected $hasManyData = [];

    protected $hasManyIndex = 0;
    protected $title = '';
    protected $pkField = 'id';
    //保存修改成功后跳转的url
    protected $redirectUrl = '';
    protected static $extend = [];

    public function __construct($model = null)
    {
        if ($model instanceof Model) {
            $this->model = $model;
            $this->tableFields = $this->model->getTableFields();
            $this->pkField = $this->model->getPk();
        }
        $this->template = 'form';
        $this->labelPosition('right');
        $this->setAttr(':label-position', "labelPosition");
        $this->addExtraData([
            'submitFromMethod' => $this->action(),
        ]);
        if (request()->has($this->pkField)) {
            $this->edit(request()->param($this->pkField));
        }
    }

    /**
     * 设置保存修改成功后跳转的url
     * @param string $url
     */
    public function redirectUrl($url)
    {
        $this->redirectUrl = $url;
    }

    /**
     * 修改成功后后退
     * @return string
     */
    public function redirectBack()
    {
        $this->redirectUrl = 'back';
    }

    /**
     * 设置提交按钮对齐方式
     * @param string $align
     */
    public function sumbitAlign($align = 'right')
    {
        $this->setVar('sumbitAlign', $align);
    }

    public function prependSubmitExtend($entend)
    {
        if (class_exists($entend)) {
            $field = new $entend('', '', []);
            $entend = $field->render();
            $this->scriptArr = array_merge($this->scriptArr, $field->getScriptVar());
        }
        $this->setVar('prependSubmitExtend', $this->getvars('prependSubmitExtend').$entend);

    }

    public function appendSubmitExtend($entend)
    {
        if (class_exists($entend)) {
            $field = new $entend('', '', []);
            $entend = $field->render();
            $this->scriptArr = array_merge($this->scriptArr, $field->getScriptVar());
        }
        $this->setVar('appendSubmitExtend',$this->getvars('prependSubmitExtend'). $entend);
    }

    /**
     * 获取修改成功后跳转的url
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    /**
     * 设置主键字段
     * @param string $field
     */
    public function setPkField($field)
    {
        $this->pkField = $field;
    }

    /**
     * 批量添加表单
     * @param \Closure $closure
     */
    public function batch(\Closure $closure)
    {
        array_push($this->formItem, ['type' => 'batchAdd', 'closure' => $closure]);
    }


    /**
     * 一对多
     * @param string $label 标签
     * @param string $relationMethod 关联方法
     * @param \Closure $closure
     */
    public function hasMany($label, $relationMethod, \Closure $closure)
    {
        if (method_exists($this->model, $relationMethod)) {
            if ($this->model->$relationMethod() instanceof HasMany) {
                array_push($this->formItem, ['type' => 'hasMany', 'label' => $label, 'relationMethod' => $relationMethod, 'closure' => $closure]);
            } else {
                abort(500, '关联方法不是一对多');
            }
        } else {
            abort(500, '无效关联方法');
        }
    }

    /**
     * 表单选项卡标签页
     * @param string $label 标签
     * @param \Closure $closure 回调
     * @param string $position 选项卡所在位置 top/right/bottom/left
     * @param string $type 风格类型 ''/card/border-card
     * @return $this
     */
    public function tab($label, \Closure $closure, $position = 'top', $type = '')
    {
        array_push($this->formItem, ['type' => 'tabs', 'style' => $type, 'position' => $position, 'label' => $label, 'closure' => $closure]);
        return $this;
    }

    /**
     * 布局行
     * @param \Closure $closure
     * @param string $title 标题
     * @param int $gutter 栅格间隔
     * @return $this
     */
    public function row(\Closure $closure, $title = '',$gutter = 0)
    {
        array_push($this->formItem, ['type' => 'layout', 'title' => $title, 'closure' => $closure,'gutter'=>$gutter]);
        return $this;
    }

    /**
     * 对齐方式
     * @param string $position top,left,right
     * @param int $width 宽度
     */
    public function labelPosition($position, $width = 120)
    {
        $this->removeAttr(':label-position');
        $this->setAttr('label-width', $width . 'px');
        $this->setAttr('label-position', $position);
    }

    /**
     * 更新数据
     * @param int $id  主键id
     * @param array $data 更新数据
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function update($id, $data)
    {
        $res = $this->autoSave($data, $id);
        return $res;
    }

    /**
     * 数据保存
     */
    public function save($data)
    {
        $res = $this->autoSave($data);
        return $res;
    }

    protected function autoSave($data, $id = null)
    {
        $this->watchCall();
        $res = false;
        $this->saveData = $data;
        $this->parseFormItem();
        $this->checkRule($this->saveData);
        Db::startTrans();
        try {
            //保存前回调
            if (!is_null($this->beforeSave)) {
                $beforePost = call_user_func($this->beforeSave, $this->saveData, $this->data);
                if (is_array($beforePost)) {
                    $this->saveData = array_merge($this->saveData, $beforePost);
                }
            }

            if (is_null($this->model)) {
                foreach ($this->saveData as $name => $value) {
                    if ($name == 'empty' || $name == 'submitFromMethod') {
                        continue;
                    }
                    $sysconfig = SystemConfig::where('name', $name)->find();
                    if ($sysconfig) {
                        $sysconfig->value = $value;
                        $res = $sysconfig->save();
                    } else {
                        $res = SystemConfig::create([
                            'name' => $name,
                            'value' => $value,
                        ]);
                    }
                }
            } else {
                //批量添加
                if (!empty($this->saveData['eadminBatchAdd'])) {
                    $this->model->saveAll($this->saveData['eadminBatchAdd']);
                    Db::commit();
                    return true;
                }
                if (!is_null($id)) {
                    $this->data = $this->model->where($this->pkField, $id)->find();
                    $this->model = $this->model->where($this->pkField, $id)->find();
                } elseif (isset($this->saveData[$this->pkField])) {
                    $isExists = Db::name($this->model->getTable())->where($this->pkField, $this->saveData[$this->pkField])->find();
                    if ($isExists) {
                        $this->data = $this->model->where($this->pkField, $this->saveData[$this->pkField])->find();
                        $this->model = $this->model->where($this->pkField, $this->saveData[$this->pkField])->find();
                    }
                }
                $res = $this->model->save($this->saveData);
                foreach ($this->saveData as $field => $value) {
                    if (method_exists($this->model, $field)) {
                        //多对多关联保存
                        if ($this->model->$field() instanceof BelongsToMany) {
                            $relationData = $value;
                            $this->model->$field()->detach();
                            if (is_string($relationData)) {

                                $relationData = explode(',', $relationData);
                                $relationData = array_filter($relationData);
                            }
                            if (count($relationData) > 0) {
                                $this->model->$field()->saveAll($relationData);
                            }
                        } elseif ($this->model->$field() instanceof HasOne || $this->model->$field() instanceof BelongsTo || $this->model->$field() instanceof MorphOne) {
                            $relationData = $this->saveData[$field];
                            if (is_null($id) || empty($this->data->$field)) {
                                $this->model->$field()->save($relationData);
                            } else {
                                $this->data->$field->save($relationData);
                            }

                        } elseif ($this->model->$field() instanceof HasMany || $this->model->$field() instanceof MorphMany) {
                            $realtionUpdateIds = array_column($value, 'id');
                            if (!empty($this->data->$field)) {
                                $deleteIds = $this->data->$field->column('id');

                                if (is_array($realtionUpdateIds)) {
                                    $deleteIds = array_diff($deleteIds, $realtionUpdateIds);
                                }
                                if (count($deleteIds) > 0) {
                                    $res = $this->model->$field()->whereIn($this->pkField, $deleteIds)->delete();
                                }
                            }
                            foreach ($value as $key => &$val) {
                                $val['sort'] = $key;
                            }
                            if (!empty($value)) {
                                $this->model->$field()->saveAll($value);
                            }
                        }
                    }
                }
            }
            //保存回后调
            if (!is_null($this->afterSave)) {
                call_user_func_array($this->afterSave, [$this->saveData, $this->model]);
            }
            Db::commit();
        } catch (HttpResponseException $e) {
            throw $e;
        } catch (\Exception $e) {
            Db::rollback();
            if (env('APP_DEBUG')) {
                throw $e;
            } else {
                Log::error($e->getMessage());
            }
            $res = false;
        }
        return $res;
    }

    /**
     * 保存后回调
     * @param \Closure $closure
     */
    public function saved(\Closure $closure)
    {
        $this->afterSave = $closure;
    }

    /**
     * 保存前回调
     * @param \Closure $closure
     */
    public function saving(\Closure $closure)
    {
        $this->beforeSave = $closure;
    }


    /**
     * 数据编辑
     * @param int $id 主键id
     * @return $this
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function edit($id)
    {
        $this->data = $this->model->where($this->pkField, $id)->find();
        $this->formData[$this->pkField] = $id;

        $this->isEdit = true;
        return $this;
    }

    /**
     * 当前表单是否编辑模式
     * @return bool
     */
    public function isEdit()
    {
        return $this->isEdit;
    }

    /**
     * 添加表单元素
     * @param $class 组件类
     * @param $field 字段
     * @param $label 标签
     * @return mixed
     */
    protected function formItem($name, $field, $arguments)
    {
        $label = array_pop($arguments);
        $class = "Eadmin\\form\\field\\";
        $inputs = [
            'text',
            'textarea',
            'number',
            'password',
            'hidden',
        ];
        $dates = [
            'date',
            'dates',
            'time',
            'year',
            'month',
            'datetime',
            'datetimeRange',
            'dateRange',
            'timeRange',
        ];
        if (in_array($name, $inputs)) {
            $class .= 'Input';
        } elseif (in_array($name, $dates)) {

            $class .= 'DateTime';
        } elseif ($name == 'switch') {
            $class .= 'Switchs';
        } elseif ($name == 'image') {
            $class .= 'File';
        } else {
            $class .= ucfirst($name);
        }
        if (array_key_exists($name, self::$extend)) {
            $class = self::$extend[$name];
        }
        $formItem = new $class($field, $label, $arguments);
        switch ($name) {
            case 'image':
                $formItem->displayType('image')->imageExt()->size(120, 120)->isUniqidmd5();
                break;
            case 'number':
                $formItem->setAttr('type', 'number');
                break;
            case 'password':
                $formItem->password();
                break;
            case 'hidden':
                $formItem->hidden();
                break;
            case 'textarea':
                $formItem->setAttr('type', 'textarea');
                break;
            case 'datetime':
                $formItem->setType('datetime');
                break;
            case 'datetimeRange':
                $formItem->setType('datetime')->range();
                break;
            case 'dateRange':
                $formItem->setType('date')->range();
                break;
            case 'timeRange':
                $formItem->setType('time')->range();
                break;
            case 'time':
                $formItem->setType('time');
                break;
            case 'year':
                $formItem->setType('year');
                break;
            case 'month':
                $formItem->setType('month');
                break;
            case 'dates':
                $formItem->setType('dates');
                break;
        }
        $this->formItem[] = $formItem;
        return $formItem;

    }

    /**
     * 解析formItem
     * @param string $formItemHtml
     * @param string $whenTag 当前when条件标记
     * @param string $whenNest 当前when嵌套条件
     * @return string
     */
    protected function parseFormItem($formItemHtml = '', $whenTag = '',$whenNest = [])
    {
        foreach ($this->formItem as $key => $formItem) {
            if (is_array($formItem)) {
                $formItemArr = array_slice($this->formItem, $key + 1);
                $this->formItem = [];
                call_user_func_array($formItem['closure'], [$this]);
                switch ($formItem['type']) {
                    case 'batchAdd':
                        $this->hasManyRelation = 'eadminBatchAdd';
                        $manyData = $this->getData($this->hasManyRelation);
                        $formItemHtml .= "<div v-for='(manyItem,manyIndex) in form.{$this->hasManyRelation}' :key='manyIndex'>";
                        $formItemHtml = $this->parseFormItem($formItemHtml);
                        $encodeManyData = urlencode(json_encode($this->hasManyRowData, JSON_UNESCAPED_UNICODE));
                        $formItemHtml .= "<el-form-item><el-button type='primary' plain @click=\"addManyData('{$this->hasManyRelation}','{$encodeManyData}')\">新增</el-button><el-button type='danger' v-show='form.{$this->hasManyRelation}.length > 1' @click=\"removeManyData('{$this->hasManyRelation}',manyIndex)\">移除</el-button><el-button @click=\"handleUp('{$this->hasManyRelation}',manyIndex)\" v-show='form.{$this->hasManyRelation}.length > 1 && manyIndex > 0'>上移</el-button><el-button v-show='form.{$this->hasManyRelation}.length > 1 && manyIndex < form.{$this->hasManyRelation}.length-1' @click=\"handleDown('{$this->hasManyRelation}',manyIndex)\">下移</el-button></el-form-item>";
                        $formItemHtml .= "</div><el-divider></el-divider>";
                        if (empty($manyData)) {
                            $this->formData[$this->hasManyRelation][] = $this->hasManyRowData;
                        } else {
                            $this->formData[$this->hasManyRelation] = $manyData;
                        }
                        $this->hasManyRelation = null;
                        break;
                    case 'hasMany':
                        $field = new Field('', '', []);
                        $this->layoutTags[$whenTag][] = $field->getTag();

                        $this->hasManyRelation = $formItem['relationMethod'];
                        $this->hasManyData = $this->getData($this->hasManyRelation);
                        $formItemHtml .= "<div v-show=\"formItemTags.indexOf('{$field->getTag()}0') === -1\"><el-divider content-position='left'>{$formItem['label']}</el-divider>";
                        $formItemHtml .= "<div v-for='(manyItem,manyIndex) in form.{$this->hasManyRelation}' :key='manyIndex'>";
                        $formItemHtml = $this->parseFormItem($formItemHtml);
                        $encodeManyData = urlencode(json_encode($this->hasManyRowData, JSON_UNESCAPED_UNICODE));
                        $formItemHtml .= "<el-form-item><el-button type='primary' plain @click=\"addManyData('{$this->hasManyRelation}','{$encodeManyData}')\" size='mini' v-if='form.{$this->hasManyRelation}.length-1 == manyIndex'>新增</el-button><el-button size='mini' type='danger' v-show='form.{$this->hasManyRelation}.length > 1' @click=\"removeManyData('{$this->hasManyRelation}',manyIndex)\">移除</el-button><el-button @click=\"handleUp('{$this->hasManyRelation}',manyIndex)\" v-show='form.{$this->hasManyRelation}.length > 1 && manyIndex > 0' size='mini'>上移</el-button><el-button size='mini' v-show='form.{$this->hasManyRelation}.length > 1 && manyIndex < form.{$this->hasManyRelation}.length-1' @click=\"handleDown('{$this->hasManyRelation}',manyIndex)\">下移</el-button></el-form-item><el-divider v-if='form.{$this->hasManyRelation}.length-1 != manyIndex'></el-divider>";
                        $formItemHtml .= "</div><el-divider></el-divider></div>";
                        if (count($this->hasManyData) == 0) {
                            $this->formData[$this->hasManyRelation][] = $this->hasManyRowData;
                        } else {
                            $this->formData[$this->hasManyRelation] = $this->hasManyData;
                        }
                        $this->hasManyRelation = null;
                        break;
                    case 'layout':
                        if (empty($formItem['title'])) {
                            $title = '';
                        } else {
                            $title = "<div><h4 style='color: #999999;font-size: 14px'>{$formItem['title']}</h4></div>";
                        }
                        $field = new Field('', '', []);
                        $this->layoutTags[$whenTag][] = $field->getTag();
                        $formItemHtml .= "<div v-show=\"formItemTags.indexOf('{$field->getTag()}0') === -1\">" . $title . '<el-row :gutter="'.$formItem['gutter'].'">' . $this->parseFormItem('', $whenTag,$whenNest) . '</el-row></div>';
                        break;
                    case 'tabs':
                        if (is_null($this->tabs)) {
                            $this->tabs = new Tabs();
                            $this->tabs->tempHtml = $formItemHtml;
                        }
                        if (!empty($formItem['style'])) {
                            $this->tabs->setAttr('type', $formItem['style']);
                        }
                        $this->tabs->setAttr('tab-position', $formItem['position']);
                        $this->tabs->push($formItem['label'], $this->parseFormItem());
                        $formItemHtml = $this->tabs->tempHtml . $this->tabs->render();
                        $this->scriptArr = array_merge($this->scriptArr, $this->tabs->getScriptVar());
                        break;
                }
                $this->formItem = $formItemArr;
            } else {
                if ($formItem instanceof Tree) {
                    $this->setVar('styleHorizontal', $formItem->styleHorizontal());
                }
                if (empty($formItem->label)) {
                    $labelWidth = "label-width='0px'";
                } else {
                    $labelWidth = '';
                }
                if (is_null($this->hasManyRelation)) {
                    $valdateField = str_replace('.', '_', $formItem->field);
                    $formItemTmp = "<el-form-item {$labelWidth} v-show=\"formItemTags.indexOf('{$formItem->getTag()}0') === -1\" ref='{$formItem->field}' :error='validates.{$valdateField}ErrorMsg' label='{$formItem->label}' prop='{$formItem->field}' :rules='formItemTags.indexOf(\"{$formItem->getTag()}0\") === -1 ? {$formItem->rule}:{required:false}'>%s<span style='font-size: 12px'>{$formItem->helpText}</span></el-form-item>";
                    //是否多个字段解析
                    if (count($formItem->fields) > 1) {
                        $fieldValue = [];
                        foreach ($formItem->fields as $field) {
                            $fieldValue[] = $this->getData($field);
                        }
                        $fieldValue = array_filter($fieldValue);
                    } else {
                        $fieldValue = $this->getData($formItem->field);
                    }
                    //级联选择器一对多关系单独解析获取值
                    if ($formItem instanceof Cascader) {
                        $relation = $formItem->getRelation();
                        if ($relation) {
                            $fieldValue = [];
                            $manyDatas = $this->getData($relation);
                            if ($manyDatas) {
                                foreach ($manyDatas as $key => $manyData) {
                                    foreach ($formItem->fields as $field) {
                                        if (!empty($manyData[$field])) {
                                            $fieldValue[$key][] = $manyData[$field];
                                        }
                                    }
                                }
                            }
                            $formItem->setAttr('v-model', 'form.' . $relation);
                            $formItem->setField($relation);
                        }
                    }
                    //设置默认值
                    if ($this->isEdit) {
                        if (is_null($fieldValue)) {
                            $this->setData($formItem->field, $formItem->defaultValue);
                        } else {
                            if ($formItem instanceof Select && !$formItem->inOptions($fieldValue)) {
                                $this->setData($formItem->field, '');
                            } else {
                                $this->setData($formItem->field, $fieldValue);
                            }
                        }
                    } else {
                        if (is_array($fieldValue)) {
                            if (empty($fieldValue) && count($formItem->fields) > 1) {
                                $this->setData($formItem->field, $formItem->defaultValue);
                            } else {
                                $this->setData($formItem->field, $fieldValue);
                            }
                        } else {

                            $this->setData($formItem->field, $formItem->defaultValue);
                        }
                    }
                    //设置固定值
                    if (!is_null($formItem->value)) {
                        $this->setData($formItem->field, $formItem->value);
                    }
                    $this->interactChangeInitJs .= $formItem->getWhenInitJs();
                } else {
                    //一对多解析
                    $formItem->setAttr('@blur', "clearValidateArr(\"{$this->hasManyRelation}\",\"{$formItem->field}\",manyIndex)");
                    $formItem->setAttr('v-model', 'manyItem.' . $formItem->field);
                    $valdateField = str_replace('.', '_', $this->hasManyRelation . '.' . $formItem->field);
                    $formItemTmp = "<el-form-item {$labelWidth} v-show=\"formItemTags.indexOf('{$formItem->getTag()}' + manyIndex) === -1\" ref='{$formItem->field}' :error=\"validates['{$valdateField}'+manyIndex+'ErrorMsg']\"  label='{$formItem->label}' :prop=\"'{$this->hasManyRelation}.' + manyIndex + '.{$formItem->field}'\" :rules='formItemTags.indexOf(\"{$formItem->getTag()}\" + manyIndex) === -1 ? {$formItem->rule}:{required:false}'>%s<span style='font-size: 12px'>{$formItem->helpText}</span></el-form-item>";
                    //设置默认值
                    if ($this->isEdit) {
                        if (count($formItem->getFileds()) > 1) {
                            $itemFields = $formItem->getFileds();
                            $field = $formItem->getField();
                            foreach ($this->hasManyData as &$val) {
                                $value = [];
                                foreach ($itemFields as $key => $itemField) {
                                    if (isset($val[$itemField])) {
                                        $value[] = $val[$itemField];
                                        unset($val[$itemField]);
                                        $val[$field] = $value;
                                    }
                                }
                            }
                        } else {
                            $this->hasManyRowData[$formItem->field] = $formItem->defaultValue;
                        }
                    } else {
                        $this->hasManyRowData[$formItem->field] = $formItem->defaultValue;
                    }
                    //设置固定值
                    if (!is_null($formItem->value)) {
                        $this->hasManyRowData[$formItem->field] = $formItem->value;
                    }
                    //一对多表单互动事件初始化值
                    if ($formItem instanceof Radio) {
                        $manyData = $this->getData($this->hasManyRelation);
                        $interactChangeJs = <<<EOF
                        this.form.{$this->hasManyRelation}.forEach((item,index)=>{
                            this.interactChange(item.{$formItem->field},'{$formItem->getTag()}',index,'when')
                        })
EOF;
                        $this->interactChangeInitJs .= $interactChangeJs;
                    }
                    $formItem->setField("{$this->hasManyRelation}.$formItem->field");
                }
                //合并表单验证规则
                list($rule, $msg) = $formItem->paseRule($formItem->createRules);
                $this->setRules($rule, $msg, 1);
                list($rule, $msg) = $formItem->paseRule($formItem->updateRules);
                $this->setRules($rule, $msg, 2);
                $this->interactChangeJs .= $formItem->changeJs;
                $render = $formItem->render();
                if (isset($this->saveData[$formItem->field]) && is_array($this->saveData[$formItem->field])) {
                    $field = $formItem->field;
                    $itemSaveValues = $this->saveData[$field];
                    $itemFields = $formItem->getFileds();
                    if (method_exists($this->model, $field) && $this->model->$field() instanceof HasMany && $formItem instanceof Cascader) {
                        //针对级联选择器多选解析保存一对多数据
                        $this->saveData[$field] = [];
                        foreach ($itemSaveValues as $index => $itemSaveValue) {
                            $saveHanyData = [];
                            foreach ($itemSaveValue as $key => $value) {
                                if (isset($itemFields[$key])) {
                                    $saveHanyData[$itemFields[$key]] = $value;
                                }
                            }
                            $this->saveData[$field][] = $saveHanyData;
                        }
                    } else {
                        if (count($itemFields) > 1) {
                            foreach ($itemFields as $key => $itemField) {
                                if (isset($itemSaveValues[$key])) {
                                    $this->saveData[$itemField] = $itemSaveValues[$key];
                                }
                            }
                        }
                    }

                } elseif (count($formItem->getFileds()) > 1 && strpos($formItem->field, '.') !== false) {
                    //多个字段,关联方法解析
                    $itemFields = $formItem->getFileds();
                    list($reliatonMethod, $field) = explode('.', $formItem->field);
                    if (isset($this->saveData[$reliatonMethod])) {
                        foreach ($this->saveData[$reliatonMethod] as &$val) {
                            $itemSaveValues = $val[$field];
                            foreach ($itemFields as $key => $itemField) {
                                if (isset($itemSaveValues[$key])) {
                                    $val[$itemField] = $itemSaveValues[$key];
                                }
                            }
                        }
                    }
                }
                if ($formItem instanceof Input && $formItem->isHidden()) {
                    $formItemTmp = $render;
                } else {
                    $formItemTmp = sprintf($formItemTmp, $render);
                }
                $this->scriptArr = array_merge($this->scriptArr, $formItem->getScriptVar());
                if (!empty($formItem->md)) {
                    $formItemHtml .= sprintf($formItem->md, $formItemTmp);
                } else {
                    $formItemHtml .= $formItemTmp;
                }
                $this->formTags[$formItem->field] = $formItem->getTag() . '0';
                $this->script($formItem->getScript());
                $whenTags = [];
                $whenTagsAll = [];
                if (!empty($whenTag)) {
                    $this->formWhenItem[$whenTag][] = $formItem;
                }
                //when显示元素解析，item互动事件显示隐藏
                foreach ($formItem->getWhenItem() as $whenIndex => $whenItem) {

                    $formItemArr = array_slice($this->formItem, $key + 1);
                    $this->formItem = [];
                    if (is_array($whenItem['value'])) {
                        $indexTag = implode('', $whenItem['value']);
                    } else {
                        $indexTag = $whenItem['value'];
                    }
                    $whenTagNow = $formItem->getTag() . $indexTag;
                    call_user_func_array($whenItem['closure'], [$this]);
                    $whenNestNext[$indexTag] = $whenNest;
                    $whenNestsNext = $whenNestNext[$indexTag];
                    array_push($whenNestsNext,['field'=>$formItem->field,'value'=>$whenItem['value']]);
                    $formItemHtml = $this->parseFormItem($formItemHtml, $whenTagNow,$whenNestsNext);
                    $formWhenItem = [];
                    if (isset($this->formWhenItem[$whenTagNow])) {
                        $formWhenItem = array_merge($this->formWhenItem[$whenTagNow], $this->formItem);
                    }
                    foreach ($formWhenItem as $whenformItem) {
                        $whenTags[$indexTag]['tag'][] = $whenformItem->getTag();
                        $whenTags[$indexTag]['value'] = $whenItem['value'];
                        $whenTagsAll[] = $whenformItem->getTag();
                    }
                    if (isset($this->layoutTags[$whenTagNow])) {
                        foreach ($this->layoutTags[$whenTagNow] as $tag) {
                            $whenTags[$indexTag]['tag'][] = $tag;
                            $whenTags[$indexTag]['value'] = $whenItem['value'];
                            $whenTagsAll[] = $tag;
                        }
                    }
                }

                if (count($whenTags) > 0) {
                    $hideTagsAllArr = array_map(function ($v) {
                        return "'{$v}' + manyIndex";
                    }, $whenTagsAll);
                    $HideTagsAllJs = implode(',', $hideTagsAllArr);
                    $this->interactChangeJs .= "if((tag + manyIndex) === ('{$formItem->getTag()}' + manyIndex)){this.formItemTags.splice(-1,0,{$HideTagsAllJs});}" . PHP_EOL;
                }
                foreach ($whenTags as $whenVal) {
                    $tags = $whenVal['tag'];
                    $value = $whenVal['value'];
                    $defalutHideTagsArr = array_map(function ($v) {
                        return "'{$v}' + manyIndex";
                    }, $tags);
                    $defalutHideTags = implode(',', $defalutHideTagsArr);
                    $hideTagsJs = '';
                    foreach ($defalutHideTagsArr as $tag) {
                        $hideTagsJs .= "this.deleteArr(this.formItemTags,{$tag});";
                    }
                    $whenVals = [];
                    if (is_array($value)) {
                        $whenVals = $value;
                        $indexTag = implode('', $value);
                    } else {
                        $whenVals[] = $value;
                        $indexTag = $value;
                    }
                    foreach ($whenVals as $val) {
                        $whenNestJs = '';
                        if(isset($whenNestNext[$indexTag]) > 0){
                            foreach ($whenNestNext[$indexTag] as $nest){
                                $whenNestJs .= "(this.form.{$nest['field']} == '{$nest['value']}') && ";
                            }
                        }
                        $this->interactChangeJs .= "if({$whenNestJs}val == '{$val}' && (tag + manyIndex) === ('{$formItem->getTag()}' + manyIndex) && changeType == 'when'){{$hideTagsJs};console.log(this.formItemTags)}" . PHP_EOL;
                    }
                }

            }
        }
        return $formItemHtml;
    }

    /**
     * 设置js
     * @param $js
     */
    public function script($js)
    {
        $this->script .= $js . PHP_EOL;
    }

    protected function setData($field, $val)
    {
        if ($this->model instanceof Model) {
            if (strpos($field, '.')) {
                list($relation, $field) = explode('.', $field);
                $this->formData[$relation][$field] = $val;
            } else {
                $this->formData[$field] = $val;
            }
        } else {
            if (empty($val)) {
                $val = SystemConfig::where('name', $field)->value('value');
                if (is_null($val)) {
                    $val = '';
                } else if (is_numeric($val) && strpos($val, '.') === false) {
                    $val = (int)$val;
                }
                $this->formData[$field] = $val;
            } else {
                $this->formData[$field] = $val;
            }
        }
    }

    /**
     * 获取模型当前数据
     * @Author: rocky
     * 2019/8/22 14:56
     * @return array|mixed
     */
    public function getData($field = null, $data = null)
    {
        if (is_null($data)) {
            $data = $this->data;
        }
        if (is_null($field)) {
            return $this->data;
        } else {
            if (method_exists($this->model, $field)) {
                if ($this->model->$field() instanceof BelongsToMany) {
                    if (empty($data->$field)) {
                        $relationData = null;
                    } else {
                        $relationData = $data->$field;
                    }
                    if (is_null($relationData)) {
                        $val = [];
                    } else {
                        $val = $relationData->column($this->pkField);
                    }
                    return $val;
                } elseif ($this->model->$field() instanceof HasMany) {
                    if (empty($data->$field)) {
                        return [];
                    } else {
                        return $data->$field;
                    }
                }
            }
            foreach (explode('.', $field) as $f) {
                if (isset($data[$f])) {
                    $data = $data[$f];
                } else {
                    $data = null;
                }
            }
            return $data;
        }
    }


    /**
     * 设置标题
     * @param $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
        $this->setVar('title', $title);
    }
    public function title(){
        return $this->title;
    }
    /**
     * 设置提交按钮文字
     * @return string
     */
    public function setSubmitText($text)
    {
        $this->setVar('submitText', $text);
    }

    /**
     * 隐藏重置按钮
     */
    public function hideResetButton()
    {
        $this->setVar('hideResetButton', true);
    }

    /**
     * 隐藏提交按钮
     */
    public function hideSubmitButton()
    {
        $this->setVar('hideSubmitButton', true);
    }

    /**
     * 添加表单附加参数
     * @param array $data
     */
    public function addExtraData(array $data)
    {
        $this->extraData = array_merge($this->extraData, $data);
        return $this;
    }


    public function view()
    {
        if (isset($this->extraData[$this->pkField])) {
            $this->edit($this->extraData[$this->pkField]);
        }
        $formItem = $this->parseFormItem();
        $scriptStr = implode(',', array_unique($this->scriptArr));
        list($attrStr, $formScriptVar) = $this->parseAttr();
        if (!empty($scriptStr)) {
            $formScriptVar = $scriptStr . ',' . $formScriptVar;
        }
        $this->script($this->initWatchJs());
        $this->formData = array_merge($this->formData, $this->extraData);
        $this->setVar('formData', json_encode($this->formData, JSON_UNESCAPED_UNICODE));
        $this->setVar('formTags', json_encode($this->formTags, JSON_UNESCAPED_UNICODE));
        $this->setVar('script', $this->script);
        $this->setVar('attrStr', $attrStr);
        $this->setVar('interactChangeJs', $this->interactChangeJs);
        $this->setVar('interactChangeInitJs', $this->interactChangeInitJs);
        $this->setVar('watchJs', $this->createWatchJs());
        $this->setVar('formItem', $formItem);
        $this->setVar('submitUrl',$this->requestUrl());
        $this->setVar('formScriptVar', $formScriptVar);
        if (Request::has('build_dialog')) {
            $this->setVar('title', '');
            Component::view($this->render());
        }
        return $this->render();
    }

    /**
     * 组件扩展
     * @param $name 名称
     * @param $class 组件类
     */
    public static function extend($name, $class)
    {
        self::$extend[$name] = $class;
    }

    public function __call($name, $arguments)
    {
        return $this->formItem($name, $arguments[0], array_slice($arguments, 1));
    }
}
