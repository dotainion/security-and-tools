<?php
namespace tools\security;

use tools\infrastructure\Collector;
use tools\infrastructure\Factory;
use tools\infrastructure\ICredential;

class SecurityFactory extends Collector{
    use Factory;

    public function mapResult($record):ICredential{
        $security = new Security();
        $security->setId($this->uuid($record['id']));
        $security->setExpire($record['expire']);
        if(isset($record['password'])){
            $security->setPassword($record['password']);
        }
        if(isset($record['token'])){
            $security->setToken($record['token']);
        }
        if(isset($record['refreshToken'])){
            $security->setRefreshToken($record['refreshToken']);
        }
        if(isset($record['email'])){
            $security->setUser(Setup::factory()->mapResult($record));
        }
        return $security;
    }
}