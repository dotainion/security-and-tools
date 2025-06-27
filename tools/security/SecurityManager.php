<?php
namespace tools\security;

use tools\infrastructure\ApiRequest;
use tools\infrastructure\DateHelper;
use tools\infrastructure\Env;
use tools\infrastructure\exeptions\NotAuthenticatedException;
use tools\infrastructure\ICredential;
use tools\infrastructure\IIdentifier;
use tools\infrastructure\IUser;
use tools\infrastructure\Password;
use tools\infrastructure\Token;

class SecurityManager{

    use PasswordTrait;

    protected Env $env;
    protected Login $login;
    protected Logout $logout;

    public function __construct(){
        $this->env = new Env();
        $this->login = new Login();
        $this->logout = new Logout();
    }

    private function _updateAccessToken(Security $credential):ICredential{
        $token = new Token();
        $this->login->updateToken($credential->user()->id(), $token->new());
        $credential->user()->setToken($token->toString());
        $credential->setToken($token->toString());
        return $credential;
    }

    public function verification(IIdentifier $identifier, Password $password):ICredential{
        $collector = $this->login->login($identifier);
        if(!$collector->first()->hasPassword()){
            throw new NotAuthenticatedException('This account do not have login access.');
        }
        $this->assertSignInPass($collector->first()->password(), $password);
        return $collector->first();
    }

    public function login(IIdentifier $identifier, Password $password):void{
        $credential = $this->verification($identifier, $password);
        $credential = $this->_updateAccessToken($credential);
        $this->startSession($credential);
    }

    public function googleLogin(string $accessToken):void{
        $api = new ApiRequest();
        $api->setUrl('https://www.googleapis.com/oauth2/v1/userinfo?access_token='.$accessToken);
        $api->setHeader($accessToken);
        $api->send();
        if($api->hasError()){
            throw new NotAuthenticatedException('You are not logged in.');
        }
        $collector = $this->login->googleLogin($api->get('id'));
        if(!$collector->hasItem()){
            throw new NotAuthenticatedException('Account not found.');
        }
        $security = $collector->first();
        $credential = $this->_updateAccessToken($security);
        $this->startSession($credential);
    }

    public function hasValidAccessToken():bool{
        $token = new Token();
        $authorizationToken = $this->env->authorizationHeader();
        if(!$token->stringIsValid($authorizationToken)){
            return false;
        }
        $token->set($authorizationToken);
        $collector = $this->login->byToken($token);
        if(!$collector->hasItem()){
            return false;
        }
        $security = $collector->first();
        if(!$security->expire() || (new DateHelper())->new()->expired($security->expire()->toString())){
            return false;
        }
        $this->startSession($security);
        $this->login->updateToken($security->user()->id(), $token);
        return true;
    }

    public function user():IUser{
        $this->assertUserAccess();
        return Session::user();
    }

    public function assertUserAccess():bool{
        if(!$this->hasValidAccessToken() && !Session::hasSession()){
            throw new NotAuthenticatedException('You are not logged in.');
        }
        if(!Session::session() instanceof ICredential || !Session::session()->token()){
            throw new NotAuthenticatedException('You are not logged in.');
        }
        return true;
    }

    public function startSession(ICredential $user){
        $_SESSION[Session::key()] = serialize($user);
    }

    public function logout(){
        $this->logout->logout($this->user()->id());
        unset($_SESSION[Session::key()]);
        session_destroy();
    }

    public function authenticated(Token $token):bool{
        //this is use for service like fetch session
        if(Session::hasSession() && Session::session()->token()->toString() === $token->toString() || $this->hasValidAccessToken()){
            $this->assertUserAccess();
            return true;
        }
        throw new NotAuthenticatedException('You are not authenticated.');
    }
}