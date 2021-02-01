<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2021-02-01
 * Time: 21:28
 */

namespace Eadmin\component\grid;


use Eadmin\component\basic\Confirm;
use Eadmin\component\Component;

/**
 * Class BatchAction
 * @package Eadmin\component\grid
 * @method $this url(string $value) ajax请求url
 * @method $this params(array $value) 提交ajax参数
 * @method $this confirm(string $value) 确认提示框
 */
class BatchAction extends Component
{   
    protected $name = 'BatchAction';
    public function __construct($content)
    {
        $this->content($content);
    }

    /**
     * 创建批量操作
     * @param $content
     * @return BatchAction
     */
    public static function create($content){
        return new self($content);
    }
}