<?php
namespace Cervix;


class Route
{
    private $info = [];
    private $params = [];
    
    public function __construct($info)
    {
        $this->info = $info;
    }
    
    public function setParams($params)
    {
        $this->params = $params;
    }
    
    public function isCallable()
    {
        return 'call' == $this->info['type'];
    }
    
    public function isInclude()
    {
        return 'include' == $this->info['include'];
    }
    
    public function isInvokable()
    {
        return 'invoke' == $this->info['invoke'];
    }
    
    public function getCallback()
    {
        return $this->info['callback'];
    }
    
    public function __invoke($input = [])
    {
        if($this->isCallable())
        {
            return call_user_func_array($this->getCallback(), [$input, $this->params]);
        }
        elseif($this->isInclude())
        {
            ob_start();
            require $this->getCallback();
            return ob_get_clean();
        }
        elseif($this->isInvokable())
        {
            $callable = $this->getCallable();
            return $callable($input, $this->params);
        }
        
        return null;
    }
}