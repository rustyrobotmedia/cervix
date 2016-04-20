<?php
namespace Cervix;

use Workerman\Worker;
use Workerman\Protocols\Http;
use Workerman\Protocols\HttpCache;


class Cervix extends Worker
{
    private $router;
    private $get;
    private $post;
    private $server;
    private $cookie;
    private $files;
    
    
    public function __construct($socket_name, $context_option = [])
    {
        parent::__construct($socket_name, $context_option);
        $this->onMessage = [$this, 'handleRequest'];
        $this->numWorkers();
        $this->name = 'cervix';
        $this->router = new Router;
    }
    
    public function numWorkers($num = 4)
    {
        $this->count = $num;
    }
    
    public function getRouter()
    {
        return $this->router;
    }
    
    public function start()
    {
        Worker::runAll();
    }
    
    public function route($methods, $pattern, $callback)
    {
        return $this->router->map($methods, $pattern, $callback);
    }
    
    public function handleRequest($connection, $data)
    {
        $this->parseIncoming($data);
        $route = $this->router->route($this->server->request_method, $this->server->request_uri);
        $response = $route(new Request($data));
        $connection->send($response ?: '');
    }
    
    private function parseIncoming($data = [])
    {
        $this->get = (object) $this->parseGlobal($data['get']);
        $this->post = (object) $this->parseGlobal($data['post']);
        $this->server = (object) $this->parseGlobal($data['server']);
        $this->files = (object) $this->parseGlobal($data['files']);
    }
    
    private function parseGlobal($global)
    {
        if(!is_array($global)) return $global;
        return array_change_key_case($global, CASE_LOWER);
    }
}