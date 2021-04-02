<?php


namespace Eadmin\component\basic;


use Eadmin\component\Component;

/**
 * Class Html
 * @package Eadmin\component\basic
 * @method $this style(array $value) 样式
 */
class Html extends Component
{
    protected $name = 'html';

    public function __construct($content = '')
    {
        $this->tag('span');
        if (!empty($content) || is_numeric($content)) {
            $this->content($content);
        }
    }

    /**
     * 自定义元素标签
     * @param string $tag 元素标签
     */
    public function tag($tag)
    {
        $this->attr('data-tag', $tag);
        return $this;
    }

    public static function create($content = '')
    {
        return new static($content);
    }
}
