<?php
namespace tools;

use tools\module\login\service\CreateCredentialService;
use tools\module\login\service\CreateGoogleCredentialService;
use tools\module\login\service\FetchSessionService;
use tools\module\login\service\GoogleLoginService;
use tools\module\login\service\LoginService;
use tools\module\login\service\LogoutService;
use tools\module\login\service\SendRecoverEmailService;
use tools\module\login\service\UpdateCredentialByTokenService;
use tools\module\login\service\UpdateCredentialService;

class SecurityTools{
    public function __construct(){
        
    }

    public function createCredential(string $id, string $password):void{
        (new CreateCredentialService())->process($id, $password);
    }

    public function createGoogleCredential(string $id):void{
        (new CreateGoogleCredentialService())->process($id);
    }

    public function getSession(string $token):void{
        (new FetchSessionService())->process($token);
    }

    public function googleLogin(string $accessToken):void{
        (new GoogleLoginService())->process($accessToken);
    }

    public function signIn(string $email, string $phone, string $password):void{
        (new LoginService())->process($email, $phone, $password);
    }

    public function signOut():void{
        (new LogoutService())->process();
    }

    public function sendEmailRecovery(string $email):void{
        (new SendRecoverEmailService())->process($email);
    }

    public function updateCredentialByToken(string $id, string $password, string $refreshToken):void{
        (new UpdateCredentialByTokenService())->process($id, $password, $refreshToken);
    }

    public function updateCredential(string $id, string $password, string $currentPassword):void{
        (new UpdateCredentialService())->process($id, $password, $currentPassword);
    }
}

