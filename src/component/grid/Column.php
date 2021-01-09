<?php


namespace Eadmin\component\grid;


use Eadmin\component\basic\Html;
use Eadmin\component\basic\Tag;
use Eadmin\component\Component;
use Eadmin\component\form\field\Rate;
use Eadmin\component\layout\Content;

/**
 * Class Column
 * @link https://element-plus.gitee.io/#/zh-CN/component/table
 * @package Eadmin\component\grid
 * @method $this type(string $type) 对应列的类型  selection/index/expand
 * @method $this prop(string $value) 对应列内容的字段名
 * @method $this align(string $value)    left/center/right
 * @method $this headerAlign(string $value)    left/center/right
 * @method $this fixed(string $value) true, left, right
 * @method $this width(int $value) 对应列的宽度
 * @method $this minWidth(int $value) 对应列的最小宽度
 */
class Column extends Component
{
    protected $name = 'ElTableColumn';
    protected $prop;
    protected $closure = null;
    //内容颜射
    protected $usings = [];
    //映射标签颜色
    protected $tagColor = [];
    //映射标签颜色主题
    protected $tagTheme = 'light';
    protected $tag = null;

    public function __construct($prop, $label)
    {
        if (!empty($prop)) {
            $this->prop = $prop;
            $this->prop($prop);
        }
        if (!empty($label)) {
            $this->label($label);
        }
    }
    /**
     * 评分显示
     * @param int $max 最大长度
     * @return $this
     */
    public function rate($max = 5)
    {
        $this->display(function ($val) use ($max) {
            return Rate::create(null,$val)->max($max)->disabled();
        });
        return $this;
    }
    /**
     * 标签显示
     * @param $color 标签颜色：success，info，warning，danger
     * @param $theme 主题：dark，light，plain
     * @param $size 尺寸:medium，small，mini
     */
    public function tag($color = '', $theme = 'dark', $size = 'mini')
    {
        $this->tag = Tag::create()->type($color)->size($size)->effect($theme);
        return $this;
    }
    /**
     * 解析每行数据
     * @param $data 数据
     */
    public function row(&$data)
    {
        $value = '';
        if (isset($data[$this->prop])) {
            $value = $data[$this->prop];
        }
        //映射内容颜色处理
        if (isset($this->tagColor[$value])) {
            $this->tag($this->tagColor[$value], $this->tagTheme);
        }
        //映射内容处理
        if (count($this->usings) > 0 && isset($this->usings[$value])) {
            $value = $this->usings[$value];
        }
        //是否显示标签
        if(!is_null($this->tag)){
            $value = $this->tag->content($value);
        }
        //自定义内容显示处理
        if (!is_null($this->closure)) {
            $value = call_user_func_array($this->closure, [$value, $data]);
        }
        $data[$this->prop] = Html::create()->content($value);
        return $this;
    }

    /**
     * 显示的标题
     * @param string $label
     * @return $this
     */
    public function label(string $label)
    {
        $this->attr('header', Html::create()->content($label));
        return $this;
    }

    /**
     * 排序
     * @return $this
     */
    public function sortable()
    {
        $this->attr('sortable', 'custom');
        return $this;
    }

    /**
     * 内容映射
     * @param array $usings 映射内容
     * @param array $tagColor 标签颜色
     * @param tagTheme 标签颜色主题：dark，light，plain
     */
    public function using(array $usings, array $tagColor = [], $tagTheme = 'light')
    {
        $this->tagColor = $tagColor;
        $this->tagTheme = $tagTheme;
        $this->usings = $usings;
        return $this;
    }

    /**
     * 自定义显示
     * @param \Closure $closure
     * @return $this
     */
    public function display(\Closure $closure)
    {
        $this->closure = $closure;
        return $this;
    }
}
