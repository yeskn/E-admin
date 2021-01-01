<?php


namespace Eadmin\component\form\field;


use Eadmin\component\form\Field;

/**
 * 上传
 * Class Upload
 * @link https://element-plus.gitee.io/#/zh-CN/component/upload
 * @method $this headers($object) 设置上传的请求头部
 * @method $this action(string $action) 必选参数，上传的地址
 * @method $this multiple(bool $bool) 是否支持多选文件
 * @method $this data($object) 上传时附带的额外参数
 * @method $this name(string $name) 上传的文件字段名
 * @method $this withCredentials(bool $bool) 支持发送 cookie 凭证信息
 * @method $this showFileList(bool $bool) 是否显示已上传文件列表
 * @method $this drag(bool $bool) 是否启用拖拽上传
 * @method $this accept(string $type) 接受上传的文件类型（thumbnail-mode 模式下此参数无效）
 * @method $this listType(string $type) 文件列表的类型 text / picture / picture-card
 * @method $this autoUpload(bool $bool) 是否在选取文件后立即进行上传
 * @method $this fileList(array $list) 上传的文件列表, 例如: [{name: 'food.jpg', url: 'https://xxx.cdn.com/xxx.jpg'}]
 * @package Eadmin\component\form\field
 */
class Upload extends Field
{
    protected $name = 'ElUpload';
}