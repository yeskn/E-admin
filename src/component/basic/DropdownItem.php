<?php


namespace Eadmin\component\basic;

/**
 * Class DropdownItem
 * @package Eadmin\component\basic
 * @method $this disabled(bool $bool = true) 禁用
 * @method $this divided(bool $bool = true) 显示分割线
 * @method $this icon(string $value) 图标类名
 * @method $this url(string $value) ajax请求url
 * @method $this method(string $value) ajax请求method get / post /put / delete
 * @method $this params(array $value) 提交ajax参数
 */
class DropdownItem extends Component
{
    protected $name = 'EadminDropdownItem';
    protected $dropdown;

    public function dropdown(Dropdown $dropdown)
    {
        $this->dropdown = $dropdown;
        return $this->dropdown;
    }

    /**
     * 模态对话框
     * @return Dialog
     */
    public function dialog()
    {
        $dialog  = Dialog::create();
        $visible = $dialog->bindAttr('modelValue');
        $this->event('click', [$visible => true]);
        $this->dropdown->content($dialog, 'reference');
        return $dialog;
    }

    /**
     * 保存数据
     * @param array $data
     * @param mixed $url
     * @param string $confirm
     * @return $this|Confirm
     */
    public function save(array $data, $url, $confirm = '')
    {
        if (empty($confirm)) {
            $this->url($url)->params($data);
        } else {
            $confirm       = Confirm::create($this->content['default'][0])
                ->message($confirm)->url($url)->params($data)->type('warning');
            $this->content = [];
            $this->content($confirm);
        }
        return $this;
    }
}
