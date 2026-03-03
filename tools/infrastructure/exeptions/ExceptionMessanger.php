<?php
namespace tools\infrastructure\exeptions;

use Throwable;
use Exception;
use InvalidArgumentException;
use permission\infrastructure\PermissionException;
use tools\infrastructure\CorsAccess;
use tools\infrastructure\ErrorMetaData;
use tools\infrastructure\exeptions\InvalidRequirementException;
use tools\infrastructure\exeptions\NoResultsException;
use tools\infrastructure\exeptions\NotAuthenticatedException;
use tools\infrastructure\exeptions\TokenExpiredException;

class ExceptionMessanger extends CorsAccess{
    protected string $message;
    protected Throwable $exception;

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

    public function sendResponse():self{
        $this->executeStatus();
        print_r($this->buildResponse());
        return $this;
    }

    public function setExceptionRequirements(Throwable $exception, int $code):self{
        $this->exception = $exception;
        if($this->INTERNAL_SERVER_ERROR === $code){
            $errorTrace = PHP_EOL . $this->exception->getTraceAsString();
            ErrorMetaData::set('ERROR_TRACE', $errorTrace);
        }
        $this->setCode($code);
        $this->setMessage($this->exception->getMessage());
        return $this;
    }

    public function handleExeption(callable $callBack):void{
        try{
            $this->setCode($this->OK);
            $callBack();
        }catch(NoResultsException $ex){
            $this->setExceptionRequirements($ex, $this->NOT_FOUND);
        }catch (NotAuthenticatedException $ex){
            $this->setExceptionRequirements($ex, $this->UNAUTHORIZED);
        }catch (TokenExpiredException $ex){
            $this->setExceptionRequirements($ex, $this->SERVICE_UNAVAILABLE);
        }catch (InvalidArgumentException $ex){
            $this->setExceptionRequirements($ex, $this->NOT_FOUND);
        }catch (InvalidRequirementException $ex){
            $this->setExceptionRequirements($ex, $this->NOT_FOUND);
        }catch (TokenExpiredException $ex){
            $this->setExceptionRequirements($ex, $this->FORBIDDEN);
        }catch (PermissionException $ex){
            $this->setExceptionRequirements($ex, $this->METHOD_NOT_ALLOWED);
        }catch (Exception $ex){
            $this->setExceptionRequirements($ex, $this->INTERNAL_SERVER_ERROR);
        }catch(Throwable $ex){
            $this->setExceptionRequirements($ex, $this->INTERNAL_SERVER_ERROR);
        }finally{
            if($this->code() !== $this->OK){
                $this->sendResponse();
            }
        }
    }
}