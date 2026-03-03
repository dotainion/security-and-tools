<?php
namespace tools\infrastructure;

class StatusCode{
    protected int $code;

    protected int $OK = 200;
    protected int $CREATED = 201;
    protected int $ACCEPTED = 202;
    protected int $NO_RESULTS = 204;
    protected int $BAD_REQUEST = 400;
    protected int $UNAUTHORIZED = 401;
    protected int $PAYMENT_REQUIRED = 402;
    protected int $FORBIDDEN = 403;
    protected int $NOT_FOUND = 404;
    protected int $METHOD_NOT_ALLOWED = 405;
    protected int $INTERNAL_SERVER_ERROR = 500;
    protected int $SERVICE_UNAVAILABLE = 503;

    public function code():int{
        return $this->code;
    }

    public function setCode(int $code):void{
        $this->code = $code;
    }
}