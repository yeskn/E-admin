<?php


namespace Eadmin\controller;

use Eadmin\Controller;
use Eadmin\controller\BaseAdmin;
use Eadmin\model\SystemNotice;
use Eadmin\service\AdminService;
use Eadmin\service\NoticeService;

/**
 * 系统通知
 * Class Notice
 * @package app\admin\controller
 */
class Notice extends Controller
{
    /**
     * 系统通知
     * @auth false
     * @login false
     */
    public function notification(){
        $data = NoticeService::instance()->receive();
        $this->successCode($data);
    }
    /**
     * 获取系统通知
     * @auth false
     * @login false
     */
    public function system(){
        $data = SystemNotice::where('user_id',AdminService::instance()->id())
            ->pages()->select();
        $this->successCode($data);
    }
    /**
     * 读取系统通知
     * @auth false
     * @login false
     */
    public function reads(){
        SystemNotice::where('id',$this->request->post('id'))->update(['is_read'=>1]);
        $count =  SystemNotice::where('user_id',AdminService::instance()->id())
            ->where('is_read',0)->count();
        $this->successCode($count);
    }
    /**
     * 清空通知
     * @auth false
     * @login false
     */
    public function clear(){
        SystemNotice::where('user_id',AdminService::instance()->id())->delete();
        $this->successCode();
    }
}
