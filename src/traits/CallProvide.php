<?php


namespace Eadmin\traits;


trait CallProvide
{
    protected $callMethod = [];
    protected $callClass;
    protected $callFunction;
    protected $callParams = [];
    protected $execClosure = null;
    public function getCallMethod($reset = false,$offset = 2)
    {
        if (empty($this->callMethod) || $reset) {
            $backtrace          = debug_backtrace(1, 3);
            $backtrace          = array_slice($backtrace, $offset);
            $backtrace          = $backtrace[0];
            $this->callClass    = $backtrace['class'];
            $this->callFunction = $backtrace['function'];
            $class              = new \ReflectionClass($this->callClass);
            try {
                $params = $class->getMethod($this->callFunction)->getParameters();
                foreach ($params as $key => $param) {
                    $name                    = $param->getName();
                    $this->callParams[$name] = isset($backtrace['args'][$key]) ? $backtrace['args'][$key] : $param->getDefaultValue();
                }
            }catch (\Exception $exception){

            }
            $this->callMethod = [
                'eadmin_class'    => $this->callClass,
                'eadmin_function' => $this->callFunction,
            ];
        }
        return array_merge($this->callMethod,$this->callParams);
    }
    public function setExec(\Closure $closure){
        $this->execClosure = $closure;
    }
    public function exec(){
        if(!is_null($this->execClosure)){
            call_user_func($this->execClosure,$this);
        }
        return $this;
    }
    public function renderable()
    {
        $method = $this->callFunction;
        return app($this->callClass)->$method();
    }
}
