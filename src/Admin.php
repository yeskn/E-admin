<?php


namespace Eadmin;


use Eadmin\controller\ResourceController;

class Admin
{
    public static function registerRoute(){

        app()->route->resource('eadmin',ResourceController::class)->ext('rest');;
    }
}
