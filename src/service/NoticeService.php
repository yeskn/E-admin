<?php


namespace Eadmin\service;

use Eadmin\model\AdminModel;
use Eadmin\model\SystemNotice;
use Eadmin\Service;

/**
 * 系统通知服务
 * Class NoticeService
 * @package Eadmin\service
 */
class NoticeService extends Service
{
    protected $cacheKey = 'eadmin_notice_list';
    /**
     * 推送通知(图标)-指定用户
     * @param $uid 用户id
     * @param $title 标题
     * @param $content 内容
     * @param $icon 图标
     * @param $iconColor 图标颜色
     * @param $url 跳转链接
     */
    public function pushIcon($uid,$title, $content,$icon,$iconColor='',$url='' )
    {
        return $this->push($uid,$title, $content,$icon,$iconColor,$url,1);
    }
    /**
     * 推送通知(头像图片)-指定用户
     * @param $uid 用户id
     * @param $title 标题
     * @param $content 内容
     * @param $avatar 头像图片
     * @param $iconColor 图标颜色
     * @param $url 跳转链接
     */
    public function pushAvatar($uid,$title, $content,$avatar,$iconColor='',$url='' )
    {
        return $this->push($uid,$title, $content,$avatar,$iconColor,$url,2);
    }
    /**
     * 推送通知(图标)-全部用户
     * @param $title 标题
     * @param $content 内容
     * @param $icon 图标
     * @param $iconColor 图标颜色
     * @param $url 跳转链接
     */
    public function pushIconAll($title, $content,$icon,$iconColor='',$url=''){
        return $this->pushAll($title, $content,$icon,$iconColor,$url,1);
    }
    /**
     * 推送通知(图标)-全部用户
     * @param $title 标题
     * @param $content 内容
     * @param $avatar 头像图片
     * @param $iconColor 图标颜色
     * @param $url 跳转链接
     */
    public function pushAvatarAll($title, $content,$avatar,$iconColor='',$url=''){
        return $this->pushAll($title, $content,$avatar,$iconColor,$url,2);
    }
    /**
     * 推送通知(图标)-全部用户
     * @param $title 标题
     * @param $content 内容
     * @param $icon 图标
     * @param $iconColor 图标颜色
     * @param $url 跳转链接
     * @param $type 类型:1图标,2头像
     */
    public function pushAll($title, $content,$icon,$iconColor='',$url='',$type){
        $uids = AdminModel::column('id');
        foreach ($uids as $uid){
            if($type == 1){
                $res = $this->pushIcon($uid,$title,$content,$icon,$iconColor,$url);
            }else{
                $res = $this->pushAvatar($uid,$title,$content,$icon,$iconColor,$url);
            }

        }
        return $res;
    }
    /**
     * 推送通知(图标)-指定用户
     * @param $uid 用户id
     * @param $title 标题
     * @param $content 内容
     * @param $avatar 图标
     * @param $iconColor 图标颜色
     * @param $url 跳转链接
     * @param $type 类型:1图标,2头像
     */
    protected function push($uid,$title, $content,$avatar,$iconColor='',$url='',$type=1)
    {
        $cacheKey = $this->cacheKey . $uid;
        $pushData = [
            'title' => $title,
            'content' => $content.PHP_EOL.PHP_EOL.date('Y-m-d H:i'),
            'url' => $url,
            'avatar'=>$avatar,
            'type'=>$type,
        ];
        if($this->app->cache->has($cacheKey)){
            $pushDatas = $this->app->cache->get($cacheKey);
            array_push($pushDatas,$pushData);
        }else{
            $pushDatas[] = $pushData;
        }
        SystemNotice::create([
            'user_id'=>$uid,
            'title'=>$title,
            'content'=>$content,
            'target_url'=>$url,
            'type'=>$type,
            'avatar'=>$avatar,
            'color'=>$iconColor
        ]);
        return $this->app->cache->set($cacheKey,$pushDatas);
    }

    /**
     * 接收当前用户通知消息
     * @return mixed
     */
    public function receive()
    {
        $uid = AdminService::instance()->id();
        $cacheKey = $this->cacheKey . $uid;
        $pushDatas = $this->app->cache->pull($cacheKey);
        return $pushDatas;
    }
}
