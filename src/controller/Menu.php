<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-04-13
 * Time: 20:28
 */

namespace Eadmin\controller;


use Eadmin\grid\Actions;
use Eadmin\form\Form;
use Eadmin\grid\Grid;
use Eadmin\model\SystemMenu;
use Eadmin\service\MenuService;
use Eadmin\controller\BaseAdmin;

/**
 * 系统菜单管理
 * Class Menu
 * @package app\admin\controller
 */
class Menu extends BaseAdmin
{
    /**
     * 系统菜单管理
     * @auth true
     * @login true
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new SystemMenu());
        $grid->treeTable();
        $grid->indexColumn();
        $grid->column('name', '菜单名称')->display(function ($val, $data) {
            return "<i class='{$data['icon']}'> </i> {$val}";
        });
        $grid->column('url', '菜单链接');
        $grid->column('status', '状态')->switch([1=>'使用中'],[0=>'已禁用']);
        $grid->actions(function (Actions $action, $data) {
            $action->hideDetail();
        });
        $grid->sortInput();
        $grid->setFormDialog();
        return $grid;
    }

    /**
     * 系统菜单
     * @auth true
     * @login true
     * @return Form
     */
    protected function form()
    {
        $menus = MenuService::instance()->listOptions();
        $form = new Form(new SystemMenu());
        $form->select('pid', '上级菜单')
            ->options([0=>'顶级菜单']+array_column($menus,'label','id'))
            ->width()->required();
        $form->text('name', '菜单名称')->required();
        $form->text('url', '菜单链接')->default('#')->required();
        $form->text('params', '链接参数');
        $form->icon('icon', '菜单图标');
        return $form;
    }
}
