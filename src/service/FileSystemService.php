<?php


namespace Eadmin\service;


use Eadmin\Service;
use Symfony\Component\Finder\Finder;
use think\facade\Filesystem;

class FileSystemService extends Service
{
    protected $path = '';
    public function __construct()
    {
        $this->path = Filesystem::disk('local')->path('/');
    }
    public function getPath(){
        return $this->path;
    }
    public function getFiles($path='',$search=''){
        if(empty($path)){
            $path = $this->path;
        }
        $finder = new Finder();
        $datas = [];
        foreach ($finder->in($path)->depth('== 0')->name("*$search*")->sortByChangedTime() as $file) {
            $author = posix_getpwuid($file->getOwner());
            $datas[] = [
                'name'=>$file->getFilename(),
                'path'=>$file->getPathname(),
                'dir'=>$file->isDir(),
                'size'=>$file->getSize(),
                'permission'=>substr(base_convert($file->getPerms(),10,8),-3),
                'author'=>$author['name'],
                'update_time'=>date('Y-m-d H:i:s',$file->getATime()),
            ];
        }
        return $datas;
    }

    /**
     * 删除文件
     * @param $path
     * @return bool
     */
    public function delFiels($path){
        if(is_file($path)){
            unlink($path);
        }elseif (is_dir($path)){
            $this->deleteDir($path);
        }
        return true;
    }
    protected function deleteDir($dirName){
        //如果是目录，那么我们就遍历下面的文件或者目录
        //打开目录句柄
        $dir = opendir($dirName);
        while($fileName = readdir($dir)){
            //不运行像上级目录运行
            if($fileName!="." && $fileName!=".."){
                $file = $dirName."/".$fileName;
                if(is_dir($file)){
                    $this->deleteDir($file);//使用递归删除目录
                }else{
                    unlink($file);
                }
            }
        }
        closedir($dir);//关闭dir
        rmdir( $dirName );
    }
}
