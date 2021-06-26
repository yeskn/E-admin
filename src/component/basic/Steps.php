<?php


namespace Eadmin\component\basic;


use Eadmin\component\Component;

class Steps extends Component
{
    protected $name = 'ASteps';
    
    public function __construct()
    {
        $this->bindAttValue('current',0);
    }
    /**
     * 步骤
     * @param string $title 标题
     * @param string $description 描述
     * @param string $icon 图标
     * @return $this
     */
    public function step($title='',$description='',$icon=''){
        $step  = new Step();
        $step->content($title,'title');
        $step->content($description,'description');
        $step->content($icon,'icon');
        $this->content($step);
      //  $step->attr('status',$status);
        return $this;
    }
    public static function create(){
        return new static();
    }
}
