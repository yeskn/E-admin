<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-04-13
 * Time: 20:28
 */

namespace Eadmin\controller;


use Eadmin\Admin;
use Eadmin\Controller;
use Eadmin\grid\Actions;
use Eadmin\form\Form;
use Eadmin\grid\Grid;
use Eadmin\model\SystemMenu;
use Eadmin\service\MenuService;
use think\facade\Filesystem;


/**
 * 系统菜单管理
 * Class Menu
 * @package app\admin\controller
 */
class Menu extends Controller
{
    /**
     * 系统菜单管理
     * @auth true
     * @login true
     * @return Grid
     */
    public function index(): Grid
    {
        $grid = new Grid(new SystemMenu());
        $grid->treeTable();
        $grid->title('系统菜单管理');
        $grid->column('name', '菜单名称')->display(function ($val, $data) {
            return "<i class='{$data['icon']}'></i> " . $val;
        });
        $grid->column('url', '菜单链接');
        $grid->column('status', '状态')->switch();
        $grid->actions(function (Actions $action, $data) {
            $action->hideDetail();
        });
        $grid->sortInput();
        $grid->setForm($this->form())->dialog();
        $grid->quickSearch();
        $grid->export();
        return $grid;
    }

    /**
     * 系统菜单
     * @auth true
     * @login true
     * @return Form
     */
    public function form(): Form
    {
        $menus = Admin::menu()->listOptions();
        $form = new Form(new SystemMenu());
        $form->select('pid', '上级菜单')
            ->options([0 => '顶级菜单'] + array_column($menus, 'label', 'id'))
            ->required();
        $form->text('name', '菜单名称')->required();
        $form->text('url', '菜单链接');
        $form->text('params', '链接参数');
        $form->icon('icon', '菜单图标');
        return $form;
    }
}
