<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-10-06
 * Time: 10:35
 */

namespace Eadmin\form\traits;


use think\exception\HttpResponseException;
use think\facade\Validate;
use think\model\relation\HasMany;

trait ValidatorForm
{
    //创建验证规则
    protected $createRules = [
        'rule' => [],
        'msg' => [],
    ];
    //更新验证规则
    protected $updateRules = [
        'rule' => [],
        'msg' => [],
    ];
    /**
     * 设置表单验证规则
     * @Author: rocky
     * 2019/8/9 10:45
     * @param $rule 验证规则
     * @param $msg 验证提示
     * @param int $type 1新增，2更新
     */
    public function setRules($rule, $msg, $type)
    {
        switch ($type) {
            case 1:
                $this->createRules['rule'] = array_merge($this->createRules['rule'], $rule);
                $this->createRules['msg'] = array_merge($this->createRules['msg'], $msg);
                break;
            case 2:
                $this->updateRules['rule'] = array_merge($this->updateRules['rule'], $rule);
                $this->updateRules['msg'] = array_merge($this->updateRules['msg'], $msg);
                break;
        }
    }

    /**
     * 验证表单规则
     * @param $datas
     */
    public function checkRule($datas)
    {
        if ($this->isEdit) {
            //更新
            $validate = Validate::rule($this->updateRules['rule'])->message($this->updateRules['msg']);
            $rules = $this->updateRules['rule'];
        } else {
            //新增
            $validate = Validate::rule($this->createRules['rule'])->message($this->createRules['msg']);
            $rules = $this->createRules['rule'];
        }
        foreach ($datas as $field => $data) {
            if (method_exists($this->model, $field) && $this->model->$field() instanceof HasMany) {
                $validateFields = [];
                $removeFields = [];
                $manyValidate = clone $validate;
                foreach ($rules as $key => $rule) {
                    if (strstr($key, $field . '.')) {
                        $validateFields[] = $key;
                        $removeFields[$key] = true;
                    }
                }
                if ($validateFields) {
                    foreach ($data as $index => $value) {
                        $valdateData[$field] = $value;
                        $result = $manyValidate->only($validateFields)->batch(true)->check($valdateData);;
                        if (!$result) {
                            throw new HttpResponseException(json(['code' => 422, 'message' => '表单验证失败', 'data' => $manyValidate->getError(), 'index' => (string)$index]));
                        }
                    }
                }
                $validate->remove($removeFields);
            }
        }
        $result = $validate->batch(true)->check($datas);
        if (!$result) {
            throw new HttpResponseException(json(['code' => 422, 'message' => '表单验证失败', 'data' => $validate->getError()]));
        }
    }
}
