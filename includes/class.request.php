<?php
class Request {
    private $ipAddress;
    private $identity;
    private $user;
    private $unixTime;
    private $method;
    private $path;
    private $protocol;
    private $status;
    private $bytes;
    private $referrer;
    private $userAgent;
    private $pagesOnly = true;

    function __construct($request){
        $this->hash      = md5($request['ip_address']);
        $this->ipAddress = $request['ip_address'];
        $this->identity  = $request['identity'];
        $this->user      = $request['user'];
        $this->unixTime  = $request['unix_time'];
        $this->method    = $request['method'];
        $this->path      = $request['path'];
        $this->protocol  = $request['protocol'];
        $this->status    = $request['status'];
        $this->bytes     = $request['bytes'];
        $this->referrer  = $request['referrer'];
        $this->userAgent = $request['user_agent'];
    }

    public function __set($name, $value) {
        $this->$name = $value;
    }

    public function __get($name){
        if(isset($this->$name))
            return $this->$name;
    }
}
