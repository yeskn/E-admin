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
    public static function create(string $content)
    {
        $self =  new self();
        $self->content($content);
        return $self;
    }
}