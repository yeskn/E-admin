<?php

use rockySysLog\model\SystemLog;
use Eadmin\service\TokenService;


if (!function_exists('sysconf')) {
    function sysconf($name, $value = null)
    {
        return \Eadmin\tools\Data::sysconf($name,$value);
    }
}
if (!function_exists('eadmin_log')) {
    function eadmin_log($action, $content)
    {
        SystemLog::create([
            'username' => TokenService::instance()->user()->nickname ?? 'cli',
            'geoip' => request()->ip(),
            'action' => $action,
            'node' => request()->url(),
            'content' => $content,
        ]);
    }
}
if (!function_exists('eadmin_success')) {
    function eadmin_success($title, $message)
    {
        return \Eadmin\facade\Component::notification()->success($title, $message);
    }
}

if (!function_exists('eadmin_error')) {
    function eadmin_error($title, $message)
    {
        return \Eadmin\facade\Component::notification()->error($title, $message);
    }
}
if (!function_exists('eadmin_info')) {
    function eadmin_info($title, $message)
    {
        return \Eadmin\facade\Component::notification()->info($title, $message);
    }
}
if (!function_exists('eadmin_warn')) {
    function eadmin_warn($title, $message)
    {
        return  \Eadmin\facade\Component::notification()->warning($title, $message);
    }
}

if (!function_exists('eadmin_msg_warn')) {
    function eadmin_msg_warn($message)
    {
        return \Eadmin\facade\Component::message()->warning($message);
    }
}
if (!function_exists('eadmin_msg_success')) {
    function eadmin_msg_success($message)
    {
        return \Eadmin\facade\Component::message()->success($message);
    }
}
if (!function_exists('eadmin_msg_error')) {
    function eadmin_msg_error($message)
    {
        return \Eadmin\facade\Component::message()->error($message);
    }
}
if (!function_exists('eadmin_msg_info')) {
    function eadmin_msg_info($message)
    {
        return \Eadmin\facade\Component::message()->info($message);
    }
}

