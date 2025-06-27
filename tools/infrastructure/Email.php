<?php

namespace tools\infrastructure;

class Email implements IIdentifier{
    protected ?string $email = null;

    public function __construct(?string $email=null){
        ($email !== null) && $this->set($email);
    }

    final public function __toString():string{
        return $this->toString();
    }

    final public function hasEmail():bool{
        return $this->email !== null;
    }

    final public function set(string $email):self{
        Assert::validEmail($email, 'Invalid email');
        $this->email = $email;
        return $this;
    }

    public function toString():string{
        return $this->email;
    }
}
