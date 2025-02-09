<?php

namespace tools\infrastructure;

use Ramsey\Uuid\Uuid;

class Id implements IId, IObjects{
    const Default = '00000000-0000-0000-0000-000000000000';
    protected $uuid;

    public function __construct(?string $uuid = null){
        ($uuid !== null) && $this->set($uuid);
    }

    final public function __toString():string{
        return $this->toString();
    }

    final public function id():IId{
        return $this;
    }

    final public function fromBytes(string $uuid):self{
        $this->uuid = (string)Uuid::fromBytes($uuid);
        return $this;
    }

    final public function hasId():bool{
        return $this->uuid !== null;
    }

    final public function new():self{
        $this->uuid = Uuid::uuid4();
        return $this;
    }

    final public function set(?string $uuid):self{
        Assert::validUuid($uuid);
        $this->uuid = (string)Uuid::fromString((string)$uuid);
        return $this;
    }

    public function isValid(?string $uuid):bool{
        return Uuid::isValid($this->_replace((string)$uuid));
    }

    public function toString():string{
        return $this->_replace((string)$this->uuid);
    }

    public function toBytes(string $uuid):string{
        Assert::validUuid($uuid);
      return $this->_replace(Uuid::fromString((string)$uuid)->getBytes());
    }

    private function _replace(string $uuid){
        return str_replace('"', '~~~~~', $uuid);
    }
}
