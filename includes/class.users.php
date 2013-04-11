<?php
class Users {
    private $users;

    function __construct(){
        $this->users = array();
    }

    function addUser($id){
        return $this->users[$id] = new User($id);
    }

    function getUsers(){
        return $this->users;
    }

    function getUser($id){
        if(array_key_exists($id,$this->users))
            return $this->users[$id];
    }

    function addRequest($request){
        // does this request belong to an existing user?
        if($user = $this->getUser($request->hash))
        {
            $user->addRequest($request);
        }
        else{
            $user = $this->addUser($request->hash);
            $user->addRequest($request);
        }
        //$this->stats->referrer[$request->referrer]++;
    }

    function orderByHits(){
        usort($this->users, function($a, $b)
        {
            if ($a->countRequests() == $b->countRequests())
                return 0;
            return ($a->countRequests() > $b->countRequests()) ? -1 : 1;
        });
    }
}
