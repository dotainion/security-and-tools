<?php
namespace tools\infrastructure;

use InvalidArgumentException;
use tools\module\mail\objects\Attatchment;

class ImageHelper{
    protected string $file;
    protected string $directory;
    protected string $extention;
    protected string $assertMessage = 'Invlid base64 string.';

    public function __construct(){
        $this->directory = __DIR__.'/../images/';
    }

    public function file():string{
        return $this->file;
    }

    public function directory():string{
        return $this->directory;
    }

    public function extention():string{
        return $this->extention;
    }

    public function assertMessage():string{
        return $this->assertMessage;
    }

    public function setAssertionMessage(string $message):void{
        $this->assertMessage = $message;
    }

    public function setUniqueFileName(Attatchment $atta):void{
        $this->file = $this->directory().$atta->contentKey().'.'.$this->extention();
    }

    public function assertValidBase64String(Attatchment $atta):bool{
        if(!str_contains($atta->image(), 'data:image/png;base64')){
            throw new InvalidArgumentException($this->assertMessage());
        }
        return true;
    }

    function initAndSaveBase64TempImage(Attatchment $attatchment):void{
        $this->assertValidBase64String($attatchment);
        $data = explode(',', $attatchment->image());
        $base64Image = $data[1];
        $data2 = explode(';', $data[0]);
        $this->extention = explode('/', $data2[0])[1];

        $this->setUniqueFileName($attatchment);
        $ifp = fopen($this->file(), 'wb'); 
        fwrite($ifp, base64_decode($base64Image));
        fclose($ifp); 
    }
}