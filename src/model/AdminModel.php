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
        $roleIds = SystemUserAuth::where('user_id',$this->id)->column('role_id');
        return SystemAuthNode::whereIn('auth',$roleIds)->select();
    }

    //角色组
    public function roles(){
        return $this->belongsToMany('system_auth',SystemUserAuth::class,'role_id','user_id');
    }
}
