<?php
namespace tools\module\login\service;

use tools\infrastructure\Assert;
use tools\infrastructure\Id;
use tools\infrastructure\PinPassword;
use tools\infrastructure\Service;
use tools\infrastructure\Token;
use tools\module\login\factory\CredentialFactory;
use tools\module\login\logic\CreateCredential;

class CreatePinCredentialService extends Service{
    protected CredentialFactory $factory;
    protected CreateCredential $credential;

    public function __construct(){
        parent::__construct(false);
        $this->factory = new CredentialFactory();
        $this->credential = new CreateCredential();
    }
    
    public function process($id, $pin){
        Assert::validUuid($id, 'No matching user account found.');
        Assert::pin($pin, '.The password you entered is incorrect.');
        
        $id = new Id($id);
        $password = new PinPassword($pin);

        $credential = $this->factory->mapResult([
            'id' => $id->toString(),
            'hide' => false,
            'token' => (new Token())->new()->toString(),
            'password' => $password->toString(),
        ]);

        $this->credential->create($credential);
        
        return $this->success();
    }
}