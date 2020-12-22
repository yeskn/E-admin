<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-04-29
 * Time: 21:52
 */

namespace Eadmin\form;


use Eadmin\View;

class Button extends View
{

    /**
     * Button constructor.
     * @param $text 按钮文字
     * @param string $colorType 颜色类型 primary / success / warning / danger / info / text
     * @param string $size 尺寸 medium / small / mini
     * @param string $icon 图标
     * @param bool $plain 朴素按钮
     */
    public function create($text = '', $colorType = '', $size = 'small', $icon = '', $plain = false)
    {
        $button = new self();
        $button->template = 'button';
        $button->text = $text;
        $button->setAttr('type', $colorType);
        $button->setAttr('size', $size);
        $button->setAttr('icon', $icon);
        $button->setAttr('text', $text);
        if ($plain) {
            $button->setAttr(':plain', 'true');
        }
        return $button;
    }

    /**
     * 创建下拉按钮元素
     * @param $text
     * @param string $icon
     * @param bool $divided
     * @return Button
     */
    public function dropdown($text, $icon = '', $divided = false)
    {
        $button = new self();
        $button->template = 'button';
        $button->text = $text;
        if ($divided) {
            $divided = 'true';
        } else {
            $divided = 'false';
        }
        $button->setAttr('btn-type', 'dropdown');
        $button->setAttr(':divided', $divided);
        $button->setAttr('icon', $icon);
        $button->setAttr('text', $text);
        return $button;
    }

    //禁用状态
    public function disabled()
    {
        $this->setAttr(':disabled', 'true');
        return $this;
    }

    //圆形按钮
    public function circle()
    {
        $this->setAttr(':circle', 'true');
        return $this;
    }

    //圆角按钮
    public function round()
    {
        $this->setAttr(':round', 'true');
        return $this;
    }

    /**
     * 打开窗口 modal弹窗对话框 open新窗口 full全屏弹窗对话框 drawer抽屉
     * @Author: rocky
     * 2019/9/11 10:02
     * @param $url 跳转链接
     * @param $type 打开方式 full全屏,modal弹窗,open内容页,drawer抽屉
     */
    public function href($url, $type = 'open')
    {
        $this->setAttr('url', $url);
        $this->setAttr('open-type', $type);
        $this->setAttr(':table-data-update.sync', 'tableDataUpdate');
        return $this->html();
    }

    /**
     * 复制文本
     * @param $text
     * @return $this
     */
    public function copy($text)
    {
        $this->setAttr('open-type', 'copy');
        $this->setAttr('copy-text', $text);
        return $this->html();
    }

    /**
     * 更新数据
     * @Author: rocky
     * 2019/9/11 10:06
     * @param $id 更新主键条件
     * @param array $updateData 更新数据
     * @param string $url
     * @param $confirm 操作提示
     * @param bool $prompt 输入框模式
     */
    public function save($id, array $data, $url = '', $confirm = '', bool $prompt = false)
    {
        $this->setAttr('pk-id', $id);
        $this->setAttr('update-data', json_encode($data, JSON_UNESCAPED_UNICODE));
        $this->setAttr('open-type', 'update');
        $this->setAttr('url', $url);
        $this->setAttr('confirm', $confirm);
        $this->setAttr(':table-data-update.sync', 'tableDataUpdate');
        $this->setAttr('prompt', $prompt);
        return $this->html();
    }

    /**
     * 批量更新数据
     * @Author: rocky
     * 2019/9/11 10:06
     * @param $id 更新主键条件
     * @param array $updateData 更新数据
     * @param string $url
     * @param string $confirm 操作提示
     * @param bool $prompt 输入框模式
     */
    public function saveAll(array $data, $url, $confirm = '', bool $prompt = false)
    {
        $this->setAttr('update-data', json_encode($data, JSON_UNESCAPED_UNICODE));
        $this->setAttr('open-type', 'updateBatch');
        $this->setAttr('url', $url);
        $this->setAttr('confirm', $confirm);
        $this->setAttr(':table-data-update.sync', 'tableDataUpdate');
        $this->setAttr(':selectionData', 'selectionData');
        $this->setAttr('prompt', $prompt);
        return $this->html();
    }

    /**
     * 删除数据
     * @param $id 更新主键条件
     * @param string $confirm 操作提示
     * @param integer $mode 删除模式：0正常删除，1永久删除，2恢复数据（回收站）
     * @return $this
     */
    public function delete($id, $confirm = '', $mode = 0)
    {
        $this->setAttr('pk-id', $id);
        $this->setAttr('open-type', 'delete');
        $this->setAttr('mode', $mode);
        $this->setAttr('confirm', $confirm);
        $this->setAttr(':tabledata.sync', 'tableData');
        $submitUrl = $this->getRequestUrl();
        $this->setAttr('url', $submitUrl);
        return $this->html();
    }

    /**
     * 上传
     * @param $url 上传的地址
     * @param $name 上传的文件字段名
     * @return string
     */
    public function upload($url, $name = 'file')
    {
        $preg = "/^(https?:)/";
        if (!preg_match($preg, $url)) {
            $url = request()->domain() . '/' . $url;
        }
        $this->setAttr('url', $url);
        $this->setAttr('name', $name);
        list($attrStr, $scriptVar) = $this->parseAttr();
        $html = "<eadmin-upload-button {$attrStr}></eadmin-upload-button>";
        return $html;
    }

    /**
     * 返回html
     * @return string
     */
    protected function html()
    {
        list($attrStr, $scriptVar) = $this->parseAttr();
        $html = "<eadmin-button {$attrStr}></eadmin-button>";
        return $html;
    }
}
