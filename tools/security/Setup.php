<?php
namespace tools\security;

use Exception;
use tools\infrastructure\Id;
use tools\infrastructure\IFactory;

class Setup{
    protected static ?IFactory $factory = null;
    protected static string $userTableName = 'user';
    protected static array $repoAfterTableSetObsovers = [];
    protected static bool $jointSecurityTableWithPermission = true;
    protected static array $repoExecuteObsover = [];
    protected static array $fireRepoSetObsover = [];

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

    public static function repoAfterTableSetObsover(callable $callback):string{
        $uniqueIdentifier = (new Id())->new()->toString();
        self::$repoAfterTableSetObsovers[] = ['id' => $uniqueIdentifier,'fx' => $callback];
        return $uniqueIdentifier;
    }

    public static function fireRepoAfterTableSetObsover($repo):void{
        foreach(self::$repoAfterTableSetObsovers as $opt){
            $opt['fx']($repo);
        }
    }

    public static function repoExecuteObsover(callable $callback):string{
        $uniqueIdentifier = (new Id())->new()->toString();
        self::$repoExecuteObsover[] = ['id' => $uniqueIdentifier,'fx' => $callback];
        return $uniqueIdentifier;
    }

    public static function fireRepoExecuteObsover($repo):void{
        foreach(self::$repoExecuteObsover as $opt){
            $opt['fx']($repo);
        }
    }

    public static function repoSetObsover($cmd, callable $callback):string{
        $uniqueIdentifier = (new Id())->new()->toString();
        self::$fireRepoSetObsover[] = ['id' => $uniqueIdentifier,'fx' => $callback, 'cmd' => $cmd];
        return $uniqueIdentifier;
    }

    public static function fireRepoSetObsover(string $cmd, $repo):void{
        foreach(array_values(self::$fireRepoSetObsover) as $opt){
            ($cmd === $opt['cmd']) && $opt['fx']($repo);
        }
    }

    public static function unsubscribeObsover(string $uniqueIdentifier):void{
        foreach(self::$repoAfterTableSetObsovers as $i => $opt){
            if($uniqueIdentifier === $opt['id']){
                unset(self::$repoAfterTableSetObsovers[$i]);
            }
        }
        foreach(self::$repoExecuteObsover as $i => $opt){
            if($uniqueIdentifier === $opt['id']){
                unset(self::$repoExecuteObsover[$i]);
            }
        }
        foreach(self::$fireRepoSetObsover as $i => $opt){
            if($uniqueIdentifier === $opt['id']){
                unset(self::$fireRepoSetObsover[$i]);
            }
        }
    }

    public static function factory():IFactory{
        if(self::$factory === null){
            throw new Exception('Factory was not set in security tools.');
        }
        return self::$factory;
    }
}