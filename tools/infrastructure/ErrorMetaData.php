<?php
namespace tools\infrastructure;

class ErrorMetaData{
    protected static ?self $_instance = null;
    protected static ?array $data = null;

    public function __construct(){
        if(self::$_instance === null){
            self::$_instance = new self;
        }
    }

    public static function set(string $key, $value):void{
        is_null(self::$data) && self::$data = [];
        self::$data[$key] = $value;
    }

    public static function get():?array{
        return self::$data;
    }
}