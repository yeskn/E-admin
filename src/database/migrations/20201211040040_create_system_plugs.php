<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateSystemPlugs extends Migrator
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $table = $this->table('system_plugs',['engine'=>'InnoDB','collation'=>'utf8mb4_unicode_ci'])->setComment('系统插件');
        $table->addColumn(Column::string('name')->setComment('名称'));
        $table->addColumn(Column::tinyInteger('status')->setDefault(1)->setComment('状态:1启用,0禁用'));
        $table->addTimestamps();
        $table->create();
    }
}
