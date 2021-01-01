<?php


namespace Eadmin\component\basic;


use Eadmin\component\Component;

class Card extends Component
{
    protected $name = 'ElCard';
    public function __construct($content)
    {
         $this->content($content);
    }
    /**
     * 创建卡片
     * @return Card
     */
    public static function create($content=''){
        return new self($content);
    }

    /**
     * 头部内容
     * @param $content
     */
    public function header($content){
        return $this->slot($content,'header');
    }

    /**
     * 内容
     * @param $content
     */
    public function content($content){
        return $this->slot($content);
    }
}