<?php
namespace tools\module\login\service;

use tools\infrastructure\Assert;
use tools\infrastructure\Id;
use tools\infrastructure\Service;
use tools\infrastructure\Token;
use tools\module\login\factory\CredentialFactory;
use tools\module\login\logic\CreateCredential;

class CreateGoogleCredentialService extends Service{
    protected CredentialFactory $factory;
    protected CreateCredential $credential;

    public function __construct(){
        parent::__construct(false);
        $this->factory = new CredentialFactory();
        $this->credential = new CreateCredential();
    }
    
    public function process($id){
        Assert::validUuid($id, 'No matching user account found.');
        
        $idObj = new Id();
        $idObj->set($id);

        $credential = $this->factory->mapResult([
            'id' => $idObj->toString(),
            'hide' => false,
            'token' => (new Token())->new()->toString(),
            'password' => null,
        ]);

        $this->credential->create($credential);
        
        return $this->success();
    }
}