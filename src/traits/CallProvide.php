<?php


namespace Eadmin\traits;


trait CallProvide
{
    protected $callMethod = [];
    protected $callClass;
    protected $callFunction;

    public function getCallMethod()
    {
        if(empty($this->callMethod)){
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
            $backtrace = array_slice($backtrace, 2);
            $this->callClass = $backtrace[0]['class'];
            $this->callFunction = $backtrace[0]['function'];
            $this->callMethod = [
                'eadmin_class' => $this->callClass,
                'eadmin_function' => $this->callFunction,
            ];
        }
        return $this->callMethod;
    }

    public function renderable()
    {
        $method = $this->callFunction;
        $a = app($this->callClass)->$method();

        return app($this->callClass)->$method();
    }
}
