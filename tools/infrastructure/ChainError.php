<?php
namespace tools\infrastructure;

class ChainError{
    protected array $messages = [];

    public function messages():string{
        return implode(', ', $this->messages);
    }

    public function prependMessage(string $message):void{
        $this->messages = $this->messages + [$message];
    }

    public function appendMessage(string $message):void{
        $this->messages[] = $message;
    }

    public function hasError():bool{
        return !empty($this->messages);
    }
}