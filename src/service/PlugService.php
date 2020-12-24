<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-08-09
 * Time: 16:49
 */

namespace Eadmin\service;

use Composer\Autoload\ClassLoader;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use think\App;
use think\facade\Cache;
use think\facade\Console;
use think\facade\Db;
use think\helper\Arr;
use Eadmin\Service;

class PlugService extends Service
{
    protected $plugPathBase = '';
    protected $plugPaths = [];
    protected $plugs = [];
    protected static $loader;
    protected $installedPlug = [];
    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->plugPathBase = app()->getRootPath() . config('admin.extension.dir', 'eadmin-plugs') ;

        foreach (glob($this->plugPathBase . '/*') as $file) {

            if (is_dir($file)) {
                foreach (glob($file . '/*') as $file) {
                    $this->plugPaths[] = $file;
                }
            }
        }

    }

    /**
     * 获取插件目录
     * @return string
     */
    public function getPath(){
        return $this->plugPathBase;
    }
    /**
     * 获取 composer 类加载器.
     *
     * @return ClassLoader
     */
    public function loader()
    {
        if (!static::$loader) {
            static::$loader = include $this->app->getRootPath() . '/vendor/autoload.php';
        }
        return static::$loader;
    }

    /**
     * 注册扩展
     */
    public function register()
    {
        $loader = $this->loader();
        foreach ($this->plugPaths as $plugPaths) {
            $file = $plugPaths . DIRECTORY_SEPARATOR . 'composer.json';
            if(is_file($file)){
                $arr = json_decode(file_get_contents($file), true);
                $psr4 = Arr::get($arr, 'autoload.psr-4');
                $name = Arr::get($arr,'name');
                if($this->status($name)){
                    if ($psr4) {
                        foreach ($psr4 as $namespace => $path) {
                            $path = $plugPaths . '/' . trim($path, '/') . '/';
                            $loader->addPsr4($namespace, $path);
                        }
                    }
                    $serviceProvider = Arr::get($arr, 'extra.e-admin');
                    if ($serviceProvider) {
                        $this->app->register($serviceProvider);
                    }
                }
            }
        }
    }

    /**
     * 获取所有插件
     */
    public function all($search='')
    {
        $cacheKey = 'eadmin_plugs_'.$search;
        $plugs = Cache::get($cacheKey);
        if(!$plugs){
            $plugs = GitlabService::instance()->getGroupProject(57,$search,1,100);
        }
        $delNames = [];
        foreach ($plugs as $plug){
            if(isset($plug['composer'])){
                $content = $plug['composer'];
            }else{
                $content = GitlabService::instance()->getFile($plug['id'],'composer.json');
            }
            if($content){
                $info = $this->getInfo($content);
                $info['composer'] = $content;
                $info['id'] = $plug['id'];
                $info['title'] = $plug['title'] ?? $plug['name'];
                $info['web_url'] = $plug['web_url'];
                $info['description'] = $plug['description'];
                $info['download'] = "https://gitlab.my8m.com/api/v4/projects/{$plug['id']}/repository/archive.zip";
                $this->plugs[] = $info;
                if(!is_dir($info['path'])){
                    $info['status'] = false;
                    $delNames[] = trim($info['name']);
                };
                if($info['install']){
                    $this->installedPlug[] = $info;
                }
            }
        }
        Db::name('system_plugs')->whereIn('name',$delNames)->delete();
        if(! Cache::has($cacheKey)){
            Cache::set($cacheKey,$this->plugs,60 * 5);
        }
        return $this->plugs;
    }

    /**
     * 已安装插件
     * @return array
     */
    public function installed($search=''){
        if(count($this->plugs) == 0){
            $this->all($search);
        }
        return  $this->installedPlug;
    }
    protected function getInfo($content)
    {
        $arr = json_decode($content,true);
        $version = '1.0.0';
        $authors = array_column(Arr::get($arr,'authors'),'name');
        $authors = implode(',',$authors);
        $emails = array_column(Arr::get($arr,'authors'),'email');
        $emails = implode(',',$emails);
        $name = Arr::get($arr,'name');
        $status = $this->status($name);
        return [
            'name' => $name,
            'description' => Arr::get($arr,'description'),
            'author' => $authors,
            'email' =>$emails,
            'status'=> $status ?? false,
            'install'=> is_null($status) ? false : true,
            'version' => $version,
            'path'=>$this->plugPathBase.'/'.$name,
        ];
    }

    /**
     * 插件状态
     * @param $name 插件名称
     * @return mixed
     */
    public function status($name){
        try{
            return Db::name('system_plugs')->where('name',$name)->value('status');
        }catch (\Exception $exception){
            return false;
        }
    }
    /**
     * 启用禁用
     * @param $name 插件名称
     * @param $status 状态
     * @return int
     * @throws \think\db\exception\DbException
     */
    public function enable($name,$status){
        return Db::name('system_plugs')->where('name',$name)->update(['status'=>$status]);
    }
    /**
     * 校验扩展包内容是否正确.
     *
     * @param $directory
     *
     * @return bool
     */
    protected function checkFiles($directory)
    {
        if (
            ! is_dir($directory.'/src')
            || ! is_file($directory.'/composer.json')
            || ! is_file($directory.'/version.php')
        ) {
            return false;
        }
        return true;
    }
    protected function dataMigrate($cmd,$path){
        $migrations = $path.'/src/database'. DIRECTORY_SEPARATOR . 'migrations';
        if(is_dir($migrations)){
            Console::call('migrate:eadmin',['cmd'=>$cmd,'path'=> $migrations]);
        }
        return true;
    }
    /**
     * 安装
     * @param $path 插件目录
     * @return mixed
     */
    public function install($name,$path)
    {
        try{
            $client = new Client(['verify'=>false]);
            $plugZip = app()->getRootPath().'plug'.time().'.zip';
            $plugZip = app()->getRootPath().'plug1608222804.zip';
            $client->get($path,['save_to'=>$plugZip]);
            $zip = new \ZipArchive();
            if ($zip->open($plugZip) === true) {
                $path = $this->plugPathBase.'/'.$name;
                if(!is_dir($path)){
                    mkdir($path,0755,true);
                }
                for ($i=0;$i<$zip->numFiles;$i++){
                    if($i > 0){
                        $filename = $zip->getNameIndex($i);
                        $pathArr = explode('/',$filename);
                        array_shift($pathArr);
                        $pathName = implode('/',$pathArr);
                        $fileinfo = pathinfo($filename);
                        $toFile = $path.'/'.$pathName;
                        if(isset($fileinfo['extension'])){
                            copy("zip://".$plugZip."#".$filename, $toFile);
                        }else{
                            if(!is_dir($toFile))mkdir($toFile,0755,true);
                        }
                    }
                }
                //关闭
                $zip->close();
                unlink($plugZip);
                $this->dataMigrate('run',$path);
                $seed = $path.'/src/database'. DIRECTORY_SEPARATOR . 'seeds';
                if(is_dir($seed)){
                    Console::call('seed:eadmin',['path'=> $seed]);
                }
                Db::name('system_plugs')->insert([
                    'name'=> trim($name),
                ]);
                return true;
            }else{
                return false;
            }
        }catch (RequestException $exception){
            return false;
        }
    }
    /**
     * 卸载
     * @param $path
     */
    public function uninstall($name,$path)
    {
        $this->deldir($path.'/');
        Db::name('system_plugs')->where('name',$name)->delete();
        Db::name('system_menu')->where('mark',$name)->delete();
        return $this->dataMigrate('rollback',$path);;
    }
    protected function deldir($path){
        //如果是目录则继续
        if(is_dir($path)){
            //扫描一个文件夹内的所有文件夹和文件并返回数组
            $p = scandir($path);
            foreach($p as $val){
                //排除目录中的.和..
                if($val !="." && $val !=".."){
                    //如果是目录则递归子目录，继续操作
                    if(is_dir($path.$val)){
                        //子目录中操作删除文件夹和文件
                        $this->deldir($path.$val.'/');
                        //目录清空后删除空文件夹
                        @rmdir($path.$val.'/');
                    }else{
                        //如果是文件直接删除
                        unlink($path.$val);
                    }
                }
            }
            @rmdir($path);
        }
    }
}
