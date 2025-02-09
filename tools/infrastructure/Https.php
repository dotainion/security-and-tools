<?php
namespace tools\infrastructure;

use tools\infrastructure\exeptions\TokenExpiredException;

class Https{
    protected array $routes = [];
    protected static ?string $baseDirectory = null;

    public function __construct(string $baseDirectory){
        self::$baseDirectory = '/'.$baseDirectory;
    }

    function __INIT__(){
        $uri = $_SERVER['REQUEST_URI'];
        $payload = null;
        foreach($this->routes as $path => $callback){
            if($path !== $uri) continue;
            $payload = $callback($this) ?? 'NO PAY LOAD';
            break;
        }
        if($payload === null){
            throw new TokenExpiredException('Url not found.');
        }
        if($payload instanceof IAction){
            $payload->execute()->sendResponse();
        }
    }

    public function directory(string $path=''){
        return self::$baseDirectory.$path;
    }

    public static function baseDirectory(){
        return self::$baseDirectory;
    }

    function route(string $path, callable $callback){
        $path = $this->directory($path);
        $this->routes[$path] = $callback;
    }
}