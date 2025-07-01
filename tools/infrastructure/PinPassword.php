<?php

namespace tools\infrastructure;

class PinPassword extends Password{

    public function set(string $password):self{
        Assert::pin($password);
        $this->password = $password;
        return $this;
    }
}
