<?php
use think\facade\Route;
Route::get('/',function (){
    return view('/index');
});
