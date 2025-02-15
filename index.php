<?php

require_once 'VcmpQueryAPI.php';  // Make sure this file exists

$ip = '57.129.44.185';
$port = 8192;

$query = new VcmpQueryAPI($ip, $port);
$data = $query->getInfo();  // Use getInfo() instead of connect()

if ($data) {
    print_r($data);
} else {
    echo "Failed to connect to the VCMP server.";
}
?>
