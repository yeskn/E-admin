<?php


namespace Eadmin\component\basic;


use Eadmin\component\Component;

class Html extends Component
{
    protected $name = 'html';
    public function __construct($content)
    {
        if(!empty($content)){
            $this->content($content);
        }
    }
    public static function create($content=''){
        return new static($content);
    }
}
