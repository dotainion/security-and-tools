<?php
namespace tools\security;

use tools\infrastructure\Collector;
use tools\infrastructure\DateHelper;
use tools\infrastructure\exeptions\NotAuthenticatedException;
use tools\infrastructure\Id;
use tools\infrastructure\IIdentifier;
use tools\infrastructure\Phone;
use tools\infrastructure\Token;

class Login{
    protected SecurityRepository $repo;

    public function __construct(){
        $this->repo = new SecurityRepository();
    }

    public function login(IIdentifier $identifier):Collector{
        $collector = $this->repo->listSecurity([
            'phoneNumber' => $identifier->toString()
        ]);
        if($collector->isEmpty()){
            $collector = $this->repo->listSecurity([
                'email' => $identifier->toString()
            ]);
            if($collector->hasItem()){
                return $collector;
            }
            $message = 'No account exist for this email.';
            $identifier instanceof Phone && $message = 'No account exist for this phone number.';
            throw new NotAuthenticatedException($message);
        }
        return $collector;
    }

    public function byToken(Token $token):Collector{
        return $this->repo->listSecurity([
            'token' => $token->toString()
        ]);
    }

    public function googleLogin(string $foreignId):Collector{
        $collector = $this->repo->listSecurity([
            'foreignId' => $foreignId
        ]);
        if(!$collector->hasItem()){
            throw new NotAuthenticatedException('This google account is not registered.');
        }
        return $collector;
    }

    public function updateToken(Id $id, Token $token):void{
        $expire = new DateHelper();
        $expire->new()->addMinutes(30);
        $this->repo->updateToken($id, $token, $expire);
    }
}