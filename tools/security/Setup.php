<?php
namespace tools\security;

use Exception;
use tools\infrastructure\IFactory;

class Setup{
    protected static ?IFactory $factory = null;
    protected static string $userTableName = 'user';
    protected static array $repoAfterTableSetObsovers = [];
    protected static bool $jointSecurityTableWithPermission = true;
    protected static array $repoExecuteObsover = [];

    public static function jointSecurityTableWithPermission():bool{
        return self::$jointSecurityTableWithPermission;
    }

    public static function jointSecurityTableWithPermissionOff():void{
        self::$jointSecurityTableWithPermission = false;
    }

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

    public static function repoAfterTableSetObsover(callable $callback):void{
        self::$repoAfterTableSetObsovers[] = $callback;
    }

    public static function fireRepoAfterTableSetObsover($repo):void{
        foreach(self::$repoAfterTableSetObsovers as $callback){
            $callback($repo);
        }
    }

    public static function repoExecuteObsover(callable $callback):void{
        self::$repoExecuteObsover[] = $callback;
    }

    public static function fireRepoExecuteObsover($repo):void{
        foreach(self::$repoExecuteObsover as $callback){
            $callback($repo);
        }
    }

    public static function factory():IFactory{
        if(self::$factory === null){
            throw new Exception('Factory was not set in security tools.');
        }
        return self::$factory;
    }
}