<?php
namespace tools\module\login\logic;

use tools\infrastructure\ICredential;
use tools\module\login\repository\CredentialRepository;

class CreateCredential{
    protected CredentialRepository $repo;

    public function __construct(){
        $this->repo = new CredentialRepository();
    }

    public function create(ICredential $credential):void{
        $this->repo->create($credential);
    }
}