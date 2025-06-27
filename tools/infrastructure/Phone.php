<?php

namespace tools\infrastructure;

class Phone implements IIdentifier{
    protected ?string $phone = null;

    public function __construct(?string $phone=null){
        ($phone !== null) && $this->set($phone);
    }

    final public function __toString():string{
        return $this->toString();
    }

    final public function hasPhone():bool{
        return $this->phone !== null;
    }

    final public function set(string $phone):self{
        Assert::stringNotEmpty($phone, 'Invalid phone');
        $this->phone = $phone;
        return $this;
    }

    public function toString():string{
        return $this->phone;
    }
}
