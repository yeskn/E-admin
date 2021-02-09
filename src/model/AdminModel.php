<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-04-25
 * Time: 17:17
 */

namespace Eadmin\model;


use app\model\User;
use think\facade\Db;
use think\Model;

class AdminModel extends BaseModel
{
    public function __construct(array $data = [])
    {
        $this->table = config('admin.system_user_table');
        parent::__construct($data);
    }
    protected function setPasswordAttr($val){
        return password_hash($val,PASSWORD_DEFAULT);
    }
    //权限
    public function permissions(){
        $roleIds = SystemUserAuth::where('user_id',$this->id)->column('auth_id');
        return SystemAuthNode::whereIn('auth_id',$roleIds)->select()->toArray();
    }
    //菜单
    public function menus(){
        $roleIds = SystemUserAuth::where('user_id',$this->id)->column('auth_id');
        $menuIds = SystemAuthMenu::whereIn('auth_id',$roleIds)->column('menu_id');
        return Db::name('system_menu')->where('status', 1)->whereIn('id',$menuIds)->order('sort asc,id desc')->select();
    }
    //角色组
    public function roles(){
        return $this->belongsToMany('system_auth',SystemUserAuth::class,'auth_id','user_id');
    }
}
