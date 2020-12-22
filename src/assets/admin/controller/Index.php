<?php


namespace app\admin\controller;

use Eadmin\chart\Echart;

use Eadmin\controller\BaseAdmin;
use Eadmin\facade\CountCard;
use Eadmin\facade\Quick;
use Eadmin\grid\Filter;
use Eadmin\layout\Content;

use Eadmin\layout\Row;

/**
 * 控制台
 * Class Index
 * @package app\admin\controller
 */
class Index extends BaseAdmin
{
    /**
     * 仪表盘
     * @auth true
     * @login true
     * @method get
     */
    public function dashboard(Content $content)
    {
        $content->row(function (Row $row) {
            $row->gutter(10);
            $row->column(Quick::create('微信授权配置', 'el-icon-mobile-phone', 'green')->href('/wechat/config/wechat')->render(), 8);
            $row->column(Quick::create('系统用户', 'el-icon-user-solid', '#4486f1')->href('/admin/admin')->render(), 8);
            $row->column(Quick::create('打开百度', 'el-icon-map-location', 'red')->href('http://www.baidu.com')->render(), 8);
        });
        $content->row(function (Row $row) {
            $row->gutter(10);
            $card = CountCard::create('访问量', 100,1000,'el-icon-mobile-phone', '#4486f1');
            $row->column($card, 6)->clickLink(url('lineEchart'),'lineEchartName');
            $card = CountCard::create('用户', 200,500,'el-icon-user-solid', 'red','周','info');
            $row->column($card, 6)->clickLink(url('radarEchart'),'lineEchartName');
            $card =CountCard::create('访问量', 300,300,'el-icon-mobile-phone', '#4486f1','月','warning');
            $row->column($card, 6)->clickLink(url('funnelchart'),'lineEchartName');
            $card = CountCard::create('用户', 400.54,648.39,'el-icon-user-solid', 'red','年','success');
            $row->column($card, 6)->clickLink(url('barEchart'),'lineEchartName');

        });
        $content->rowComponentUrl(url('lineEchart'),24,'lineEchartName');
        $content->row(function (Row $row) {
            $row->gutter(10);
            $row->columnComponentUrl(url('pieEchart'), 12);
            $row->columnComponentUrl(url('radarEchart'), 12);
        });
        $content->row(function (Row $row) {
            $row->gutter(10);
            $row->columnComponentUrl(url('funnelchart'), 12);
            $row->columnComponentUrl(url('barEchart'), 12);
        });
        $this->view($content);
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

        $this->view($echart);
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
        $this->view($echart);
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

        $this->view($echart);
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
        $this->view($echart);
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
        $this->view($echart);
    }
}
