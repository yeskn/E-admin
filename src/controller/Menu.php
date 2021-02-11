<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-04-13
 * Time: 20:28
 */

namespace Eadmin\controller;


use Eadmin\Admin;
use Eadmin\component\basic\Button;
use Eadmin\component\basic\Dropdown;
use Eadmin\component\basic\DropdownItem;
use Eadmin\component\basic\Html;
use Eadmin\component\basic\Tip;
use Eadmin\component\layout\Content;
use Eadmin\component\layout\Row;
use Eadmin\Controller;
use Eadmin\grid\Actions;
use Eadmin\form\Form;
use Eadmin\detail\Detail;
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
    public function index()
    {
        $grid = new Grid(new SystemMenu());
        $grid->treeTable();
        $grid->title('系统菜单管理');
        $grid->column('name', '菜单名称')->display(function ($val, $data) {
            return "<i class='{$data['icon']}'></i>" . $val;
        });
        $grid->column('url', '菜单链接')->display(function ($val) {
            return ' ' . $val;
        });
        $grid->column('status', '状态')->switch();
        $grid->actions(function (Actions $action, $data) {
            //  $action->hideDetail();
        });
        $grid->sortInput();
        $grid->setForm($this->form())->dialog();
        $grid->setDetail($this->detail());
        $grid->quickSearch();
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

    /**
     * 系统菜单详情
     * @auth true
     * @login true
     * @return Detail
     */
    public function detail($id = 0): Detail
    {

        $detail = new Detail(SystemMenu::find($id));
        $detail->title('系统菜单详情');
        $detail->field('name','菜单')->md(12)->tip();
        $detail->field('name','icon')->md(12)->display(function ($val,$data){
            return $data['icon'];
        });
        $detail->field('name','菜单')->md(12);

        $detail->row(function ($detail){
            $detail->card('卡片',function ($detail){
                $detail->field('name','菜单')->md(12);
                $detail->field('name','菜单')->md(12);
                $detail->field('name','菜单')->md(12);

            },24);
        });
//        $detail->grid('test','auth',function (Grid $grid){
//            $grid->column('auth_id','auth_id');
//        });

        return $detail;
    }
}
