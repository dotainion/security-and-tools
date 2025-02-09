<?php
namespace tools\module\mail\logic;

class RecoveryTemplate{
    protected $refreshToken;
    protected $domainName;
    protected $route;
    protected $dir;

    public function __construct(){
        $this->domainName = $_SERVER['SERVER_NAME'];
        $this->route = '/update/credential/by/token/';
        $this->dir = '/gdbuild';
    }

    private function href():string{
        return $this->domainName.$this->dir.'/#'.$this->route.$this->refreshToken;
    }

    public function setToken(string $refreshToken):void{
        $this->refreshToken = $refreshToken;
    }

    public function recovery():string{
        return '
            <h2><b>Pay Roll Application</b></h2>
            <a href="'.$this->href().'">Click here to recover your account.</a>
        ';
    }

    public function assignCredential():string{
        return '
            <h2><b>Pay Roll Application</b></h2>
            <a href="'.$this->href().'">Click here to accept access to this platform and create a password.</a>
        ';
    }
}

