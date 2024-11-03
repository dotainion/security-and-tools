<?php
namespace tools\infrastructure;

use Exception;

class Env{
    protected array $messages = [];

    public static function dir():string{
        $filter = explode('/', self::uri());
        $trimed = array_filter($filter);
        $index = 0;
        foreach($trimed as $value) {  
            $trimed[$index] = $value;
            $index ++;
        } 
        return $trimed[0] ?? null;
    }

    public static function imageDomain():string{
        $dm = explode('.', self::serverName());
        if(!isset($dm[1]) || empty($dm[1]) || !isset($dm[2]) || empty($dm[2])){
            return '';
        }
        return 'https://'.$dm[1].'.'.$dm[2];
    }

    public static function uiDir():string{
        return 'pos';
    }

    public static function serverName():string{
        return $_SERVER['SERVER_NAME'];
    }

    public static function rootDir():string{
        return $_SERVER['DOCUMENT_ROOT'];
    }

    public static function domain():string{
        return $_SERVER['HTTP_HOST'];
    }

    public static function accessPath():?string{
        return $_SERVER['HTTP_ACCESSPATH']; //green-energy-admin/
    }

    public static function isAdminHost():bool{
        return self::accessPath() === '/green-energy-admin/';//todo: need to add admin domain
    }

    public static function ip():string{
        return $_SERVER['REMOTE_ADDR'];
    }

    public static function uri():string{
        return $_SERVER['REQUEST_URI'];
    }

    public static function baseDir(){
        $directory = str_replace(self::rootDir(), '', __DIR__);
        $directory = str_replace('\\', '/', $directory);
        return array_values(array_filter(explode('/', $directory), fn($dir)=>!empty($dir)))[0];
    }

    public static function headers($key=null){
        $headers = getallheaders();
        if($key === null){
            return $headers;
        }
        if(!isset($headers[$key])){
            return null;
        }
        return $headers[$key];
    }

    public static function authorizationHeader():?string{
        return self::headers('Authorization');
    }

    public static function dotEnv():string{
        return self::rootDir() . '/' . self::baseDir() . '/.env';
    }
    
    public static function parseEnvFile() {
        if (!file_exists(self::dotEnv())) {
            throw new Exception("The .env file does not exist.");
        }
    
        $variables = [];
        $lines = file(self::dotEnv(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
        foreach ($lines as $line) {
            if (trim($line) === '') {
                continue;
            }
            list($key, $value) = explode('=', rtrim($line, ";"), 2);
            $variables[trim($key)] = trim($value);
        }
        return $variables;
    }
    
    public static function loadEnv() {
        foreach (self::parseEnvFile() as $key => $value) {
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }

    public static function env(?string $key=null){
        if($key === null){
            return $_ENV;
        }
        if(isset($_ENV[$key])){
            return $_ENV[$key];
        }
        return null;
    }

    public static function server():string{
        return self::env('DB_SERVER');
    }

    public static function username():string{
        return self::env('DB_USERNAME');
    }

    public static function password():string{
        return self::env('DB_PASSWORD');
    }

    public static function database():string{
        return self::env('DB_DATABASE');
    }
}
