<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-04-28
 * Time: 21:38
 */

namespace app\admin\controller;


use Eadmin\controller\BaseAdmin;
use Eadmin\facade\Button;
use Eadmin\form\Form;
use Eadmin\grid\Actions;
use Eadmin\grid\Filter;
use Eadmin\grid\Grid;
use Eadmin\model\AdminModel;
use Eadmin\model\SystemAuth;
use Eadmin\service\AdminService;
use Eadmin\service\TokenService;
use Eadmin\tools\Data;

/**
 * 系统用户管理
 * Class Admin
 * @package app\admin\controller
 */
class Admin extends BaseAdmin
{
    /**
     * 系统用户列表
     * @auth true
     * @login true
     * @method get
     */
    protected function grid()
    {
        $grid = new Grid(new AdminModel());
        $grid->indexColumn();
        $grid->setTitle('系统用户');
        $grid->userInfo('avatar', 'nickname', '头像');
        $grid->column('username', '用户账号')->display(function ($val, $data) {
            if ($data['id'] == config('admin.admin_auth_id')) {
                return "{$val} <el-badge value=\"超级管理员\" type=\"primary\" style='margin-top: 10px;' />";
            } else {
                return $val;
            }
        });
        $grid->column('phone', '手机号');
        $grid->column('mail', '邮箱');
        $grid->column('status', '账号状态')->switch([1 => '正常'], [0 => '已禁用']);
        $grid->column('create_time', '创建时间');
        $grid->setFormDialog();
        $grid->actions(function (Actions $action, $data) {
            if ($data['id'] == config('admin.admin_auth_id')) {
                $action->hideDel();
            }
            $action->width(300);
            $action->hideDetail();
            $button = Button::create('重置密码', 'primary', 'small', 'el-icon-key', true)
                ->href(url('resetPassword', ['id' => $data['id']]), 'modal');

            $action->prepend($button);

        });
        //删掉前回调
        $grid->deling(function ($ids, $trueDelete) {
            if (!$trueDelete) {
                if ($ids === true || in_array(config('admin.admin_auth_id'), $ids)) {
                    $this->errorCode(999, '超级管理员不可以删除噢!');
                }
            }
        });
        //更新前回调
        $grid->updateing(function ($ids, $data) {
            if (in_array(config('admin.admin_auth_id'), $ids)) {
                if ($data['status'] == 0) {
                    $this->errorCode(999, '超级管理员不可以禁用噢!');
                }
            }
        });
        $grid->filter(function (Filter $filter) {
            $filter->like('username', '用户账号');
            $filter->like('phone', '手机号');
            $filter->eq('status', '账号状态')->select([
                1 => '正常',
                0 => '已禁用'
            ]);
            $filter->dateRange('create_time', '创建时间');
        });
        $grid->quickSearch();
        return $grid;
    }
    /**
     * 修改密码
     * @auth true
     * @login true
     */
    public function updatePassword()
    {
        $form = new Form(new AdminModel());
        $form->edit(AdminService::instance()->id());
        $form->password('old_password','旧密码')->required();
        $form->password('new_password', '新密码')->rule([
            'confirm' => '输入密码不一致',
            'min:5' => '密码最少5位数'
        ])->required();
        $form->password('new_password_confirm', '确认密码')->required();
        $form->saving(function ($data,$model) {
            if(!password_verify($data['old_password'],$model['password'])){
                $this->errorCode(999,'旧密码错误');
            }
            $data['password'] = $data['new_password'];
            return $data;
        });
        return $this->view($form);
    }
    /**
     * 重置密码
     * @auth true
     * @login true
     */
    public function resetPassword()
    {
        $form = new Form(new AdminModel());
        $form->password('new_password', '新密码')->rule([
            'confirm' => '输入密码不一致',
            'min:5' => '密码最少5位数'
        ])->required();
        $form->password('new_password_confirm', '确认密码')->required();
        $form->saving(function ($data) {
            $data['password'] = $data['new_password'];
            return $data;
        });
        return $this->view($form);
    }

    /**
     * 系统用户
     * @auth true
     * @login true
     */
    protected function form()
    {
        $form = new Form(new AdminModel());
        $userInput = $form->text('username', '用户账号')->rule([
            'chsDash' => '用户账号只能是汉字、字母、数字和下划线_及破折号-',
            'unique:system_user' => '用户名存在重复'
        ])->required();
        if ($form->isEdit()) {
            $userInput->disabled();
        }
        $form->text('nickname', '用户昵称')->rule([
            'chsAlphaNum' => '用户昵称只能是汉字、字母和数字',
        ])->required();
        $form->image('avatar', '用户头像');
        if (!$form->isEdit()) {
            $form->password('password', '密码')->rule(['min:5' => '密码最少5位数'])->default(123456)->help('初始化密码123456,建议密码包含大小写字母、数字、符号')->required();
        }
        $form->number('phone', '手机号')
            ->rule([
                'mobile' => '请输入正确的手机号',
                'unique:' . config('admin.system_user_table') => '手机号已存在'
            ]);
        $form->text('mail', '邮箱')->rule([
            'email' => '请输入正确的邮箱',
        ]);
        if ($form->getData('id') != config('admin.admin_auth_id')) {
            $auths = SystemAuth::column('name', 'id');
            $form->checkbox('roles', '访问权限')->themeButton()->options($auths);
        }
        return $form;
    }

    /**
     * 获取个人信息
     * @auth false
     * @login true
     * @method get
     */
    public function info()
    {
        //初始化节点获取
        list($router, $menus) = AdminService::instance()->menus();
        $data['menus'] = $menus;
        $data['router'] = $router;
        $data['info'] = AdminService::instance()->user();
        $this->successCode($data);
    }

    /**
     * 编辑个人信息
     * @auth false
     * @login true
     * @method get
     */
    public function editInfo()
    {
        $form = new Form(new AdminModel());
        $userInput = $form->text('username', '用户名')->rule([
            'chsDash' => '用户名只能是汉字、字母、数字和下划线_及破折号-'
        ])->required();
        if ($form->isEdit()) {
            $userInput->disabled();
        }
        $form->text('nickname', '用户昵称')->rule([
            'chsAlphaNum' => '用户昵称只能是汉字、字母和数字',
        ])->required();
        $form->image('avatar', '用户头像')->default($this->request->domain() . '/static/img/headimg.png')->size(80, 80);
        if (!$form->isEdit()) {
            $form->password('password', '密码')->rule(['min:5' => '密码最少5位数'])->default(123456)->help('初始化密码123456,建议密码包含大小写字母、数字、符号')->required();
        }
        $form->text('phone', '手机号')
            ->rule([
                'mobile' => '请输入正确的手机号',
                'unique:' . config('admin.system_user_table') => '手机号已存在'
            ]);
        $form->text('mail', '邮箱')->rule([
            'email' => '请输入正确的邮箱',
        ]);
        return $this->view($form);
    }

    /**
     * 刷新token
     * @auth false
     * @login true
     * @method post
     */
    public function refreshToken()
    {
        $tokens = TokenService::instance()->refresh();
        $this->successCode($tokens);
    }
    /**
     * 初始化加载
     * @auth false
     * @login true
     */
    public function init()
    {
        $template = '';
        $this->successCode($template);
    }
}
