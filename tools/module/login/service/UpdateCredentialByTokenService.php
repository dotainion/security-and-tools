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
        Assert::validUuid($id, 'User not found.');
        Assert::validPassword($password, 'Incorrect password.');
        Assert::validToken($refreshToken, 'Invalid token.');

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