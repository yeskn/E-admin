<?php


namespace Eadmin\component\basic;


use Eadmin\component\Component;

class Html extends Component
{
    protected $name = 'html';
    protected $isComponent = false;
    public function __construct($thml)
    {
        $this->slot($thml);
    }
    public static function create($html){
        return new self($html);
    }
}
