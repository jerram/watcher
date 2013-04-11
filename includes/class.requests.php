<?php
class Requests {
    private $id;
    private $requests;
    public static $instance = NULL;

    function __construct(){
    }

    function addRequest($request){
        $this->request[$request->unixTime] = $request;
    }

    function countRequests(){
        return count($this->requests);
    }

    function getRequests(){
        foreach($this->requests as $request){
            $path[] = $request->path;
        }
        return $path;
    }

    function get($key){
        if(!isset($this->$key)){
            $this->request = end($this->requests);
            return $this->request->$key;
        }
    }
}

