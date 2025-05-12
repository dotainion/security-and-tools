<?php
namespace tools\module\login\service;

use tools\infrastructure\Assert;
use tools\infrastructure\IObjects;
use tools\infrastructure\Service;
use tools\module\login\logic\SendMessage;

class SendMessageService extends Service{
    protected SendMessage $message;

    public function __construct(){
        parent::__construct(false);
        $this->message = new SendMessage();
    }
    
    public function process(string $channel, string $event, IObjects $message){
        Assert::stringNotEmpty($channel, 'Message channel is required.');
        Assert::stringNotEmpty($event, 'Message event is required.');
        Assert::stringNotEmpty($message, 'A Message required.');

        $this->message->send($channel, $event, $message);
        
        return $this;
    }
}