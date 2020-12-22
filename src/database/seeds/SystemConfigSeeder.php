<?php

use think\migration\Seeder;

class SystemConfigSeeder extends Seeder
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
        $data = [
            [
                'name' => 'web_name',
                'value' => 'E-admin',
            ],
            [
                'name' => 'web_logo',
                'value' => 'E-admin',
            ],
            [
                'name' => 'web_miitbeian',
                'value' => '粤ICP备16006642号-2',
            ],
            [
                'name' => 'web_copyright',
                'value' => '©版权所有 2014-2020',
            ],
            [
                'name' => 'databackup_on',
                'value' => '1',
            ],
            [
                'name' => 'database_number',
                'value' => '10',
            ],
            [
                'name' => 'database_day',
                'value' => '1',
            ],
        ];
        $this->table('system_config')->insert($data)->save();
    }
}
