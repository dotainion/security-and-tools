<?php
namespace tools\module\login\service;

use tools\infrastructure\Pin;
use tools\infrastructure\PinPassword;
use tools\infrastructure\Service;
use tools\security\SecurityManager;

class PinLoginService extends Service{
    protected SecurityManager $security;

    public function __construct(){
        parent::__construct(false);
        $this->security = new SecurityManager();
    }
    
    public function process($pin){

        $identifier = new Pin($pin);
        $password = new PinPassword($pin);
        $this->security->pinSignIn($identifier, $password);
        
        return $this->setOutput($this->security->user());
    }
}