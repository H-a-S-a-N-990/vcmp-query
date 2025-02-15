<?php
// Include the Server class (if it's in a separate file, adjust the path)
require_once 'Server.php';

// Create a new instance of the Server class with the IP and port
$server = new Server("178.32.116.43", 4748);
print_r($server);
