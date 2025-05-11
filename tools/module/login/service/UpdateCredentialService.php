<?php
namespace tools\module\login\service;

use tools\infrastructure\Assert;
use tools\infrastructure\Id;
use tools\infrastructure\Password;
use tools\infrastructure\Service;
use tools\module\login\logic\UpdateCredential;

class UpdateCredentialService extends Service{
    protected UpdateCredential $creds;

    public function __construct(){
        parent::__construct(false);
        $this->creds = new UpdateCredential();
    }
    
    public function process($id, $password, $currentPassword){
        Assert::validUuid($id, 'No matching user account found.');
        Assert::validPassword($password, 'The password you entered is incorrect.');
        Assert::validPassword($currentPassword, 'The current password you entered is incorrect.');

        $userId = new Id();
        $userId->set($id);
        $currentPasswordObj = new Password();
        $currentPasswordObj->set($currentPassword);
        $passwordObj = new Password();
        $passwordObj->set($password);

        $this->creds->updatecredential($userId, $currentPasswordObj, $passwordObj);
        
        return $this->success();
    }
}