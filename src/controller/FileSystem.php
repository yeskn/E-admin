<?php


namespace Eadmin\controller;


use Eadmin\Controller;
use Eadmin\service\FileSystemService;

class FileSystem extends Controller
{
    public function index(){
        $data = FileSystemService::instance()->getFiles($this->request->get('path'),$this->request->get('search'));
        $fileSystem = new \Eadmin\component\basic\FileSystem($data);
        return $fileSystem->initPath(FileSystemService::instance()->getPath());
    }
    //新建文件夹
    public function mkdir($path){
        if(is_dir($path)){
            admin_error_message('文件夹已存在');
        }
        mkdir($path);
        admin_success('成功','新建文件夹成功');
    }
    public function del($paths){
        foreach ($paths as $path){
            FileSystemService::instance()->delFiels($path);
        }
        admin_success('成功','删除完成');
    }
}
