<?php
// Include the VcmpQueryAPI class
require_once 'VcmpQueryAPI.php';

// Set the content type to JSON
header('Content-Type: application/json');

// Get query parameters from the URL
$sServer = $_GET['server'] ?? '127.0.0.1'; // Default server IP
$iPort = $_GET['port'] ?? 8192; // Default server port

// Create an instance of the VcmpQueryAPI class
$oQuery = new VcmpQueryAPI($sServer, $iPort);

// Check if the server is online
if ($oQuery->isOnline()) {
    // Get server information
    $aInfo = $oQuery->getInfo();
    
    // Get player list (basic or detailed)
    $aPlayers = $oQuery->getBasicPlayers(); // Use getDetailedPlayers() for more details
    
    // Prepare the response
    $aResponse = [
        'status' => 'online',
        'server' => [
            'ip' => $sServer,
            'port' => $iPort,
        ],
        'info' => $aInfo,
        'players' => $aPlayers,
    ];
} else {
    // Server is offline or unreachable
    $aResponse = [
        'status' => 'offline',
        'message' => 'The server is offline or unreachable.',
    ];
}

// Output the response as JSON
echo json_encode($aResponse, JSON_PRETTY_PRINT);
?>
