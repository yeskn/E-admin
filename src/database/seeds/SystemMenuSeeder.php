<?php

use think\migration\Seeder;

class SystemMenuSeeder extends Seeder
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $datas = array (
  0 =>
  array (
    'id' => 2,
    'pid' => 0,
    'name' => '系统管理',
    'icon' => '',
    'url' => '#',
    'params' => '',
    'sort' => 2,
    'status' => 1,
    'create_at' => '2020-04-28 23:46:06',
    'update_time' => '2020-06-22 16:49:36',
  ),
  1 =>
  array (
    'id' => 3,
    'pid' => 4,
    'name' => '系统菜单管理',
    'icon' => 'el-icon-menu',
    'url' => 'admin/menu',
    'params' => '',
    'sort' => 6,
    'status' => 1,
    'create_at' => '2020-04-28 23:46:06',
    'update_time' => '2020-05-10 20:09:58',
  ),
  2 =>
  array (
    'id' => 4,
    'pid' => 2,
    'name' => '系统配置',
    'icon' => 'el-icon-s-tools',
    'url' => '#',
    'params' => '',
    'sort' => 18,
    'status' => 1,
    'create_at' => '2020-04-28 23:46:06',
    'update_time' => '2020-05-10 20:10:08',
  ),
  3 =>
  array (
    'id' => 5,
    'pid' => 12,
    'name' => '系统用户管理',
    'icon' => 'el-icon-user',
    'url' => 'admin/admin',
    'params' => '',
    'sort' => 1,
    'status' => 1,
    'create_at' => '2020-04-28 23:46:06',
    'update_time' => '2020-06-22 23:17:49',
  ),
  4 =>
  array (
    'id' => 7,
    'pid' => 12,
    'name' => '访问权限管理',
    'icon' => 'el-icon-lock',
    'url' => 'admin/auth',
    'params' => '',
    'sort' => 2,
    'status' => 1,
    'create_at' => '2020-04-28 23:46:06',
    'update_time' => '2020-06-22 23:23:49',
  ),
  5 =>
  array (
    'id' => 11,
    'pid' => 4,
    'name' => '系统参数配置',
    'icon' => 'el-icon-setting',
    'url' => 'admin/system/config',
    'params' => '',
    'sort' => 16,
    'status' => 1,
    'create_at' => '2020-04-28 23:46:06',
    'update_time' => '2020-07-12 21:58:31',
  ),
  6 =>
  array (
    'id' => 12,
    'pid' => 2,
    'name' => '权限管理',
    'icon' => 'el-icon-user-solid',
    'url' => '#',
    'params' => '',
    'sort' => 17,
    'status' => 1,
    'create_at' => '2020-04-28 23:46:06',
    'update_time' => NULL,
  ),
  12 =>
  array (
    'id' => 1012,
    'pid' => 0,
    'name' => '仪表盘',
    'icon' => 'fa fa-dashboard',
    'url' => 'admin/index/dashboard',
    'params' => '',
    'sort' => 0,
    'status' => 1,
    'create_at' => '2020-06-22 14:02:15',
    'update_time' => '2020-07-16 10:25:26',
  ),
  13 =>
  array (
    'id' => 1014,
    'pid' => 4,
    'name' => '数据库备份',
    'icon' => 'fa fa-stack-exchange',
    'url' => 'admin/backup',
    'params' => '',
    'sort' => 0,
    'status' => 1,
    'create_at' => '2020-07-01 23:00:58',
    'update_time' => '2020-07-01 23:01:14',
  ),
  14 =>
  array (
    'id' => 1015,
    'pid' => 4,
    'name' => '插件管理',
    'icon' => 'fa fa-assistive-listening-systems',
    'url' => 'admin/plug',
    'params' => '',
    'sort' => 0,
    'status' => 1,
    'create_at' => '2020-08-10 09:20:36',
    'update_time' => '2020-08-10 09:22:00',
  ),
);
        $this->table('system_menu')->insert($datas)->save();
    }
}
