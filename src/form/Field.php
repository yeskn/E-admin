<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-04-21
 * Time: 19:24
 */

namespace Eadmin\form;


use Eadmin\facade\Component;
use Eadmin\View;
use think\helper\Str;
class Field extends View
{
    //字段
    public $field = '';

    public $fields = [];

    //标签
    public $label = '';

    //验证规则
    public $rule = [];

    //占位栅格数
    public $md = 0;

    //输入框inline
    protected $inline = '';

    //缺省默认值
    public $defaultValue = '';

    //设置值
    public $value = null;

    //创建验证规则
    public $createRules = [];

    //更新验证规则
    public $updateRules = [];

    //提示帮助文本
    public $helpText = '';

    protected $whenItem = [];
    public $changeJs = '';
    /**
     * Input constructor.
     * @param $field 字段
     * @param $label 标签
     */
    public function __construct($field, $label, $arguments = [])
    {
        $this->field = $field;
        $this->fields = $arguments;
        array_unshift($this->fields, $this->field);
        $this->label = $label;
        $this->rule = json_encode([], JSON_UNESCAPED_UNICODE);
        if(!empty($field)){
            $this->setAttr('v-model', 'form.' . $field);
        }
        $this->setAttr('placeholder', '请输入' . $label);
    }

    public function getField()
    {
        $fields = explode('.', $this->field);
        return end($fields);
    }

    public function getFileds()
    {
        return $this->fields;
    }

    /**
     * 禁用
     */
    public function disabled()
    {
        $this->setAttr('disabled', true);
        return $this;
    }

    /**
     * 提示帮助文本
     * @param $text
     * @return $this
     */
    public function help($text)
    {
        $this->helpText = $text;
        return $this;
    }

    /**
     * 缺省默认值
     * @param $value 值
     * @return $this
     */
    public function default($value)
    {
        $this->defaultValue = $value;
        return $this;
    }

    /**
     * 设置值
     * @param $value 值
     * @return $this
     */
    public function value($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * 必填
     * @param bool $bool true判断不为空且大于0
     * @return $this
     */
    public function required($bool = false)
    {
        $this->rule(['require'=>$this->label.'不能为空']);
        if($bool){
            $this->rule(['gt:0'=>$this->label.'不能为空']);
        }
        $this->rule = json_encode([['required' => true, 'message' => $this->label.'不能为空', 'trigger' => 'change']], JSON_UNESCAPED_UNICODE);
        return $this;
    }

    /**
     * 条件必填
     * @param $field 字段
     * @param $val 条件值
     * @return $this
     */
    public function requiredIf($field,$val)
    {
        $this->rule(["requireIf:{$field},{$val}"=>$this->label.'不能为空']);
        $this->rule = json_encode([['required' => true, 'message' => $this->label.'不能为空', 'trigger' => 'blur']], JSON_UNESCAPED_UNICODE);
        return $this;
    }
    /**
     * 输入框inline
     */
    public function inline($span = 4)
    {
        $this->inline = "<el-row><el-col :span='{$span}' :xs='24' :sm='24' :md='{$span}'>%s</el-col></el-row>";
        return $this;
    }

    /**
     * 输入框占位提示文本
     */
    public function placeholder($text)
    {
        $this->setAttr('placeholder', $text);
        return $this;
    }

    /**
     * 占位栅格数，24栏占满
     * @param $num 数量
     * @return $this
     */
    public function md($num = 3)
    {
        $this->md = "<el-col :xs='24' :sm='24' :md='{$num}' :span='{$num}'>%s</el-col>";
        return $this;
    }

    /**
     * 表单新增更新验证规则
     * @Author: rocky
     * 2019/8/9 10:50
     * @param $rule 验证规则
     */
    public function rule(array $rule)
    {
        $this->createRules = array_merge($this->createRules, $rule);
        $this->updateRules = array_merge($this->updateRules, $rule);
        return $this;
    }

    /**
     * 表单新增验证规则
     * @Author: rocky
     * 2019/8/9 10:50
     * @param $rule 验证规则
     */
    public function createRule(array $rule)
    {
        $this->createRules = array_merge($this->createRules, $rule);
        return $this;
    }

    /**
     * 表单更新验证规则
     * @Author: rocky
     * 2019/8/9 10:50
     * @param $rule 验证规则
     */
    public function updateRule(array $rule)
    {
        $this->updateRules = array_merge($this->updateRules, $rule);
        return $this;
    }

    /**
     * 生成验证规则
     * @param $rules
     * @return array
     */
    public function paseRule($rules)
    {
        $ruleMsg = [];
        $rule = [];
        foreach ($rules as $key => $value) {
            if (strpos($key, ':') !== false) {
                $msgKey = $this->field . '.' . substr($key, 0, strpos($key, ':'));
            } else {
                $msgKey = $this->field . '.' . $key;

            }
            $ruleMsg[$msgKey] = $value;
            $rule[] = $key;
        }
        $resRule = [
            $this->field => $rule
        ];
        return [$resRule, $ruleMsg];
    }

    /**
     * 条件显示
     * @param mixed ...$conditon
     * @return $this
     */
    public function when(...$conditon)
    {
        $this->setAttr('@change', "interactChangeInit");
        if (count($conditon) == 3) {
            list($val, $operator, $closure) = $conditon;
        } elseif (count($conditon) == 2) {
            $operator = '=';
            list($val, $closure) = $conditon;
        }
        $this->whenItem[] = [
            'value' => $val,
            'operator' => $operator,
            'closure' => $closure,
        ];
        return $this;
    }
    public function getWhenInitJs(){
        if(count($this->whenItem) > 0){
            return "this.interactChange(this.form.{$this->field},'{$this->getTag()}',0,\"when\")" . PHP_EOL;
        }
        return '';
    }
    public function getWhenItem()
    {
        return $this->whenItem;
    }
    protected function buildComponent($component)
    {
        $key = Str::random(10,0);

        list($attrStr, $tableScriptVar) = $this->parseAttr();
        $this->scriptVar[] = "$key : () => new Promise(resolve => {
                            resolve(this.\$splitCode(decodeURIComponent('" . rawurlencode($component) . "')))
                        })";
        return "<component :is=\"{$key}\" {$attrStr}></component>";
    }

}
