<?php
namespace tools\module\login\service;

use InvalidArgumentException;
use tools\infrastructure\Assert;
use tools\infrastructure\DateHelper;
use tools\infrastructure\Email;
use tools\infrastructure\Service;
use tools\infrastructure\Token;
use tools\module\login\factory\CredentialFactory;
use tools\module\login\logic\CreateCredential;
use tools\module\mail\logic\RecoveryTemplate;
use tools\module\mail\service\SendMailService;
use tools\module\user\logic\FetchUser;

class SendRecoverEmailService extends Service{
    protected FetchUser $fetch;
    protected CredentialFactory $factory;
    protected CreateCredential $credential;
    protected RecoveryTemplate $template;

    public function __construct(){
        parent::__construct(false);
        $this->fetch = new FetchUser();
        $this->factory = new CredentialFactory();
        $this->credential = new CreateCredential();
        $this->template = new RecoveryTemplate();
    }
    
    public function process($email){
        Assert::validEmail($email, 'Invalid email.');
        
        $emailObj = new Email();
        $emailObj->set($email);

        $user = $this->fetch->userByEmail($emailObj);
        if(!$user->hasItem()){
            throw new InvalidArgumentException('There is no account under this email: '.$email);
        }

        $credential = $this->factory->mapResult([
            'id' => $user->first()->id()->toString(),
            'expire' => (new DateHelper())->new()->addDays(30)->toString(),
            'refreshToken' => (new Token())->new()->toString()
        ]);

        $this->template->setToken($credential->refreshToken());

        $service = (new SendMailService())->process('Password recovery', $this->template->recovery(), [[
            'userId' => $user->first()->id()->toString(),
            'recipient' => $user->first()->email(),
        ]]);

        $this->credential->create($credential);
        
        $this->mergeOutput($service);
        return $this;
    }
}