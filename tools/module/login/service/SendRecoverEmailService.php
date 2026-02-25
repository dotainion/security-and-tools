<?php
namespace tools\module\login\service;

use tools\infrastructure\Assert;
use tools\infrastructure\DateHelper;
use tools\infrastructure\Service;
use tools\infrastructure\Token;
use tools\module\login\factory\CredentialFactory;
use tools\module\login\logic\UpdateCredential;
use tools\module\mail\logic\RecoveryTemplate;
use tools\module\mail\service\SendMailService;

class SendRecoverEmailService extends Service{
    protected CredentialFactory $factory;
    protected UpdateCredential $credential;
    protected RecoveryTemplate $template;

    public function __construct(){
        parent::__construct(false);
        $this->factory = new CredentialFactory();
        $this->credential = new UpdateCredential();
        $this->template = new RecoveryTemplate();
    }
    
    public function process($email, $userId){
        Assert::validEmail($email, 'Invalid email.');
        Assert::validUuid($userId, 'User not found.');

        $credential = $this->factory->mapResult([
            'id' => $userId,
            'expire' => (new DateHelper())->new()->addDays(30)->toString(),
            'refreshToken' => (new Token())->new()->toString()
        ]);

        $this->template->setToken($credential->refreshToken());

        $service = (new SendMailService())->process('Password recovery', $this->template->recovery(), [[
            'userId' => $userId,
            'recipient' => $email,
        ]]);

        $this->credential->setRefreshToken($credential);
        
        return $this->mergeOutput($service);
    }
}