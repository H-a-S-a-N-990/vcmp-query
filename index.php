<?php
require_once 'VcmpQueryAPI.php';

header('Content-Type: application/json');

$sServer = $_GET['server'] ?? '127.0.0.1';
$iPort = $_GET['port'] ?? 8192;

$oQuery = new VcmpQueryAPI($sServer, $iPort);

if ($oQuery->isOnline()) {
    $aInfo = $oQuery->getInfo();
    $aPlayers = $oQuery->getBasicPlayers();
    
    echo json_encode([
        'status' => 'online',
        'info' => $aInfo,
        'players' => $aPlayers,
    ]);
} else {
    echo json_encode([
        'status' => 'offline',
        'message' => 'Server is offline or unreachable.',
    ]);
}
