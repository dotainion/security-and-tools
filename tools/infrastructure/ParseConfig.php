<?php
namespace tools\infrastructure;

use Exception;

class ParseConfig{

    public static function path(){
        return __DIR__.'/../secrets/stripe.secrets.txt';
    }
    
    public static function get(string $key):?string{
        return self::list()[$key] ?? null;
    }
    
    public static function secretKey():?string{
        return self::get('secretKey');
    }
    
    public static function publishableKey():?string{
        return self::get('publishableKey');
    }
    
    public static function list(){
        $delimiter = '::';
        $result = [];
        if(!file_exists(self::path())){
            throw new Exception('stripe stripe.secrets.txt has issues.');
        }

        $content = fopen(self::path(), 'r');
        while(!feof($content)) { 
            $lines = explode($delimiter, fgets($content));
            if(!trim($lines[0]) && !trim($lines[1])){
                continue;
            }
            $result[trim($lines[0])] = trim($lines[1]);
        } 
        return $result;
      }
}