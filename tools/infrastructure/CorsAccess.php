<?php
namespace tools\infrastructure;

class CorsAccess extends StatusCode{

    public function startSession():self{
        session_start();
        return $this;
    }

    public function allowCorsOriginAccess():self{
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
        return $this;
    }
    
    public function executeStatus():self{
        //header('Content-type: application/json');
        //http_response_code($this->code());

        $sapi_type = php_sapi_name();
        if (substr($sapi_type, 0, 3) == 'cgi'){
            header('Status: '.$this->code().' Not Found');
        }else{
            header('HTTP/1.1 '.$this->code().' Not Found');
        }
        return $this;
    }
}