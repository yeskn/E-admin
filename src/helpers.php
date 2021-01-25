<?php

use rockySysLog\model\SystemLog;
use admin\service\TokenService;
use Eadmin\Admin;
if (!function_exists('sysconf')) {
    function sysconf($name, $value = null)
    {
        return Admin::sysconf($name,$value);
    }
}
if (!function_exists('admin_log')) {
    function admin_log($action, $content)
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

if (!function_exists('admin_success')) {
    function admin_success($title, $message)
    {
        return Admin::notification()->success($title, $message);
    }
}

if (!function_exists('admin_error')) {
    function admin_error($title, $message)
    {
        return Admin::notification()->error($title, $message);
    }
}
if (!function_exists('admin_info')) {
    function admin_info($title, $message)
    {
        return Admin::notification()->info($title, $message);
    }
}
if (!function_exists('admin_warn')) {
    function admin_warn($title, $message)
    {
        return Admin::notification()->warning($title, $message);
    }
}

if (!function_exists('admin_warn_message')) {
    function admin_warn_message($message)
    {
        return Admin::message()->warning($message);
    }
}
if (!function_exists('admin_success_message')) {
    function admin_success_message($message)
    {
        return Admin::message()->success($message);
    }
}
if (!function_exists('admin_error_message')) {
    function admin_error_message($message)
    {
        return Admin::message()->error($message);
    }
}
if (!function_exists('admin_info_message')) {
    function admin_info_message($message)
    {
        return Admin::message()->info($message);
    }
}

