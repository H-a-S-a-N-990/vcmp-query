<?php
class VcmpQueryAPI
{
    private $rSocket = false;
    private $aServer = array();

    public function __construct($sServer, $iPort = 8192)
    {
        $this->aServer[0] = $sServer;
        $this->aServer[1] = $iPort;

        // Open a UDP socket
        $this->rSocket = fsockopen('udp://' . $this->aServer[0], $this->aServer[1], $iError, $sError, 2);

        if (!$this->rSocket) {
            $this->aServer[4] = false;
            return;
        }

        socket_set_timeout($this->rSocket, 2);
        $this->aServer[4] = true;
    }

    public function __destruct()
    {
        @fclose($this->rSocket);
    }

    public function isOnline()
    {
        return isset($this->aServer[4]) ? $this->aServer[4] : false;
    }

    public function getInfo()
    {
        @fwrite($this->rSocket, $this->createPacket('i'));
        $response = fread($this->rSocket, 1024);
        
        if (!$response || strlen($response) < 28) {
            return false;
        }

        $aDetails = array();
        $aDetails['version'] = trim(substr($response, 11, 12));
        $aDetails['password'] = ord(substr($response, 23, 1));
        $aDetails['players'] = $this->toInteger(substr($response, 24, 2));
        $aDetails['maxplayers'] = $this->toInteger(substr($response, 26, 2));

        $offset = 28;
        $aDetails['hostname'] = $this->readString($response, $offset);
        $aDetails['gamemode'] = $this->readString($response, $offset);
        $aDetails['mapname'] = $this->readString($response, $offset);

        return $aDetails;
    }

    public function getPlayers()
    {
        @fwrite($this->rSocket, $this->createPacket('c'));
        $response = fread($this->rSocket, 3072);

        if (!$response || strlen($response) < 13) {
            return false;
        }

        $iPlayerCount = $this->toInteger(substr($response, 11, 2));
        $aDetails = array();
        $offset = 13;

        for ($i = 0; $i < $iPlayerCount; $i++) {
            $aDetails[] = $this->readString($response, $offset);
        }

        return $aDetails;
    }

    public function getPing()
    {
        @fwrite($this->rSocket, $this->createPacket('p'));
        return fread($this->rSocket, 1024);
    }

    private function createPacket($sPayload)
    {
        $sPacket = 'MP04';
        $ipParts = explode('.', $this->aServer[0]);

        if (count($ipParts) != 4) {
            return false;
        }

        foreach ($ipParts as $part) {
            $sPacket .= chr(intval($part));
        }

        $sPacket .= chr($this->aServer[1] & 0xFF);
        $sPacket .= chr($this->aServer[1] >> 8 & 0xFF);
        $sPacket .= $sPayload;

        return $sPacket;
    }

    private function toInteger($sData)
    {
        $iInteger = 0;
        for ($i = 0; $i < strlen($sData); $i++) {
            $iInteger |= ord($sData[$i]) << ($i * 8);
        }
        return $iInteger;
    }

    private function readString($sData, &$offset)
    {
        $length = $this->toInteger(substr($sData, $offset, 4));
        $offset += 4;
        $string = substr($sData, $offset, $length);
        $offset += $length;
        return trim($string);
    }
}
?>
