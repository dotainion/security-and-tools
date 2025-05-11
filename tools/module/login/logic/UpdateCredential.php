<?php
namespace tools\module\login\logic;

use tools\infrastructure\Id;
use tools\infrastructure\Password;
use tools\infrastructure\Token;
use tools\module\login\repository\CredentialRepository;
use tools\security\PasswordTrait;

class UpdateCredential{
    
    use PasswordTrait;

    protected CredentialRepository $repo;

    public function __construct(){
        $this->repo = new CredentialRepository();
    }

    public function updatecredential(Id $id, Password $currentPassword, Password $password):void{
        $collector = $this->repo->listHasCredential(['id' => $id]);
        $collector->assertHasItem('No matching user record was found.');
        $security = $collector->first();
        $this->assertSignInPass($security->password(), $currentPassword->toString());
        $this->repo->editPassword($id, $password);
    }

    public function updateByToken(Id $id, Password $password, Token $token):void{
        $this->repo->updatePasswordByRefreshToken($id, $password, $token);
    }

    public function unsetTokenRefreshToken(Id $id, Token $token):void{
        $this->repo->unsetTokenRefreshToken($id, $token);
    }
}