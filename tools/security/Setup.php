<?php
namespace tools\security;

use Exception;
use tools\infrastructure\IFactory;

class Setup{
    protected static ?IFactory $factory = null;
    protected static string $userTableName = 'user';

    public static function setRequiredFactory(IFactory $factory):void{
        if(self::$factory === null){
            self::$factory = $factory;
        }
    }

    public static function setUserTableName(string $userTableName):void{
        if(self::$userTableName === null){
            self::$userTableName = $userTableName;
        }
    }

    public static function userTableName():string{
        return self::$userTableName;
    }

    public static function factory():IFactory{
        if(self::$factory === null){
            throw new Exception('Factory was not set in security tools.');
        }
        return self::$factory;
    }
}