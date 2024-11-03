<?php
namespace tools\module\login\service;

use tools\infrastructure\Service;
use tools\security\SecurityManager;

class GoogleLoginService extends Service{
    protected SecurityManager $security;

    public function __construct(){
        parent::__construct(false);
        $this->security = new SecurityManager();
    }
    
    public function process($accessToken){
        
        $this->security->googleLogin($accessToken);
        
        return $this->setOutput($this->security->user());
    }
}