<html>
    <head>
        <style>
.paths {
    display: none;
    border:1px solid grey;
    position:absolute;
    top:200px;
    left:400px;
}

a:hover .paths {
    display: block;
}
</style>
</head>
<body>
    <h1>Watcher</h1>

    <h2>Aims</h2>
    <ul>
        <li>See where requests are being generated</li>
        <li>See what is being hit</li>
        <li>zoom in on ip</li>
        <li>realtime</li>
    </ul>

    <h2>Tasks</h2>
    <ul>
        <li><strike>remove image, css and js files from request list</strike></li>
        <li>show date / time range of results</li>
        <li>click ip to see details</li>
        <li>Add some bootstrap</li>
        <li>tail logs</li>
        <li>memcache or sql</li>
        <li>save results</li>
        <li>force https</li>
        <li>block ip</li>
        <li>unzip logs</li>
        <li>show refferer</li>
        <li>read and show errors</li>
        <li>replay</a>
        <li>requests/s total by ip and url</a>
        <li>top burst request rates</li>
    </ul>

    <h2>Details</h2>
    <ul>
        <li>link to url</a>
        <li>location, whois on ip</a>
    </ul>
<?php

require_once 'Apache-Logfile-Parser/common_log_parser.php';
//darkestpowers.mobi.access.log.1
//lacandy.mobi.access.log

$start = microtime(true);
$parser = new CommonLogParser('/var/vhosts/sandbox/watcher/logs/linkme/apache2/darkestpowers.mobi.access.log.1');
$parse = microtime(true);
//$parser->move_pointer_to_time(time() - 36000);
$users = new Users;
// retrieve one line at a time from the new location of the pointer
while (false !== ($line = $parser->get_next_line())) {
    $match = preg_match("/\.(jpg|jpeg|gif|bmp|png|css|ico|js|json|txt)/i",$line['path']);
    if($match === false){
        echo '<br>preg_match error: '.__LINE__;
    }
    elseif($match === 0){
        $request = new Request($line);
        $users->addRequest($request);
    }
    $count++;
}
$time = microtime(true) - $start;
echo $count.' lines in '.$time.'(s) for '.($count/$time).' lines/s<br />';
$users->orderByHits();

foreach ($users->getUsers() as $user){
    echo '<a href title="'.$user->get('userAgent').'">'.$user->get('ipAddress').'</a>
    ';
    echo ' (<a href="" title="">'. $user->countRequests().'
    <div class="paths">'.implode("\n<br />",$user->getRequests()).'</div>
    </a>)<br />
    ';
}

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
        $this->stats->referrer[$request->referrer]++;
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
?>
</body>
</html>