<?php
namespace tools\module\login\service;

use tools\infrastructure\Assert;
use tools\infrastructure\exeptions\NotAuthenticatedException;
use tools\infrastructure\Service;
use tools\infrastructure\Token;

class FetchSessionService extends Service{
    public function __construct(){
        parent::__construct(false);
    }
    
    public function process($token){
        Assert::validToken($token, 'Invalid token');

        if($this->security()->authenticated(new Token($token))){
            return $this->setOutput($this->security()->user());
        }
        
        throw new NotAuthenticatedException('Your are not authenticted.');
    }
}