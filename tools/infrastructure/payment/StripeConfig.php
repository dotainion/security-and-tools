<?php
namespace tools\infrastructure\payment;

use tools\infrastructure\Id;
use tools\infrastructure\IId;
use tools\infrastructure\IObjects;
use tools\infrastructure\ParseConfig;

class StripeConfig implements IObjects{
    protected Id $id;
    protected ParseConfig $config;

    public function __construct(){
        $this->id = (new Id())->new();
        $this->config = new ParseConfig();
    }

    public function id():IId{
        return $this->id;
    }
    
    public function secretKey():string{
        return $this->config->secretKey();
    }
    
    public function publishableKey():string{
        return $this->config->publishableKey();
    }
}