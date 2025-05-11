<?php
namespace tools\module\login\service;

use InvalidArgumentException;
use tools\infrastructure\Assert;
use tools\infrastructure\Email;
use tools\infrastructure\Password;
use tools\infrastructure\Phone;
use tools\infrastructure\Service;
use tools\security\SecurityManager;

class LoginService extends Service{
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

        if($email){
            Assert::validEmail($email, 'Invalid email');
            $identifier = new Email();
            $identifier->set($email);
        }else{
            $identifier = new Phone();
            $identifier->set($phone);
        }

        $passwordObj = new Password();
        $passwordObj->set($password);

        $this->security->login($identifier, $passwordObj);
        
        return $this->setOutput($this->security->user());
    }
}