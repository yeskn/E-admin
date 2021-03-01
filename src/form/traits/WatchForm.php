<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-10-06
 * Time: 10:26
 */

namespace Eadmin\form\traits;

use think\facade\Request;
use Eadmin\ApiJson;

/**
 * form数据监听
 * Trait Watch
 * @package Eadmin\form\traits
 */
trait WatchForm
{
    use ApiJson;

    protected $watchs = [];
    protected $watchJs = '';

    /**
     * 设置监听数据方法
     * @param array $data
     */
    public function watch(array $data)
    {
        $this->watchs = $data;
    }

    protected function initWatchJs()
    {
        $fields = array_keys($this->watchs);
        $js     = '';
        foreach ($fields as $field) {
            $js .= $this->watchRequstJs($field, "this.form.{$field}", "this.form.{$field}");

        }
        return $js;
    }

    protected function watchRequstJs($field, $newVal = 'newVal', $oldValue = 'oldValue')
    {
        $submitUrl = $this->getRequestUrl();
        $js        = <<<EOF
            var method,url = '{$submitUrl}'
            if(this.form.id == undefined){
                url = url+'.rest'
                method = 'post'
            }else{
                url = url +'/'+this.form.id+'.rest'
                method = 'put'
            }
            if(this.watchOldValue['{$field}'] && JSON.stringify({$newVal}) === JSON.stringify(this.watchOldValue['{$field}'])){
                return
            }
            if({$oldValue} instanceof Array || {$oldValue} instanceof Object){
                 this.watchOldValue['{$field}'] = JSON.parse(JSON.stringify({$oldValue}))
                 {$oldValue} = this.watchOldValue['{$field}']
            }
            this.\$request({
                  url:url,
                  method:method,
                  data:{
                      field:'{$field}',
                      submitFromMethod:'{$this->extraData['submitFromMethod']}',
                      newVal:{$newVal},
                      oldValue:{$oldValue},
                      form:this.form,
                      eadmin_form_watch:true
                  }
            }).then(res=>{
                res.data.showField.forEach(field=>{
                    this.deleteArr(this.formItemTags,this.formTags[field])
                })
                res.data.hideField.forEach(field=>{
                   this.formItemTags.splice(-1,0,this.formTags[field])
                  
                })
                let formData = res.data.form
                for(field in formData){
                    if(field == '{$field}' && formData[field] != {$newVal}){
                       this.form[field] = formData[field]
                    }else if(field != '{$field}'){
                       this.form[field] = formData[field]
                    }
                }
            })
           
EOF;
        return $js;
    }

    /**
     * 生成监听js
     * @return string
     */
    protected function createWatchJs()
    {
        $fields = array_keys($this->watchs);
        foreach ($fields as $field) {
            $requestJs     = $this->watchRequstJs($field);
            $this->watchJs .= <<<EOF
    'form.{$field}': {
         deep:true,
         handler: function(newVal,oldValue) {
            {$requestJs}
         }
     },
EOF;
        }
        return $this->watchJs;
    }

    public function setWatchData($field, $value)
    {
        $this->watchData[$field] = $value;
    }

    /**
     * 监听数据回调
     */
    protected function watchCall()
    {
        if (Request::has('eadmin_form_watch')) {
            $data    = Request::post();
            $watch   = new \Eadmin\form\Watch($data['form']);
            $closure = $this->watchs[$data['field']];
            call_user_func_array($closure, [$data['newVal'], $watch, $data['oldValue']]);
            $this->successCode([
                'form'      => $watch->get(),
                'showField' => $watch->getShowField(),
                'hideField' => $watch->getHideField(),
            ]);
        }
    }
}
