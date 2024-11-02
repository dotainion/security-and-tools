<?php
namespace tools\infrastructure;

use Exception;
use InvalidArgumentException;
use permission\infrastructure\PermissionException;
use tools\infrastructure\exeptions\InvalidRequirementException;
use tools\infrastructure\exeptions\NoResultsException;
use tools\infrastructure\exeptions\NotAuthenticatedException;
use tools\infrastructure\exeptions\TokenExpiredException;
use Throwable;

class StatusCode{
    protected int $code;
    protected string $message;

    protected int $OK = 200;
    protected int $CREATED = 201;
    protected int $ACCEPTED = 202;
    protected int $NO_RESULTS = 204;
    protected int $BAD_REQUEST = 400;
    protected int $UNAUTHORIZED = 401;
    protected int $PAYMENT_REQUIRED = 402;
    protected int $FORBIDDEN = 403;
    protected int $NOT_FOUND = 404;
    protected int $INTERNAL_SERVER_ERROR = 500;
    protected int $SERVICE_UNAVAILABLE = 503;

    public function __construct(){
        session_start();
        $this->allowCorsOriginAccess();
    }

    public function allowCorsOriginAccess():void{
        // Allow from any origin
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');    // cache for 1 day
        }

        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])){
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         
            }
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])){
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
            }
            exit(0);
        }
    }
    
    public function executeStatus(){
        //header('Content-type: application/json');
        //http_response_code($this->code());

        $sapi_type = php_sapi_name();
        if (substr($sapi_type, 0, 3) == 'cgi'){
            header('Status: '.$this->code().' Not Found');
        }else{
            header('HTTP/1.1 '.$this->code().' Not Found');
        }
    }

    public function code():int{
        return $this->code;
    }

    public function setCode(int $code):void{
        $this->code = $code;
    }

    public function message():string{
        return $this->message;
    }

    public function setMessage(string $message):void{
        $this->message = $message;
    }

    public function buildResponse():string{
        $response = [
            'error' => [
                'message' => $this->message(), 
                'meta' => ErrorMetaData::get()
            ]
        ];
        return json_encode($response);
    }

    public function handleExeption(callable $callBack):void{
        try{
            $this->setCode($this->OK);
            $callBack();
        }catch(NoResultsException $ex){
            $this->setCode($this->NOT_FOUND/*$this->NO_RESULTS*/);
            $this->setMessage($ex->getMessage());
        }catch (NotAuthenticatedException $ex){
            $this->setCode($this->UNAUTHORIZED);
            $this->setMessage($ex->getMessage());
        }catch (TokenExpiredException $ex){
            $this->setCode($this->SERVICE_UNAVAILABLE);
            $this->setMessage($ex->getMessage());
        }catch (InvalidArgumentException $ex){
            $this->setCode($this->NOT_FOUND);
            $this->setMessage($ex->getMessage());
        }catch (InvalidRequirementException $ex){
            $this->setCode($this->NOT_FOUND);
            $this->setMessage($ex->getMessage());
        }catch (TokenExpiredException $ex){
            $this->setCode($this->FORBIDDEN);
            $this->setMessage($ex->getMessage());
        }catch (PermissionException $ex){
            $this->setCode($this->FORBIDDEN);
            $this->setMessage($ex->getMessage());
        }catch (Exception $ex){
            $this->setCode($this->INTERNAL_SERVER_ERROR);
            $this->setMessage($ex->getMessage() . PHP_EOL . $ex->getTraceAsString());
        }catch(Throwable $ex){
            $this->setCode($this->INTERNAL_SERVER_ERROR);
            $this->setMessage($ex->getMessage() . PHP_EOL . $ex->getTraceAsString());
        }finally{
            if($this->code() !== $this->OK){
                $this->executeStatus();
                print_r($this->buildResponse());
            }
        }
    }
}