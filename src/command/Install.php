<?php


namespace Eadmin\command;


use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Console;

class Install extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('eadmin:install')->setDescription('安装 eadmin insall');
    }

    protected function execute(Input $input, Output $output)
    {
        $path = dirname(__DIR__);
        Console::call('migrate:eadmin',['cmd'=>'run','path'=>$path.'/database/migrations']);
        Console::call('seed:eadmin',['path'=> $path.'/database/seeds']);
        Console::call('eadmin:publish');
        copy($path.'/assets/admin.php',app()->getConfigPath().'admin.php');
        $output->writeln("<info>install success</info>");
    }
}
