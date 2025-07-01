<?php

namespace tools\infrastructure;

class Pin implements IIdentifier{
    protected ?string $pin = null;

    public function __construct(?string $pin=null){
        ($pin !== null) && $this->set($pin);
    }

    final public function __toString():string{
        return $this->toString();
    }

    final public function hasPin():bool{
        return $this->pin !== null;
    }

    final public function set(string $pin):self{
        Assert::pin($pin);
        $this->pin = $pin;
        return $this;
    }

    public function toString():string{
        return $this->pin;
    }
}
