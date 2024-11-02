<?php
namespace tools\module\mail\service;

use tools\infrastructure\Assert;
use tools\infrastructure\Id;
use tools\infrastructure\SendMail;
use tools\infrastructure\Service;
use tools\module\mail\factory\AttatchmentFactory;
use tools\module\mail\factory\MailFactory;
use tools\module\mail\factory\RecipientFactory;

class SendMailService extends Service{
    protected SendMail $mail;
    protected MailFactory $factory;
    protected RecipientFactory $recipientsFactory;
    protected AttatchmentFactory $attatchmentFactory;

    public function __construct(){
        parent::__construct();
        $this->mail = new SendMail();
        $this->factory = new MailFactory();
        $this->recipientsFactory = new RecipientFactory();
        $this->attatchmentFactory = new AttatchmentFactory();
    }
    
    public function process($subject, $body, $recipients, $attatchments = null){
        Assert::stringNotEmpty($subject, 'Mail subject is required.');
        Assert::stringNotEmpty($body, 'Mail body is required.');
        Assert::isArray($recipients, 'Recipients must be an array.');

        $mail = $this->factory->mapResult([
            'id' => (new Id())->new()->toString(),
            'subject' => $subject,
            'body' => $body,
        ]);

        foreach($recipients ?? [] as $recip){
            $recipient = $this->recipientsFactory->mapResult([
                'id' => (new Id())->new()->toString(),
                'mailId' => $mail->id()->toString(),
                'userId' => $recip['userId'],
                'recipient' => $recip['recipient'],
            ]);
            $this->recipientsFactory->add($recipient);
        }

        foreach($attatchments ?? [] as $attatch){
            $attatchment = $this->attatchmentFactory->mapResult([
                'id' => (new Id())->new()->toString(),
                'mailId' => $mail->id()->toString(),
                'image' => $attatch['img'],
                'contentId' => $attatch['contentId']
            ]);
            $this->attatchmentFactory->add($attatchment);
        }

        $mail->setRecipients($this->recipientsFactory);
        $mail->setAttatchments($this->attatchmentFactory);
        
        $this->mail->setMail($mail)->send();

        $this->setOutput($mail);
        return $this;
    }
}