<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2021-01-24
 * Time: 13:45
 */

namespace Eadmin\component\basic;


class Component extends \Eadmin\component\Component
{
    protected $name = 'component';

    public static function create($content)
    {
        $self = new static();
        $self->content($content);
        return $self;
    }
}