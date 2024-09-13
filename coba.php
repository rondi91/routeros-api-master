<?php 
require('routeros-api-master/routeros_api.class.php');

$ip = '192.168.9.1';
$user = 'rondi';
$pass = '21184662';


$API = new RouterosAPI();

$API->connect($ip,$user,$pass);

$API->connect($ip,$user,$pass);
    $API->write('/ppp/active/print');
    $connections = $API->read();
    var_dump($connections);
    $API->disconnect();
