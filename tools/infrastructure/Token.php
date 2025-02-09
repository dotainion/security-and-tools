<?php

namespace tools\infrastructure;

class Token{
    protected ?string $token = null;

    public function __construct(?string $token=null){
        $token !== null && $this->set($token);
    }

    public function __toString():string{
        return $this->toString();
    }

    public function hasToken():bool{
        return $this->token !== null;
    }

    public function stringIsValid($tokenString):bool{
        return (new Id())->isValid($tokenString);
    }

    public function new():self{
        $this->token = (new Id())->new()->toString();
        return $this;
    }

    public function set(string $token):self{
        Assert::validUuid($token);
        $this->token = $token;
        return $this;
    }

    public function toString():string{
        return $this->token;
    }
}
