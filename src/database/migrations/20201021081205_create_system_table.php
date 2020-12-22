<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateSystemTable extends Migrator
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
        $table = $this->table('system_table',['engine'=>'InnoDB','collation'=>'utf8mb4_unicode_ci'])->setComment('表格视图');
        $table->addColumn(Column::integer('uid')->setDefault(0)->setComment('用户id'));
        $table->addColumn(Column::string('name')->setComment('名称'));
        $table->addColumn(Column::string('grid',32)->setComment('表格标示'));
        $table->addColumn(Column::text('fields')->setComment('已选择字段'));
        $table->addColumn(Column::text('all_fields')->setComment('所有自选'));
        $table->addColumn(Column::integer('sort')->setDefault(0)->setComment('排序'));
        $table->addTimestamps();
        $table->create();
    }
}
