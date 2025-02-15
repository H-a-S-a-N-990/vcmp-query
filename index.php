<?php

require_once 'VcmpQueryAPI.php'; // Include the VCMP query class

$query = new VcmpQueryAPI('192.168.4.1', 8192);

if ($query->connect()) {
    $info = $query->getInfo();
    $players = $query->getPlayers();

    echo "<pre>";
    print_r($info);
    print_r($players);
    echo "</pre>";
} else {
    echo "Failed to connect to the VCMP server.";
}

?>
