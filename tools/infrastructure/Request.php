<?php
namespace tools\infrastructure;

class Request{
    protected array $routes = [];
    protected $request;

    public function __construct(){
        $this->__REQUEST__();
    }

    public function __REQUEST__(){
        $contents = file_get_contents('php://input');
        $this->request = !empty($contents) ? json_decode($contents, true) : null;
        if(empty($this->request) && !empty($_POST)){
            $this->request = $_POST;
        }
    }
    
    public static function isPost(){
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            return true;
        }
        return false;
    }

    public function file(string $attr){
        if(isset($_FILES[$attr])){
            return $_FILES[$attr];
        }
        return null;
    }

    public function get(string $attr){
        if(!isset($this->request[$attr])){
            return null;
        }
        if(is_bool($this->request[$attr])){
            return $this->request[$attr];
        }
        if(in_array($this->request[$attr], ['true', 'false'])){
            return $this->request[$attr] === 'true';
        }
        if(is_string($this->request[$attr])){
            return stripcslashes($this->request[$attr]);
        }
        return $this->request[$attr];
    }

    public function request(){
        return $this->request;
    }

    public function pagination():Pagination{
        return new Pagination($this->get('limit'), $this->get('offset'));
    }
}