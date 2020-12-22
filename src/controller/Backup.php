<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-07-01
 * Time: 22:59
 */

namespace Eadmin\controller;


use Eadmin\service\BackupData;
use Eadmin\controller\BaseAdmin;
use Eadmin\facade\Button;
use Eadmin\facade\Component;
use Eadmin\form\Form;
use Eadmin\grid\Column;
use Eadmin\grid\Table;
use Eadmin\layout\Content;
use Eadmin\layout\Row;


/**
 * 数据库备份
 * Class Backup
 * @package
 */
class Backup extends BaseAdmin
{
    /**
     * 数据库备份列表
     * @auth true
     * @login true
     * @return $this
     */
    protected function grid()
    {
        if (request()->method() == 'DELETE') {
            return $this;
        }
        $content = new Content();
        $content->row(function (Row $row) {
            $row->columnComponentUrl('backup/config');
        });
        $content->rowComponent($this->table());
        $this->view($content);
    }


    //删除备份文件
    protected function destroy($id)
    {
        if (BackupData::instance()->delete($id)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 备份配置
     * @auth true
     * @login true
     */
    public function config()
    {

        $form = new Form();
        $form->setAttr('inline', true);
        $form->setAttr('size', 'mini');
        $form->labelPosition('right', 120);
        $form->radio('databackup_on', '自动备份')->options([
            1 => '开启',
            0 => '关闭',
        ])->default(0)->themeButton();
        $form->number('database_number', '最多保留')->setAttr('style', 'width:150px')->min(1)->append('份')->required();
        $form->number('database_day', '	数据库每')->min(1)->setAttr('style', 'width:180px')->append('天自动备份')->required();
        $button = Button::create('备份数据库', 'primary', 'mini', '', true)->save('', [], 'backup/add', '', true);

        $form->appendSubmitExtend($button);
        return $this->view($form);
    }

    /**
     * 还原数据库
     * @auth true
     * @login true
     */
    public function reduction()
    {
        if (BackupData::instance()->reduction()) {
            Component::message()->success('数据库还原完成')->refresh();
        } else {
            Component::message()->error('数据库还原失败');
        }
    }

    /**
     * 备份数据库
     * @auth true
     * @login true
     */
    public function add()
    {
        $res = BackupData::instance()->backup();
        if ($res === true) {
            Component::message()->success('数据库备份成功')->refresh();
        } else {
            Component::message()->error($res);
        }

    }

    protected function table()
    {
        $datas = BackupData::instance()->getBackUpList();
        $columns[] = new Column('name', '备份名称');
        $columns[] = new Column('size', '备份大小');
        $columns[] = new Column('create_time', '备份时间');
        $columns[] = new Column('action', '操作');
        foreach ($datas as $rows) {
            foreach ($columns as $column) {
                $field = $column->getField();
                if ($field == 'action') {
                    $column->display(function () use ($rows) {
                        $button = Button::create('还原', 'primary')->save($rows['id'], ['name' => $rows['id']], 'backup/reduction', '确认还原备份？');
                        $button .= Button::create('删除', 'danger')->delete($rows['id'], '确认删除？');
                        return $button;
                    });
                }
                $column->setData($rows);
            }
        }
        $table = new Table($columns, $datas);
        return $table->view();
    }
}
