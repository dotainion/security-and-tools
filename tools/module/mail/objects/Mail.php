<?php
namespace tools\module\mail\objects;

use tools\infrastructure\Assert;
use tools\infrastructure\Collector;
use tools\infrastructure\Id;
use tools\infrastructure\IId;
use tools\infrastructure\IObjects;

class Mail implements IObjects{
    protected Id $id;
    protected string $subject;
    protected string $body;
    protected Collector $recipients;
    protected Collector $attatchments;

    public function __construct(){
        $this->id = new Id();
    }
        
    public function id():IId{
        return $this->id;
    }

    public function subject():string{
        return $this->subject;
    }

    public function body():string{
        return $this->body;
    }

    public function recipients():Collector{
        return $this->recipients;
    }

    public function attatchments():Collector{
        return $this->attatchments;
    }

    public function setId(string $id):void{
        $this->id->set($id);
    }

    public function setSubject(string $subject):void{
        $this->subject = $subject;
    }

    public function setBody(string $body):void{
        Assert::stringNotEmpty($body, 'Email body required.');
        $this->body = $body;
    }

    public function setRecipients(Collector $recipients):void{
        $this->recipients = $recipients;
    }

    public function setAttatchments(Collector $attatchments):void{
        $this->attatchments = $attatchments;
    }
}

