<?php
namespace tools;

use tools\infrastructure\Service;
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

    public function createCredential(string $id, string $password):Service{
        return (new CreateCredentialService())->process($id, $password);
    }

    public function createGoogleCredential(string $id):Service{
        return (new CreateGoogleCredentialService())->process($id);
    }

    public function getSession(string $token):Service{
        return (new FetchSessionService())->process($token);
    }

    public function googleLogin(string $accessToken):Service{
        return (new GoogleLoginService())->process($accessToken);
    }

    public function signIn(string $email, string $phone, string $password):Service{
        return (new LoginService())->process($email, $phone, $password);
    }

    public function signOut():Service{
        return (new LogoutService())->process();
    }

    public function sendEmailRecovery(string $email, string $userId):Service{
        return (new SendRecoverEmailService())->process($email, $userId);
    }

    public function updateCredentialByToken(string $id, string $password, string $refreshToken):Service{
        return (new UpdateCredentialByTokenService())->process($id, $password, $refreshToken);
    }

    public function updateCredential(string $id, string $password, string $currentPassword):Service{
        return (new UpdateCredentialService())->process($id, $password, $currentPassword);
    }
}

