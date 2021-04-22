<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2021-02-11
 * Time: 08:58
 */

namespace Eadmin\detail;

use Eadmin\component\basic\Card;
use Eadmin\component\basic\Html;
use Eadmin\component\basic\Image;
use Eadmin\component\Component;
use Eadmin\component\layout\Column;
use Eadmin\component\layout\Row;
use Eadmin\contract\DetailInterface;
use Eadmin\grid\Grid;
use Eadmin\traits\CallProvide;
use think\facade\Request;
use think\helper\Arr;

class Detail extends Html
{
    use CallProvide;

    protected $data = null;
    protected $title = '详情';
    protected $card = null;
    protected $row;
    protected $fields = [];

    public function __construct($data)
    {
        parent::__construct();
        $this->data = $data;
        $this->getCallMethod();
        $this->title($this->title);
        $this->card = $this->createCard();
        $this->bind('eadmin_description', '详情');
    }

    /**
     * 设置标题
     * @param string $title
     * @return string
     */
    public function title(string $title)
    {
        $this->title = $title;
        return $this->bind('eadmin_title', $title);
    }

    /**
     * 添加一行布局
     * @param \Closure $closure
     * @return $this
     */
    public function row(\Closure $closure)
    {
        $row = new Row();
        $row->gutter(10);
        $fields = $this->collectFields($closure);
        foreach ($fields as $field) {
            $row->content($field);
        }
        $this->push($row);
        return $this;
    }

    public function collectFields(\Closure $closure)
    {
        $offset = count($this->fields);
        call_user_func($closure, $this);
        $fields       = array_slice($this->fields, $offset);
        $this->fields = array_slice($this->fields, 0, $offset);
        return $fields;
    }

    public function grid($relation, $title, \Closure $closure)
    {
        $grid = new Grid($this->getData($relation));
        $grid->hideTools();
        $grid->hideAction();
        $grid->hidePage();
        $grid->hideSelection();
        call_user_func($closure, $grid);
        $card = $this->createCard()->header("<b>{$title}</b>");
        $card->attr('bodyStyle', ['padding' => '0px']);
        $card->content($grid);
        $this->push($card);
        return $this;
    }

    protected function createCard()
    {
        return Card::create()->attr('bodyStyle', ['padding' => '0 15px'])->attr('style', ['marginBottom' => ' 10px']);
    }

    /**
     * 卡片布局
     * @param string $title 标题
     * @param \Closure $closure
     * @param int $md
     * @return $this
     */
    public function card($title, \Closure $closure, $md = 24)
    {
        $card   = $this->createCard()->header(Html::create($title)->tag('b'));
        $fields = $this->collectFields($closure);
        $row    = new Row();
        $row->gutter(5);
        foreach ($fields as $field) {
            $row->content($field);
        }
        $card->content($row);
        $column = new Column();
        $column->span($md);
        $column->content($card);
        $this->push($column);
        return $this;
    }

    /**
     * 头像昵称列
     * @param string $avatar 头像
     * @param string $nickname 昵称
     * @param string $label 标签
     * @return Column
     */
    public function userInfo($avatar = 'headimg', $nickname = 'nickname', $label = '用户信息')
    {
        $field = $this->field($avatar, $label);
        return $field->display(function ($val, $data) use ($nickname) {

            $nicknameValue = $this->getData($nickname);
            return Card::create(
                Html::create([
                    Image::create()->src($val)->fit('cover')->previewSrcList([$val])->attr('style', ['width' => '80px', 'height' => '80px', 'borderRadius' => '50%']),
                    "<br>{$nicknameValue}"
                ])->attr('style', ['textAlign' => 'center', 'lineHeight' => '25px', 'display' => 'block'])
            )->bodyStyle(['padding' => '10px']);
        });
    }

    public function field($field, $label = '')
    {
        $field = new Field($label, $this->getData($field), $this->data);
        $this->push($field);
        return $field;
    }

    protected function push($field)
    {
        $this->fields[] = $field;
    }

    /**
     * 获取数据
     * @param null $field 字段，默认全部
     * @return |null
     */
    public function getData($field = null)
    {
        if (is_null($field)) {
            return $this->data;
        }
        return Arr::get($this->data, $field);
    }

    public function jsonSerialize()
    {
        foreach ($this->fields as $field) {
            if ($field instanceof Field) {
                if (is_null($this->row)) {
                    $this->row = new Row();
                    $this->row->gutter(5);
                }
                $this->row->content($field);
            } else {
                $this->content($field);
            }
        }
        if (!is_null($this->row)) {
            if (!isset($this->content['default'])) {
                $this->content['default'] = [];
            }
            if (Request::has('eadmin_layout')) {
                array_unshift($this->content['default'], Html::create($this->row)->tag('div')->attr('style', ['padding' => '20px']));
            } else {

                array_unshift($this->content['default'], $this->card->content($this->row));
            }
        }
        return parent::jsonSerialize(); // TODO: Change the autogenerated stub
    }
}
