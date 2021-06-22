<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateUserProfile extends Migrator
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
		$table = $this->table('user_profile', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_unicode_ci'])->setComment('用户表');
		$table->addColumn(Column::integer('uid')->setDefault(0)->setComment('头像'))
			->addColumn(Column::string('country', 100)->setDefault('')->setComment('地区'))
			->addColumn(Column::string('province', 100)->setDefault('')->setComment('省份'))
			->addColumn(Column::string('city', 100)->setDefault('')->setComment('城市'))
			->addColumn(Column::string('area', 100)->setDefault('')->setComment('地区'))
			->addColumn(Column::string('address')->setDefault('')->setComment('详情地址'))
			->addColumn(Column::string('withdraw_name', 100)->setDefault('')->setComment('提现姓名'))
			->addColumn(Column::integer('withdraw_id')->setDefault(0)->setComment('提现id'))
			->addColumn(Column::char('withdraw_phone', 20)->setDefault('')->setComment('提现手机号'))
			->addColumn(Column::string('withdraw_card')->setDefault('')->setComment('提现卡号'))
			->addForeignKey('uid', 'user', 'id', [
				'delete' => 'CASCADE',
				'update' => 'NO_ACTION',
			])
			->create();
	}
}
