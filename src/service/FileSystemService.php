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
}
