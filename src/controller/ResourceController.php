<?php
declare (strict_types=1);

namespace Eadmin\controller;

use Eadmin\Controller;
use think\Request;

class ResourceController extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $call = $this->call()->exec();
        if (request()->has('eadmin_export')) {
            return $call->exportData();
        }
        return $call;
    }

    /**
     * 保存新建的资源
     *
     * @param \think\Request $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $form = $this->call()->exec();
        $res  = $form->save($request->post());
        if ($res !== false) {
            $url      = $form->redirectUrl();
            $response = admin_success('操作完成', '数据保存成功')->redirect($url);


        } else {
            admin_error_message('数据保存失败');
        }
    }

    public function create()
    {
        return $this->call()->exec();
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param int $id
     * @return \think\Response
     */
    public function edit($id)
    {
        return $this->call()->exec()->edit($id);
    }

    /**
     * 显示指定的资源
     *
     * @param int $id
     * @return \think\Response
     */
    public function read($id)
    {
        return $this->call()->exec();
    }

    /**
     * 保存更新的资源
     *
     * @param \think\Request $request
     * @param int $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        $url = '';
        if ($id == 'batch') {
            $res = $this->call()->exec()->update($this->request->put('eadmin_ids'), $request->put());
        } else {
            $form = $this->call()->exec();
            $url  = $form->redirectUrl();
            $res  = $form->save($request->put());
        }
        if ($res !== false) {
            $response = admin_success('操作完成', '数据保存成功')->redirect($url);
        } else {
            admin_error_message('数据保存失败');
        }
    }

    /**
     * 删除指定资源
     *
     * @param int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        if ($id == 'delete') {
            $ids = request()->delete('eadmin_ids');
        } else {
            $ids = explode(',', $id);
        }
        $res = $this->call()->exec()->destroy($ids);
        if ($res !== false) {
            admin_success('操作完成', '删除成功');
        } else {
            admin_error_message('数据保存失败');
        }
    }

    protected function call()
    {
        $class    = request()->param('eadmin_class');
        $action   = request()->param('eadmin_function');
        $instance = app($class);
        $reflect  = new \ReflectionMethod($instance, $action);
        $class = explode('\\',$class);
        $controller = end($class);
        $this->request->setController($controller);
        $data     = app()->invokeReflectMethod($instance, $reflect, $this->request->param());
        return $data;
    }
}
