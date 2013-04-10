<?php
class User {
    private $id;
    private $request;
    private $requests;
    public static $instance = NULL;

    function __construct($id){
        $this->id = $id;
    }

    function addRequest($request){
        $this->requests[$request->unixTime] = $request;
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

