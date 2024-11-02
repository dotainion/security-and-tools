<?php
namespace tools\module\login\logic;

use tools\infrastructure\Id;
use tools\infrastructure\Password;
use tools\infrastructure\Token;
use tools\module\login\repository\CredentialRepository;

class UpdateCredential{
    protected CredentialRepository $repo;

    public function __construct(){
        $this->repo = new CredentialRepository();
    }

    public function updatecredential(Id $id, Password $currentPassword, Password $password):void{
        $collector = $this->repo->listHasCredential([
            'id' => $id,
            'password' => $currentPassword->toHash()
        ]);
        $collector->assertHasItem('Invalid password.');
        $this->repo->editPassword($id, $currentPassword, $password);
    }

    public function updateByToken(Id $id, Password $password, Token $token):void{
        $this->repo->updatePasswordByRefreshToken($id, $password, $token);
    }

    public function unsetTokenRefreshToken(Id $id, Token $token):void{
        $this->repo->unsetTokenRefreshToken($id, $token);
    }
}