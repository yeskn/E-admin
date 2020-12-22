<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-04-12
 * Time: 16:20
 */

namespace Eadmin;


use think\facade\App;
use think\helper\Str;

abstract class View
{
    //模板变量
    protected $vars = [];

    //设置的组件属性
    protected $attrVars = [];

    //模板
    protected $template;

    //定义组件属性
    protected $attrs = [];

    //解析完成的已设置组件属性
    protected $attrArr = [];
    protected $attr = [];
    //js变量
    protected $scriptVar = [];

    protected $varMark = null;
    //唯一标记
    protected $tag = null;
    //js
    protected $script = '';
    /**
     * 设置变量
     */
    protected function setVar($key, $val)
    {
        $this->vars[$key] = $val;
    }

    /**
     * 解析属性
     */
    protected function parseAttr()
    {

        $this->scriptVar = [];

        foreach ($this->attrVars as $var => $value) {
            $attr = $this->varAttrName($var);
            if (in_array($attr, $this->attrs)) {
                $this->attrArr[] = ":{$attr}=\"{$var}\"";
                if (is_numeric($this->attrVars[$var])) {
                    $this->scriptVar[] = "{$var}:{$this->attrVars[$var]}";
                } elseif (is_bool($this->attrVars[$var])) {
                    if ($this->attrVars[$var]) {
                        $this->scriptVar[] = "{$var}:true";
                    } else {
                        $this->scriptVar[] = "{$var}:false";
                    }
                } elseif (is_array($this->attrVars[$var])) {
                    $this->scriptVar[] = "{$var}:" . json_encode($this->attrVars[$var]) . "";
                } else {
                    $this->scriptVar[] = "{$var}:\"{$this->attrVars[$var]}\"";
                }
            } else {
                $this->attrArr[] = "{$attr}='{$value}'";
            }

        }
        $attrStr = implode(' ', array_unique($this->attrArr));
        $dataStr = implode(',', $this->scriptVar);
        return [$attrStr, $dataStr];
    }

    /**
     * 获取定义属性
     * @return array
     */
    public function getAttrArr($name = '')
    {
        if (empty($name)) {
            return $this->attrArr;
        } else {
            if (isset($this->attrArr[$name])) {
                return $this->attrArr[$name];
            } else {
                return null;
            }
        }

    }
    /**
     * 获取定义模板变量
     * @return array
     */
    public function getvars($name = ''){
        if (empty($name)) {
            return $this->vars;
        } else {
            if (isset($this->vars[$name])) {
                return $this->vars[$name];
            } else {
                return null;
            }
        }

    }
    /**
     * 获取定义变量属性
     * @return array
     */
    public function getVarArr($name = '')
    {
        if (empty($name)) {
            return $this->attrVars;
        } else {
            if (isset($this->attrVars[$name])) {
                return $this->attrVars[$name];
            } else {
                return null;
            }
        }

    }
    /**
     * 获取定义的script变量
     * @return array
     */
    public function getScriptVar()
    {
        return $this->scriptVar;
    }

    private function varAttrName($var)
    {
        $var = str_replace($this->varMark, '', $var);
        $var = Str::snake($var);
        $var = str_replace('_', '-', $var);
        $class = basename(str_replace('\\', '/', lcfirst(get_class($this))));
        $class = Str::snake($class);
        $class = str_replace('_', '-', strtolower($class));
        $var = str_replace($class . ':', ':', $var);
        $var = str_replace($class . '@', '@', $var);
        $var = str_replace($class . '-', '', $var);
        $var = str_replace($this->varMark, '', $var);
        return $var;
    }

    /**
     * 解析带-的变量转换驼峰命名
     * @param $attr 属性名
     * @return string
     */
    private function attrVarName($attr)
    {
        if (is_null($this->varMark)) {
            $this->varMark = 'buildVar' . mt_rand(100000, 999999);
        }
        $varName = str_replace('-', '_', $attr);
        $class = basename(str_replace('\\', '/', get_class($this)));
        $var = lcfirst($class) . Str::studly($varName) . $this->varMark;
        return $var;
    }
    public function setField($field){
        $this->field = $field;
    }

    /**
     * 设置属性变量
     * @param $name
     * @param $value
     */
    public function setAttr($name, $value)
    {
        $var = $this->attrVarName($name, $value);
        $this->attrVars[$var] = $value;
        $this->attr[$name] = $value;
        return $this;
    }
    public function removeAttr($name){
        $var = $this->attrVarName($name);
        unset($this->attrVars[$var],$this->attr[$name]);
        return $this;
    }
    public function getAttr($name=''){
        if(empty($name)){
            return $this->attr;
        }else{
            return $this->attr[$name] ?? '';
        }

    }
    /**
     * 获取标记
     */
    public function getTag(){
        if(is_null($this->tag)){
            $class = basename(str_replace('\\', '/', get_class($this)));
            $this->tag = $class .'tag'. mt_rand(10000000, 99999999);
        }
        return $this->tag;
    }
    /**
     * 设置标记
     * @param $name
     * @return $this
     */
    public function tag($name){
        $this->tag = $name;
        return $this;
    }
    public function getScript(){
        return $this->script;
    }
    protected function getRequestUrl(){
        $requestUrl = substr(request()->baseUrl(),1);
        $requestUrl = preg_replace("/(\/[\d]*\/edit\.rest)$/U",'',$requestUrl);
        $requestUrl = str_replace(['/create.rest','.rest',],['','',''],$requestUrl);
        if(!empty(request()->action())){
            $requestUrl = str_replace('/'.request()->action(),'',$requestUrl);
        }
        return $requestUrl;
    }
    /**
     * 返回视图
     * @return string
     */
    public function render()
    {
        $path = __DIR__ . '/view/' . $this->template . '.vue';
        if (file_exists($path)) {
            $content = file_get_contents($path);
        } else {
            abort(999,$path.'不存在');
        }
        return \think\facade\View::display($content, array_merge($this->attrVars, $this->vars));
    }

}
