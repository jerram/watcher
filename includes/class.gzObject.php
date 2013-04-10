<?php
class gzObject {
    function __construct(){}

    function gzThis($object){
        $this->gzJson = gzencode(json_encode($object));
    }

    function getSize(){
        return strlen($this->gzJson);
    }

    function write($object){
        $gz = gzopen('logs-'.time().'.json.gz','w9');
        gzwrite($gz, json_encode($object));
        gzclose($gz);
    }

    function download(){
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=logs.json.gz");
        echo $this->gzJson;
    }
}
