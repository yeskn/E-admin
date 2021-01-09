<?php


namespace Eadmin\component\basic;


use Eadmin\component\Component;

class Html extends Component
{
    protected $name = 'html';
    public static function create(){
        return new static();
    }
}
