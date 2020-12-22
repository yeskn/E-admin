<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-04-25
 * Time: 16:38
 */

namespace app\admin\controller;


use Eadmin\controller\BaseAdmin;

use Eadmin\facade\Button;
use Eadmin\form\Form;
use Eadmin\grid\Actions;
use Eadmin\grid\Grid;

use Eadmin\model\SystemAuth;
use Eadmin\model\SystemAuthNode;
use Eadmin\service\NodeService;

/**
 * 系统权限管理
 * Class Auth
 * @package app\admin\controller
 */
class Auth extends BaseAdmin
{
    /**
     * 系统权限列表
     * @auth true
     * @login true
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new SystemAuth());
        $grid->setTitle('访问权限管理');
        $grid->indexColumn();
        $grid->column('name', '权限名称');
        $grid->column('desc', '权限描述');
        $grid->column('status', '状态')->switch([1=>'使用中'],[0=>'已禁用']);
        $grid->actions(function (Actions $action,$data) {
            $action->hideDetail();
            $button = Button::create('权限授权','primary','small','el-icon-s-check',true)
                ->href(url('authNode',['id'=>$data['id']]));
            $action->prepend($button);
        });
        $grid->setFormDialog();
        return $grid;
    }

    /**
     * 系统权限
     * @auth true
     * @login true
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new SystemAuth());
        $form->text('name', '权限名称')->required();
        $form->textarea('desc', '权限描述')->rows(4)->required();
        return $form;
    }
    /**
     * 权限授权
     * @auth true
     * @login true
     * @return string
     */
    public function authNode(){
        $form = new Form(new SystemAuth());
        $form->setTitle('权限授权');
        $data = NodeService::instance()->tree();
        $nodes = SystemAuthNode::where('auth',request()->get('id'))->field('md5(concat(node,method)) as node')->select()->column('node');
        $form->tree('auth_nodes', '')->data($data)->setChecked('mark',$nodes)->horizontal();
        $form->hideResetButton();
        $form->saving(function ($data){
            if(!empty($data['auth_nodes']) &&count($data['auth_nodes']) > 0){
                SystemAuthNode::where('auth',$data['id'])->delete();
                $authData = [];
                foreach ($data['auth_nodes'] as $node){
                    if(!empty($node['rule'])){
                        $authData[] = [
                            'auth'=>$data['id'],
                            'method'=>$node['method'],
                            'node'=>$node['rule'],
                        ];
                    }
                }
                if(count($authData)> 0){
                    (new SystemAuthNode())->saveAll($authData);
                }

            }
        });
        return $this->view($form);
    }
}
