<?php

require_once 'VcmpQueryAPI.php'; // Include the VCMP query class

$query = new VcmpQueryAPI('57.129.44.185', 8192);

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
