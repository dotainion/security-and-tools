<?php
namespace tools\security;

use tools\infrastructure\ChainError;

class ValidatePassword{
	protected ChainError $chain;
    protected array $specialChars;

    public function __construct(){
        $this->chain = new ChainError();
        $this->specialChars = ['!', '@', '#', '$', '%' , '^', '&', '*', '(', ')'];
    }

    public function validate($password):void{
        if(!preg_match('/[A-Z]/', $password)){
            $this->chain->appendMessage('must contain a capital letter');
        }
        if (!preg_match('~[0-9]+~', $password)) {
            $this->chain->appendMessage('must contain a number (0-9)');
        }
        if (strlen($password) < 7) {
            $this->chain->appendMessage('must be at least 7 digits long');
        }
        if (!preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $password)){
            $this->chain->appendMessage('must contain a special character '.implode(', ', $this->specialChars()));
        }
        if($this->chain->hasError()){
            $this->chain->prependMessage('Password ');
        }
    }

    public function chain():ChainError{
        return $this->chain;
    }

    public function specialChars():array{
        return $this->specialChars;
    }
}

?>