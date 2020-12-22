<?php


namespace Eadmin\model;


use think\Model;
use Eadmin\service\AdminService;

class SystemTable extends BaseModel
{
    protected $json = ['fields','all_fields'];
    protected $jsonAssoc = true;
    public static function onBeforeInsert($data)
    {
        $data['uid'] = AdminService::instance()->id();
    }

    protected function setGridAttr($val)
    {
        return md5($val);
    }

}
