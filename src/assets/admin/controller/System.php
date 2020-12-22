<?php

namespace app\admin\controller;

use think\facade\Cache;
use Eadmin\controller\BaseAdmin;
use Eadmin\form\Form;
use Eadmin\model\SystemConfig;
use Eadmin\model\SystemNotice;
use Eadmin\service\AdminService;
use Eadmin\service\CaptchaService;
use Eadmin\service\NoticeService;
use Eadmin\tools\Data;

/**
 * 系统参数配置
 * Class System
 * @package app\admin\controller
 */
class System extends BaseAdmin
{
    /**
     * 系统参数配置
     * @auth true
     * @login true
     * @return string
     */
    public function config()
    {
        $form = new Form();
        $form->setTitle('系统参数配置');
        $form->image('web_logo', '网站LOGO')->size(50,50);
        $form->text('web_name', '网站名称')->inline()->required();
        $form->text('web_miitbeian', '网站备案号')->inline()->required();
        $form->text('web_copyright', '网站版权信息')->inline()->required();
        return $this->view($form);
    }
    /**
     * 系统配置信息
     * @return string
     */
    public function info(){
        $this->app->cache->delete('eadmin_node_list');
        $where= ['web_logo','web_name','web_miitbeian','web_copyright','web_theme'];
        $data = SystemConfig::whereIn('name',$where)->column('value','name');
        $this->successCode($data);
    }
    /**
     * 获取验证码
     */
    public function verify(){
        $verify = CaptchaService::instance()->create();
        $verifyKey = md5($this->app->request->ip().date('Y-m-d'));
        $verifyErrorNum = Cache::get($verifyKey);

        if(empty($verifyErrorNum)){
            $verify['mode']  = 0;
        }elseif ($verifyErrorNum < 3){
            $verify['mode']  = 1;
        }else{
            $verify['mode']  = 2;
        }
        $debug = env('APP_DEBUG') ? true : false;
        $verify['debug'] = $debug;
        if($debug === true){
            $verify['username'] = 'admin';
            $verify['password'] = 'admin';
        }
        $this->successCode($verify);
    }


}
