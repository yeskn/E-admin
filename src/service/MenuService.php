<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-04-11
 * Time: 14:21
 */

namespace Eadmin\service;

use think\facade\Db;
use Eadmin\Service;

/**
 * 系统菜单服务
 * Class MenuService
 * @package app\admin\service
 */
class MenuService extends Service
{
    /**
     * 获取所有菜单
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function all()
    {
        $data = Db::name('system_menu')->where('status', 1)->order('sort asc,id desc')->cache(10)->select()->toArray();
        return $data;
    }

    /**
     * 生成树形菜单和路由
     * @param $data
     * @return array
     */
    public function treeMenus($data)
    {
        return $this->getTree($data);
    }

    /**
     * 生成菜单下拉框option
     * @param array $data
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function listOptions($data = [])
    {
        if(count($data) == 0){
            $data = Db::name('system_menu')->where('status', 1)->order('sort asc,id asc')->select();
        }
        $menusList = $this->getTreeLevel($data);
        foreach ($menusList as &$value) {
            $value['label'] = str_repeat("　├　", $value['level'] + 1) . $value['name'];
        }
        return $menusList;
    }

    protected function getTree($list, $pid = 0)
    {
        $tree = [];
        $resourceRouter = [];
        if (!empty($list)) {
            $newList = [];
            foreach ($list as $k => $v) {
                $params = [];
                if(!empty($v['params'])){
                    $paramsArrs = explode('&',$v['params']);
                    foreach ($paramsArrs as $paramsArr){
                        if(strstr($paramsArr,'=') !== false){
                            list($key,$value) = explode('=',$paramsArr);
                            $params[] = [
                                'key'=>$key,
                                'value'=>$value
                            ];
                        }
                    }
                }
                $v['meta'] = ['title' => $v['name'], 'icon' => $v['icon'], 'id' => $v['id'], 'pid' => $v['pid'],'params'=>$params];
                $v['component'] = 'Layout';
                $v['name'] = 'tag_' . $v['id'];
                $preg = "/^(https?:|mailto:|tel:)/";
                if ($v['url'] == '#') {
                    $v['path'] = $v['name'];
                } else {
                    if (preg_match($preg, $v['url'])) {
                        $v['path'] = $v['url'];
                    } else {
                        $v['path'] = '/' . $v['url'];
                    }
                }
                $newList[$v['id']] = $v;
            }
            $routers = array_values($newList);

            //资源路由
            foreach ($routers as $router) {
                $createRouter = $router;
                $createRouter['path'] = $createRouter['path'] . '/create\.rest';
                $createRouter['name'] = $createRouter['name'] . '$create';
                $createRouter['meta']['id'] = -1;
                $createRouter['meta']['title'] = $createRouter['meta']['title'].'添加';
                $editRouter = $router;
                $editRouter['path'] = $editRouter['path'] . '/:id/edit\.rest';
                $editRouter['name'] = $editRouter['name'] . '$edit';
                $editRouter['meta']['title'] = $editRouter['meta']['title'].'编辑';
                $editRouter['meta']['id'] = -1;
                $detailRouter = $router;
                $detailRouter['path'] = $detailRouter['path'] . '/:id\.rest';
                $detailRouter['meta']['title'] = $detailRouter['meta']['title'].'详情';
                $detailRouter['name'] = $detailRouter['name'] . '$detail';
                $detailRouter['meta']['id'] = -1;
                $resourceRouter[] = $router;
                $resourceRouter[] = $createRouter;
                $resourceRouter[] = $editRouter;
                $resourceRouter[] = $detailRouter;
            }

            //追加节点方法到路由
            foreach (NodeService::instance()->all() as $node) {
                $appendRouter = [];
                if ($node['method'] == 'get' || $node['method'] == 'any') {
                    $appendRouter['meta'] = ['title' => $node['label'], 'icon' => '', 'id' => -1, 'pid' => -1, 'params' => ''];
                    $appendRouter['component'] = 'Layout';
                    $appendRouter['path'] = '/' . $node['rule'];
                    $appendRouter['name'] = 'tag_' . mt_rand(100000, 999999);

                    $append = true;
                    array_push($resourceRouter, $appendRouter);
                }
            }

            //递归树形
            foreach ($newList as $value) {
                if ($pid == $value['pid']) {
                    $tree[] = &$newList[$value['id']];
                } elseif (isset($newList[$value['pid']])) {
                    $newList[$value['pid']]['children'][] = &$newList[$value['id']];
                }
            }
        }
        return [$resourceRouter, $tree];
    }

    protected function getTreeLevel($array, $pid = 0, $level = 0)
    {
        //声明静态数组,避免递归调用时,多次声明导致数组覆盖
        static $listMenus = [];
        foreach ($array as $key => $value) {
            //第一次遍历,找到父节点为根节点的节点 也就是pid=0的节点
            if ($value['pid'] == $pid) {
                //父节点为根节点的节点,级别为0，也就是第一级
                $value['level'] = $level;
                //把数组放到list中
                $listMenus[] = $value;
                //把这个节点从数组中移除,减少后续递归消耗
                unset($array[$key]);
                //开始递归,查找父ID为该节点ID的节点,级别则为原级别+1
                $this->getTreeLevel($array, $value['id'], $level + 1);
            }
        }
        return $listMenus;
    }
}
