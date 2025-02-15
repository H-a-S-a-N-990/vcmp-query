<?php
// Include the Server class (if it's in a separate file, adjust the path)
require_once 'Server.php';

// Create a new instance of the Server class with the IP and port
$server = new Server("57.129.44.185", 8192);

// Refresh the server information and player list
$server->Refresh();

// Output the server details
if ($server->success) {
    echo "Server Name: " . $server->name . "<br>";
    echo "Version: " . $server->version . "<br>";
    echo "Passworded: " . ($server->passworded ? "Yes" : "No") . "<br>";
    echo "Players: " . $server->players . "/" . $server->max_players . "<br>";
    echo "Player List: <br>";
    print_r($server->player_list); // Display the player list
} else {
    echo "Failed to retrieve server information.";
}
?>
