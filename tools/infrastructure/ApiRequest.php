<?php
namespace tools\infrastructure;

use Exception;

class ApiRequest
{
    protected $ch;
    protected string $url;
    protected ?string $header=null;
    protected ?array $payload=null;
    protected ?array $response=null;

    public function __construct(bool $sendResponseToBrowswer=false){
        $this->ch = curl_init();
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, !$sendResponseToBrowswer);//true to not send data to browser
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
    }

    public function setUrl(string $url):void{
        $this->url = $url;
    }

    public function setPayload(array $payload):void{
        $this->payload = $payload;
    }

    public function setHeader(string $header):void{
        $this->header = $header;
    }

    public function statusCode():?int{
        return curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
    }

    public function isSuccess():bool{
        return $this->statusCode() === 200;
    }

    public function hasError():bool{
        return curl_errno($this->ch);
    }

    public function response():?array{
        return $this->response;
    }

    public function get(string $key):?string{
        return $this->response[$key] ?? null;
    }

    public function send():void{
        curl_setopt($this->ch, CURLOPT_URL, $this->url);	
        if($this->payload !== null){
            curl_setopt($this->ch, CURLOPT_POSTFIELDS, http_build_query($this->payload));
        }	
        if($this->header !== null){
            curl_setopt($this->ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '. $this->header));
        }
        $this->response = json_decode(curl_exec($this->ch), true);
        curl_close($this->ch);
    }

    public function assertSuccessResponse():bool{
        if($this->statusCode() === 200){
            throw new Exception('Request fail');
        }
        return true;
    }
}

?>

