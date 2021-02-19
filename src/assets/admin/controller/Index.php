<?php


namespace app\admin\controller;

use Eadmin\chart\Echart;

use Eadmin\component\basic\Card;
use Eadmin\component\basic\Tabs;
use Eadmin\component\layout\Content;
use Eadmin\component\layout\Row;
use Eadmin\Controller;
use Eadmin\grid\Filter;


/**
 * 控制台
 * Class Index
 * @package app\admin\controller
 */
class Index extends Controller
{
    /**
     * 仪表盘
     * @auth true
     * @login true
     * @method get
     */
    public function dashboard(Content $content)
    {

        $tab = Tabs::create()
            ->tabPosition('left')
            ->pane('折线图',$this->lineEchart())
//            ->pane('漏斗图',$this->funnelchart())
//            ->pane('饼图',$this->pieEchart())
//            ->pane('祝转图',$this->barEchart())
            ->pane('柱状图',$this->radarEchart());
        $content->title('数据分析');
        $content->row(Card::create($tab));
        return $content;
    }

    /**
     * 雷达图
     */
    public function radarEchart()
    {
        $echart = new Echart('雷达图', 'radar', '400px');
        //不分组直接调用
//        $echart->table('system_user','create_time');
//        $echart->count('不分组系统用户数量',2);
//        $echart->sum('系统用户id总和','id',50);
//        $echart->sum('系统用户id1','id',50);
//        $echart->sum('系统用户id2','id',50);
//        $echart->sum('系统用户id3','id',100);

        //多模块分组
        $echart->group('系统用户', function ($echart) {
            $echart->table('system_user', 'create_time', 50);
            $echart->count('系统用户数量', 50);
            $echart->sum('系统用户id总和', 'id', 50);
        });
        $echart->group('日志', function ($echart) {
            $echart->table('system_user', 'create_time', 50);
            $echart->count('日志总数量', 50);
            $echart->sum('日志id总和', 'id', 1000);
        });
        return $echart;
    }

    /**
     * 漏斗图
     */
    public function funnelchart()
    {
        $echart = new Echart('漏斗图', 'funnel', '400px');
        $echart->table('system_user', 'create_time');
        $echart->count('日志总数量');
        $echart->sum('日志id总和', 'id');
        $echart->avg('日志id平均', 'id');
        return $echart;
    }

    /**
     * 饼图
     */
    public function pieEchart()
    {
        $echart = new Echart('饼图', 'pie', '400px');
        //不分组直接调用
        $echart->table('system_user', 'create_time');
        $echart->count('不分组系统用户数量');

        //多模块分组
        $echart->group('系统用户', function ($echart) {
            $echart->table('system_user', 'create_time');
            $echart->count('系统用户数量');
            $echart->sum('系统用户id总和', 'id');
        });
        $echart->group('日志', function ($echart) {
            $echart->table('system_user', 'create_time');
            $echart->count('日志总数量');
            $echart->sum('日志id总和', 'id');
            $echart->avg('日志id平均', 'id');
        });

        return $echart;
    }

    /**
     * 折线图
     */
    public function lineEchart()
    {
        $echart = new Echart('折线图', 'line');
        $echart->table('system_user');
        //筛选
        $echart->filter(function (Filter $filter) {
            $filter->eq('id', 'id');
        });
        $echart->count('系统用户', function ($query) {
            $query->where('id', 1);
        });
        $echart->sum('123', 'id');
        $echart->sum('ff', 'id');
        return $echart;
    }

    /**
     * 柱状图
     */
    public function barEchart()
    {
        $echart = new Echart('折线图', 'bar');
        $echart->table('system_user');
        //筛选
        $echart->filter(function (Filter $filter) {
            $filter->eq('id', 'id');
        });
        $echart->count('系统用户', function ($query) {
            $query->where('id', 1);
        });
        $echart->sum('123', 'id');
        $echart->sum('ff', 'id');
        return $echart;
    }
}
