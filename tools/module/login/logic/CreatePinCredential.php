<?php
namespace tools\module\login\logic;

use tools\infrastructure\exeptions\InvalidRequirementException;
use tools\infrastructure\ICredential;
use tools\module\login\repository\CredentialRepository;

class CreatePinCredential{
    protected CredentialRepository $repo;

    public function __construct(){
        $this->repo = new CredentialRepository();
    }

    public function create(ICredential $credential):void{
        $collector = $this->repo->listHasCredential([
            'pin' => $credential->pin()
        ]);
        if($collector->hasItem()){
            throw new InvalidRequirementException('The PIN you entered is already in use. Please try a different one.');
        }
        $this->repo->create($credential);
    }
}