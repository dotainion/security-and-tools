<?php

namespace tools\infrastructure;

class Password{

    protected ?string $password = null;
    protected int $cost = 10;
    const CONTROL_OPTION = 'AES-128-ECB';
    const CONTROL_KEY = '26dc161e-c590-40ca-a341-2ea107d9468f-35eb45ab-3758-4624-a977-3bb94400ed4c';

    public function __construct(?string $password=null){
        ($password !== null) && $this->set($password);
    }

    public function __toString():string{
        return $this->toString();
    }

    public function hasPassword():bool{
        return $this->password !== null;
    }

    public function set(string $password):self{
        Assert::validPassword($password);
        $this->password = $password;
        return $this;
    }

    public function setControlPassword(string $password):self{
        $this->password = $password;
        return $this;
    }

    public function toString():?string{
        return $this->password;
    }

    public function toControlString():string{
        return openssl_decrypt($this->password, self::CONTROL_OPTION, self::CONTROL_KEY);
    }

    public function toHash():string{
        return password_hash($this->password, PASSWORD_BCRYPT, ['cost' => $this->cost]);
    }

    public function toControlHash():string{
        return openssl_encrypt($this->password, self::CONTROL_OPTION, self::CONTROL_KEY);
    }

    public function setCost(int $cost):void{
        if ($cost < 4 || $cost > 12) {
            throw new \InvalidArgumentException('Cost must be in the range of 4-31.');
        }
        $this->cost = $cost;
    }
}
