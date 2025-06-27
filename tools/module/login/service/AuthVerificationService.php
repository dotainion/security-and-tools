<?php
namespace tools\module\login\service;

use InvalidArgumentException;
use tools\infrastructure\Assert;
use tools\infrastructure\Email;
use tools\infrastructure\Password;
use tools\infrastructure\Phone;
use tools\infrastructure\Service;
use tools\security\SecurityManager;

class AuthVerificationService extends Service{
    protected SecurityManager $security;

    public function __construct(){
        parent::__construct(false);
        $this->security = new SecurityManager();
    }
    
    public function process($email, $phone, $password){
        Assert::validPassword($password, 'The password you entered is incorrect.', false);
        
        if(!$email && !$phone){
            throw new InvalidArgumentException('Invalid email or phone number.');
        }

        $identifier = $email ? new Email($email) : new Phone($phone);
        $password = new Password($password);
        $security = $this->security->verification($identifier, $password);
        
        return $this->setOutput($security->user());
    }
}