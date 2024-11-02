<?php
namespace tools\module\mail\factory;

use tools\infrastructure\Collector;
use tools\infrastructure\Factory;
use tools\module\mail\objects\Mail;

class MailFactory extends Collector{
    use Factory;

    public function __construct(){
    }
    
    public function mapResult($record):Mail{
        $bank = new Mail();
        $bank->setId($this->uuid($record['id']));
        $bank->setSubject($record['subject']);
        $bank->setBody($record['body']);
        return $bank;
    }
}