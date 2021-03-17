<?php


namespace Eadmin\component\basic;


use Eadmin\component\Component;

class Steps extends Component
{
    protected $name = 'ElSteps';
    public function __construct()
    {
        $this->attr('alignCenter',true);
        $this->bindAttValue('active',1);
    }
    public function step($title='',$description='',$icon=''){
        $step  = new Step();
        $step->attr('title',$title);
        $step->attr('description',$description);
        $step->icon($icon);
        $this->content($step);
        return $this;
    }
    public static function create(){
        return new static();
    }
}
