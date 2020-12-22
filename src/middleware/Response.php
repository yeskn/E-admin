<?php


namespace Eadmin\middleware;


use Eadmin\facade\Component;
use Eadmin\form\Form;
use Eadmin\grid\Detail;
use Eadmin\grid\Grid;
use Eadmin\layout\Content;
use think\Request;

class Response
{
    public function handle(Request $request, \Closure $next)
    {
        $response = $next($request);
        $build = $response->getData();
        if($build instanceof Grid || $build instanceof Form || $build instanceof Detail){
            $content = new Content();
            $view = $content->title($build->title())->body($build)->view();
            Component::view($view);
        }
        return $response;
    }

}
