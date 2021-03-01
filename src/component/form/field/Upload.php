<?php


namespace Eadmin\component\form\field;


use Eadmin\Admin;
use Eadmin\component\form\Field;

use Overtrue\Flysystem\Qiniu\Plugins\UploadToken;
use think\facade\Filesystem;

/**
 * 上传
 * Class Upload
 * @link https://element-plus.gitee.io/#/zh-CN/component/upload
 * @method $this isUniqidmd5(bool $value = true) 唯一文件名
 * @method $this displayType(string $value) 上传显示方式 image图片, file文件
 * @method $this drag(bool $value = true) 是否启用拖拽上传
 * @package Eadmin\component\form\field
 */
class Upload extends Field
{
    protected $name = 'EadminUpload';

    public function __construct($field = null, string $value = '')
    {
        parent::__construct($field, $value);
        $this->attr('url', request()->domain() . '/eadmin/upload');
        $this->attr('token', Admin::token()->get());
        $this->disk(config('admin.uploadDisks'));
    }

    /**
     * 多文件上传
     */
    public function multiple()
    {
        $this->attr('singleFile', false);
        return $this;
    }

    /**
     * 上传存储类型
     * @param string $diskType local,qiniu,oss
     */
    public function disk($diskType)
    {
        $config          = config('filesystem.disks.' . $diskType);
        $uptype          = $config['type'];
        $accessKey       = '';
        $accessKeySecret = '';
        $this->attr('upType', $diskType);
        if ($uptype == 'qiniu') {
            $this->attr('bucket', $config['bucket']);
            $this->attr('domain', $config['domain']);
            Filesystem::disk('qiniu')->addPlugin(new UploadToken());
            $this->attr('uploadToken', Filesystem::disk('qiniu')->getUploadToken(null, 3600 * 3));
        } elseif ($uptype == 'oss') {
            $this->attr('accessKey', $config['accessKey']);
            $this->attr('secretKey', $config['secretKey']);
            $this->attr('bucket', $config['bucket']);
            $this->attr('endpoint', $config['endpoint']);
            $this->attr('domain', $config['domain']);
            $this->attr('region', $config['region']);
        }
        return $this;
    }

    /**
     * 指定保存目录
     * @param string $path 目录地址
     */
    public function saveDir($path)
    {
        if (substr($path, -1) != '/') {
            $path .= '/';
        }
        $this->attr('saveDir', $path);
        return $this;
    }

    /**
     * 显示尺寸
     * @param int $width 宽度
     * @param int $height 高度
     * @return $this
     */
    public function size($width, $height)
    {
        $this->attr('width', $width);
        $this->attr('height', $height);
        return $this;
    }
//    /**
//     * 裁剪尺寸,暂仅支持单文件
//     * @param $width 宽度
//     * @param $height 高度
//     * @param $auto 是否自动居中裁剪,否显示界面手动裁剪
//     * @return $this
//     */
//    public function crop($width,$height,$auto = false){
//        $this->attr('cropWidth',$width);
//        $this->attr('cropHeight',$height);
//        $this->attr('cropperOn',true);
//        $this->attr('cropperAuto',$auto);
//        return $this;
//    }
//    /**
//     * 图片建议提示
//     * @param $width 宽度
//     * @param $height 高度
//     */
//    public function helpSize($width,$height){
//        $this->help("建议上传图片尺寸 $width * $height");
//        return $this;
//    }
    /**
     * 限制上传类型
     * @param string $val
     */
    public function ext($val)
    {
        if (is_string($val)) {
            $val = explode(',', $val);
        }
        $val   = array_map(function ($item) {
            return ".{$item}";
        }, $val);
        $accept = implode(',', $val);
        $this->attr('accept', $accept);
        return $this;
    }
}
