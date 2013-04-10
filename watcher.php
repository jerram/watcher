<?php
    require_once 'includes/common_log_parser.php';
    require_once 'includes/class.gzObject.php';
    require_once 'includes/class.request.php';
    require_once 'includes/class.user.php';
    require_once 'includes/class.users.php';
    require_once 'vendor/autoload.php';
    $loader = new Twig_Loader_Filesystem('templates');
    $twig = new Twig_Environment($loader, array(
        //'cache' => 'compilation_cache',
    ));

    //darkestpowers.mobi.access.log.1
    //lacandy.mobi.access.log
    $start = microtime(true);
    $parser = new CommonLogParser('/var/vhosts/sandbox/watcher/logs/linkme/apache2/lacandy.mobi.access.log');
    $parse = microtime(true);
    //$parser->move_pointer_to_time(time() - 36000);
    $users = new Users;
    // retrieve one line at a time from the new location of the pointer
    while (false !== ($line = $parser->get_next_line())) {
        $match = preg_match("/\.(jpg|jpeg|gif|bmp|png|css|ico|js|json|txt)/i",$line['path']);
        if($match === false){
            $content .=  '<br>preg_match error: '.__LINE__;
        }
        elseif($match === 0){
            $request = new Request($line);
            $users->addRequest($request);
        }
        $count++;
    }

    $gzObject = new gzObject();
    $gzObject->write($users);

    $time = microtime(true) - $start;
    $content .=  $count.' lines in '.$time.'(s) for '.($count/$time).' lines/s<br />';
    $users->orderByHits();

    foreach ($users->getUsers() as $user){
        $content .=  '<a href title="'.$user->get('userAgent').'">'.$user->get('ipAddress').'</a>
        ';
        $content .=  ' (<a href="" title="">'. $user->countRequests().'
        <div class="paths">'.implode("\n<br />",$user->getRequests()).'</div>
        </a>)<br />
        ';
    }
    echo $twig->render('watcher.twig', array('content' => $content));

?>