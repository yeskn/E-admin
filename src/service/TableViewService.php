<?php


namespace Eadmin\service;


use think\facade\Cache;
use think\facade\Db;
use Eadmin\ApiJson;
use Eadmin\model\SystemTable;
use Eadmin\Service;

class TableViewService extends Service
{
    use ApiJson;

    public function list($grid)
    {
        return SystemTable::where('uid', AdminService::instance()->id())->where('grid', md5($grid))->select();
    }

    public function delete($id)
    {
        return SystemTable::where('id', $id)->delete();
    }

    public function get($id)
    {
        return SystemTable::find($id);
    }

    public function save($data)
    {
        $grid = $data['grid'];
        if (isset($data['id'])) {
            $data = SystemTable::update($data);
        } else {
            $data = SystemTable::create($data);
        }
        Cache::set(md5($grid . AdminService::instance()->id()), $data->toArray());
    }

    public function grid()
    {
        $eadmingrid = $this->app->request->param('eadmingrid');
        if ($eadmingrid) {
            $cacheKey = md5($eadmingrid . AdminService::instance()->id());
        } else {
            $moudel = app('http')->getName();
            $pathinfo = $this->app->request->pathinfo();
            $node = $moudel . '/' . $pathinfo;
            $query = $this->app->request->query();
            $params = '';
            if($query){
                $params = Db::name('system_menu')->where('url', $node)->where('params',$query)->value('params');
            }
            if(!$params){
                $params = Db::name('system_menu')->where('url', $node)->value('params');
            }
            if (empty($params)) {
                $params = '';
            } else {
                $paramsArrs = explode('&', $params);
                $params = [];
                foreach ($paramsArrs as $paramsArr) {
                    if (strstr($paramsArr, '=') !== false) {
                        list($key, $value) = explode('=', $paramsArr);
                        $params[] = [
                            'key' => $key,
                            'value' => $value
                        ];
                    }
                }
                $params = json_encode($params);
            }
            $cacheKey = md5('/' . $node . $params . AdminService::instance()->id());
        }
        return Cache::get($cacheKey);
    }

    public function registerRoute()
    {
        $this->app->route->post('eadmin/tableView/select', function () {
            $data = $this->app->request->post();
            Cache::set(md5($data['grid'] . AdminService::instance()->id()), $data['data']);
            $this->successCode();
        });
        $this->app->route->get('eadmin/tableView/:id', function ($id) {
            $this->successCode($this->get($id));
        });
        $this->app->route->get('eadmin/tableView', function () {
            $grid = $this->app->request->get('grid');
            $data = $this->list($grid);
            $cacheKey = md5($grid . AdminService::instance()->id());
            $selectData = Cache::get($cacheKey);
            if ($selectData) {
                $select = $selectData['id'];
            } else {
                $select = -1;
            }
            $this->successCode([
                'list' => $data,
                'select' => $select
            ]);
        });
        $this->app->route->post('eadmin/tableView', function () {
            $data = $this->app->request->post();
            $this->save($data);

            $this->successCode();
        });
        $this->app->route->delete('eadmin/tableView', function () {
            $this->delete($this->app->request->delete('id'));
            $this->successCode();
        });

    }
}
