<?php
namespace tools\module\login\service;

use tools\infrastructure\Assert;
use tools\infrastructure\Id;
use tools\infrastructure\Password;
use tools\infrastructure\Service;
use tools\infrastructure\Token;
use tools\module\login\factory\CredentialFactory;
use tools\module\login\logic\CreateCredential;

class CreateCredentialService extends Service{
    protected CredentialFactory $factory;
    protected CreateCredential $credential;

    public function __construct(){
        parent::__construct(false);
        $this->factory = new CredentialFactory();
        $this->credential = new CreateCredential();
    }
    
    public function process($id, $password){
        Assert::validUuid($id, 'User not found.');
        Assert::validPassword($password, 'Invalid password.');
        
        $idObj = new Id();
        $idObj->set($id);
        $passwordObj = new Password();
        $passwordObj->set($password);

        $credential = $this->factory->mapResult([
            'id' => $idObj->toString(),
            'hide' => false,
            'token' => (new Token())->new()->toString(),
            'password' => $passwordObj->toString(),
        ]);

        $this->credential->create($credential);
        
        return $this;
    }
}