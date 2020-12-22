<?php


namespace Eadmin\controller;

use think\facade\Console;
use think\facade\Db;
use Eadmin\controller\BaseAdmin;
use Eadmin\facade\Button;
use Eadmin\facade\Component;
use Eadmin\form\Form;
use Eadmin\grid\Column;
use Eadmin\grid\Table;
use Eadmin\layout\Content;
use Eadmin\service\PlugService;

/**
 * 插件管理
 * Class Plug
 * @package app\admin\controller
 */
class Plug extends BaseAdmin
{
    /**
     * 插件列表
     * @auth true
     * @login true
     */
    public function index()
    {
        $search = $this->request->get('search');
        $datas = PlugService::instance()->all($search);
        $columns[] = new Column('name', '插件信息');
        $columns[] = new Column('authors', '作者');

        $searchInput = Component::fetch(__DIR__.'/../view/search.vue');
        $columns[] = new Column('action', $searchInput);
        foreach ($datas as $key => &$rows) {
            $rows['id'] = $key;
            foreach ($columns as $column) {
                $field = $column->getField();
                if ($field == 'name') {
                    $column->display(function () use ($rows) {
                        $html = <<<EOF
<div style='display: flex;justify-content: space-between;align-items: center'>
   
    <div style="flex: 1">
        名称 : <b><el-link target="_blank" href="{$rows['web_url']}">{$rows['title']}</el-link> &nbsp;<el-tag size="mini">{$rows['version']}</b></el-tag><br>描述 : {$rows['description']}
    </div>
</div> 
EOF;
                        return $html;
                    });
                }
                if ($field == 'authors') {
                    $column->display(function () use ($rows) {
                        $html = <<<EOF
{$rows['author']} <el-tag size="mini">{$rows['email']}</el-tag>
EOF;
                        return $html;
                    });
                }
                if ($field == 'action') {
                    $column->display(function () use ($rows) {
                        if ($rows['install']) {
                            $button = '';
                            if($rows['status']){
                                $button .= Button::create('禁用', 'info')->save($rows['name'], ['status' => 0], 'plug/enable', '确认卸载？');
                            }else{
                                $button = Button::create('启用', 'success')->save($rows['name'], ['status' => 1],'plug/enable', '确认卸载？');
                            }
                            $button .= Button::create('卸载', 'danger')->save($rows['name'], ['name'=>$rows['name'],'path' => $rows['path'], 'type' => 2], 'plug/install', '确认卸载？');
                        } else {
                            $button = Button::create('安装', 'primary')->save($rows['name'], ['name'=>$rows['name'],'path' => $rows['download'], 'type' => 1], 'plug/install', '确认安装？');
                        }
                        return $button;
                    });
                }
                $column->setData($rows);
            }
        }
        $table = new Table($columns, $datas);
        $content = new Content();
        $button = Button::create('创建扩展', 'primary','mini','',true)->href('plug/add','modal');
        $view = $content->title('插件管理 | &nbsp;'.$button)->body($table)->view();
        return Component::view($view);
    }
    /**
     * 创建扩展
     * @auth true
     * @login true
     */
    public function add(){
        $form = new Form();
        $form->text('name','名称')->placeholder('请输入扩展名称，例如：eadmin/plug')->required();
        $form->text('description','描述');
        $form->text('namespace','命名空间');
        $form->saving(function ($post){
            $name = $post['name'];
            if(!strpos($name,'/')){
                Component::message()->warning('扩展名称格式错误，例如：eadmin/plug');
            }
            $cmd['name'] = $name;
            $description = $post['description'];
            $namespace = $post['namespace'];
            if(!empty($description)){
                array_push($cmd,"--description={$description}");
            }
            if(!empty($namespace)){
                array_push($cmd,"--namespace={$namespace}");
            }
            Console::call('eadmin:plug',$cmd);
            Component::message()->success('添加成功')->refresh();
        });
        return $form;

    }
    /**
     * 启用/禁用
     * @auth true
     * @login true
     */
    public function enable($id,$status){
        PlugService::instance()->enable($id,$status);
        Component::message()->success('操作完成')->refresh();
    }
    /**
     * 安装/卸载
     * @auth true
     * @login true
     */
    public function install()
    {
        $type = $this->request->put('type');
        $path = $this->request->put('path');
        $name = $this->request->put('name');
        if ($type == 1) {
            PlugService::instance()->install($name,$path);
        } else {
            PlugService::instance()->uninstall($name,$path);
        }
        Component::message()->success('操作完成')->refresh();
    }
}
