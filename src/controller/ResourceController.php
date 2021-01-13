<?php
declare (strict_types = 1);

namespace Eadmin\controller;

use think\Request;

class ResourceController
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        halt(1);
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $res = $this->call()->save($request->post());
        if ($res !== false) {
            return json(['code'=>200]);
            eadmin_success('操作完成','数据保存成功');
        } else {
            eadmin_msg_error('数据保存失败');
        }
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        halt(2);
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        $res = $this->call()->save($request->post());
        if ($res !== false) {
            return json(['code'=>200]);
            eadmin_success('操作完成','数据保存成功');
        } else {
            eadmin_msg_error('数据保存失败');
        }
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        $res = $this->call()->destroy($id);
        if ($res !== false) {
            return json(['code'=>200]);
            eadmin_success('操作完成','删除成功');
        } else {
            eadmin_msg_error('数据保存失败');
        }
    }
    protected function call(){
        $class = request()->param('eadmin_class');
        $function = request()->param('eadmin_function');
        return app($class)->$function();
    }
}
