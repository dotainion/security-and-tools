<?php
namespace tools\module\login\service;

use tools\infrastructure\Assert;
use tools\infrastructure\Id;
use tools\infrastructure\Password;
use tools\infrastructure\Service;
use tools\infrastructure\Token;
use tools\module\login\logic\UpdateCredential;

class UpdateCredentialByTokenService extends Service{
    protected UpdateCredential $credential;

    public function __construct(){
        parent::__construct(false);
        $this->credential = new UpdateCredential();
    }
    
    public function process($id, $password, $refreshToken){
        Assert::validUuid($id, 'No matching user account found.');
        Assert::validPassword($password, 'The password you entered is incorrect.');
        Assert::validToken($refreshToken, 'The provided token is invalid or has expired.');

        $userId = new Id();
        $userId->set($id);
        $passwordObj = new Password();
        $passwordObj->set($password);
        $token = new Token();
        $token->set($refreshToken);

        $this->credential->updateByToken($userId, $passwordObj, $token);
        $this->credential->unsetTokenRefreshToken($userId, $token);

        return $this->success();
    }
}