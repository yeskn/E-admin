<?php


namespace Eadmin\traits;


trait CallProvide
{
    protected $callMethod = [];
    protected $callClass;
    protected $callFunction;
    protected $callParams = [];
    public function getCallMethod()
    {
        if(empty($this->callMethod)){
            $backtrace = debug_backtrace(1,3);
            $backtrace = array_slice($backtrace, 2);
            $backtrace = $backtrace[0];
            $this->callClass = $backtrace['class'];
            $this->callFunction = $backtrace['function'];
            $class = new \ReflectionClass($this->callClass);
            $params = $class->getMethod( $this->callFunction)->getParameters();
            foreach ($params as $key=>$param){
                $name = $param->getName();
                $this->callParams[$name] = isset($backtrace['args'][$key]) ? $backtrace['args'][$key] : $param->getDefaultValue();
            }
            $this->callMethod = [
                'eadmin_class' => $this->callClass,
                'eadmin_function' => $this->callFunction,
            ];
        }
        return $this->callMethod;
    }
    public function getCallParams(){
        return $this->callParams;
    }
    public function renderable()
    {
        $method = $this->callFunction;
        $a = app($this->callClass)->$method();

        return app($this->callClass)->$method();
    }
}
