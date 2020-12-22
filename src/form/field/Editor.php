<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-05-23
 * Time: 11:01
 */

namespace Eadmin\form\field;


use Overtrue\Flysystem\Qiniu\Plugins\UploadToken;
use think\facade\Filesystem;
use Eadmin\form\Field;
class Editor extends Field
{
    public function __construct($field, $label, array $arguments = [])
    {
        parent::__construct($field, $label, $arguments);
        $this->setAttr('url',request()->domain().'/eadmin/upload');
        $this->disk(config('admin.uploadDisks'));
    }
    public function render()
    {
        list($attrStr, $tableScriptVar) = $this->parseAttr();
        $html = "<eadmin-tinymce {$attrStr}></eadmin-tinymce>";
        return $html;
    }
    /**
     * 上传存储类型
     * @param $uptype local,qiniu,oss
     */
    public function disk($diskType){
        $config = config('filesystem.disks.'.$diskType);
        $uptype = $config['type'];
        $accessKey = '';
        $accessKeySecret = '';
        $this->setAttr('up-type',$uptype);
        if($uptype == 'qiniu'){
            $this->setAttr('bucket',$config['bucket']);
            $this->setAttr('domain',$config['domain']);
            Filesystem::disk('qiniu')->addPlugin(new UploadToken());
            $this->setAttr('uploadToken',Filesystem::disk('qiniu')->getUploadToken(null,3600*3));
        }elseif ($uptype == 'oss'){
            $this->setAttr('access-key',$config['accessKey']);
            $this->setAttr('secret-key',$config['secretKey']);
            $this->setAttr('bucket',$config['bucket']);
            $this->setAttr('endpoint',$config['endpoint']);
            $this->setAttr('domain',$config['domain']);
            $this->setAttr('region',$config['region']);
        }else{
            $this->setAttr('domain',request()->domain());
        }
        return $this;
    }
    /**
     * 高度
     * @param $number
     */
    public function height($number){
        $this->setAttr('height',$number);
        return $this;
    }

    /**
     * 宽度
     * @param $number
     */
    public function width($number){
        $this->setAttr('width',$number);
        return $this;
    }
}
