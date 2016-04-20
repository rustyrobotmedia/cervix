<?php
namespace Cervix;

class Router
{
    private $routes = [];
    
    public function map($methods, $pattern, $callback)
    {
        $type = 'call';
        $pattern = "/^" . preg_quote($pattern, '/') . ".*/";
        
        if(!is_callable($callback))
        {
            if(class_exists($callback)) {
                $type = 'invoke';
            }
            elseif(file_exists($callback) && is_readable($callback))
            {
                $type = 'include';
            }
        }
        
        foreach((array) $methods as $method)
        {
            $callback = new Route([
                'callback' => $callback,
                'type' => $type,
            ]);
            
            $this->routes[strtoupper($method)][$pattern] = $callback;
        }
    }
    
    public function route($method, $uri)
    {
        $method = strtoupper($method);
        
        if(!array_key_exists($method, $this->routes) || empty($this->routes[$method])) {
            return null;
        }
        
        foreach($this->routes[$method] as $pattern => $callback)
        {
            if(preg_match($pattern, $uri, $params) === 1)
            {
                array_shift($params);
                $callback->setParams(array_values($params));
                return $callback;
            }
        }
        
        return null;
    }
}