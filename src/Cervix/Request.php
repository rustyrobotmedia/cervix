<?php
namespace Cervix;

class Request
{
    private $data = [];
    private $input = [];
    
    public function __construct($data = [])
    {
        $this->data = $data;
        $this->input = array_merge($data['get'], $data['post']);
    }
    
    public function __call($name, $params = [])
    {
        if(array_key_exists($name, $this->data))
        {
            if(empty($params)) return $this->data[$name];
            return isset($this->data[$name][$params[0]]) ? $this->data[$name][$params[0]] : null;
        }
        return null;
    }
}