<?php
require_once __DIR__ ."/../App/bootstrap.php";
try{
    $f3->get('logger')->write("method: " .$_SERVER['REQUEST_METHOD']);
    $_SERVER['REQUEST_URI'] = str_replace('/v3/', '/', $_SERVER['REQUEST_URI']); 
    $f3->get('logger')->write('Request started ' . $_SERVER['REQUEST_URI']);

    if (!$f3->get('service.user')->isConnected() && ($_SERVER['REQUEST_URI'] != '/') ) {
        //$f3->reroute('/');

    } elseif ($f3->get('service.user')->isConnected() && ( $_SERVER['REQUEST_URI'] == '/')) {
        $f3->get('logger')->write('Fi /dashboard');    
        $f3->reroute('/dashboard');
    }

    $f3->get('logger')->write('Request started');
    $f3->run();

} catch (Error $e) {
    dump(get_defined_vars());
    dump($e->getMessage());
    dump($e->getTraceAsString());
    dump($e);
}
catch (HttpException $e) {
    dump(get_defined_vars());
    dump($e);
    //throw $e;
    throw new HttpException($e->getStatusCode());
} catch (Exception $e) {
    dump(get_defined_vars());
    dump($e->getMessage());
    dump($e->getTraceAsString());
    dump($e);
}

