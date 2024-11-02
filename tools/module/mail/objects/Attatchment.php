<?php
namespace tools\module\mail\objects;

use tools\infrastructure\Assert;
use tools\infrastructure\Id;
use tools\infrastructure\IId;
use tools\infrastructure\IObjects;

class Attatchment implements IObjects{
    protected Id $id;
    protected Id $mailId;
    protected string $image;
    protected string $contentKey;

    public function __construct(){
        $this->id = new Id();
        $this->mailId = new Id();
    }
        
    public function id():IId{
        return $this->id;
    }

    public function mailId():IId{
        return $this->mailId;
    }

    public function image():string{
        return $this->image;
    }

    public function contentKey():string{
        return $this->contentKey;
    }

    public function setId(string $id):void{
        $this->id->set($id);
    }

    public function setMailId(string $mailId):void{
        Assert::validUuid($mailId, 'Mail not found.');
        $this->mailId->set($mailId);
    }

    public function setImage(string $image):void{
        Assert::stringNotEmpty($image, 'Invalid image.');
        $this->image = $image;
    }

    public function setContentKey(string $contentKey):void{
        Assert::stringNotEmpty($contentKey, 'Invalid content Id.');
        $this->contentKey = $contentKey;
    }
}

