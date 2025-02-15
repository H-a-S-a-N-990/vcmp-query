<?php
/**
 * This API connects directly to the VC:MP server, without any need for any
 * middlemen connections.
 * Your server must have fsockopen enabled in order to access the 
 * functions that have been made available from this.
 *
 * @package vcmpAPI
 * @version 1.0
 * @author Your Name
 * @copyright 2025
 */

class VcmpQueryAPI
{
    /**
     * @ignore
     */
    private $rSocket = false;
    
    /**
     * @ignore
     */
    private $aServer = array();
    
    /**
     * Creation of the server class.
     *
     * @param string $sServer Server IP, or hostname.
     * @param integer $iPort Server port
     */
    public function __construct($sServer, $iPort = 8192)
    {
        /* Fill some arrays. */
        $this->aServer[0] = $sServer;
        $this->aServer[1] = $iPort;
        
        /* Start the connection. */    
        $this->rSocket = fsockopen('udp://'.$this->aServer[0], $this->aServer[1], $iError, $sError, 2);
        
        if(!$this->rSocket)
        {
            $this->aServer[4] = false;
            return;
        }
        
        socket_set_timeout($this->rSocket, 2);
        
        $sPacket = 'VCMP';
        $sPacket .= chr(strtok($this->aServer[0], '.'));
        $sPacket .= chr(strtok('.'));
        $sPacket .= chr(strtok('.'));
        $sPacket .= chr(strtok('.'));
        $sPacket .= chr($this->aServer[1] & 0xFF);
        $sPacket .= chr($this->aServer[1] >> 8 & 0xFF);
        $sPacket .= 'i';
        
        fwrite($this->rSocket, $sPacket);
        
        if(fread($this->rSocket, 10))
        {
            if(fread($this->rSocket, 4) == 'VCMP')
            {
                $this->aServer[4] = true;
                return;
            }
        }
        
        $this->aServer[4] = false;
    }
    
    /**
     * @ignore
     */
    public function __destruct()
    {
        @fclose($this->rSocket);
    }
    
    /**
     * Used to tell if the server is ready to accept queries.
     *
     * If false is returned, then it is suggested that you remove the
     * class from active use, so that you can reload the class if needs
     * be.
     *
     * @return bool true if success, false if failure.
     */
    public function isOnline()
    {
        return isset($this->aServer[4]) ? $this->aServer[4] : false;
    }
    
    /**
     * This function is used to get the server information.
     *
     * @return array Array of server information.
     */
    public function getInfo()
    {
        @fwrite($this->rSocket, $this->createPacket('i'));
        
        fread($this->rSocket, 11); // Read the header

        $aDetails['password'] = (integer) ord(fread($this->rSocket, 1)); // Password protected?
        
        $aDetails['players'] = (integer) $this->toInteger(fread($this->rSocket, 2)); // Player count
        
        $aDetails['maxplayers'] = (integer) $this->toInteger(fread($this->rSocket, 2)); // Max players
        
        $iStrlen = ord(fread($this->rSocket, 4)); // Hostname length
        if(!$iStrlen) return -1;
        
        $aDetails['hostname'] = (string) fread($this->rSocket, $iStrlen); // Hostname
        
        $iStrlen = ord(fread($this->rSocket, 4)); // Gamemode length
        $aDetails['gamemode'] = (string) fread($this->rSocket, $iStrlen); // Gamemode
        
        $iStrlen = ord(fread($this->rSocket, 4)); // Version length
        $aDetails['version'] = (string) fread($this->rSocket, $iStrlen); // Server version
        
        return $aDetails;
    }
    
    /**
     * This function gets a basic list of all the players on the server.
     *
     * @return array Array of player information.
     */
    public function getBasicPlayers()
    {
        @fwrite($this->rSocket, $this->createPacket('c'));
        fread($this->rSocket, 11);
        
        $iPlayerCount = ord(fread($this->rSocket, 2));
        $aDetails = array();
        
        if($iPlayerCount > 0)
        {
            for($iIndex = 0; $iIndex < $iPlayerCount; ++$iIndex)
            {
                $iStrlen = ord(fread($this->rSocket, 1));
                $aDetails[] = array
                (
                    "nickname" => (string) fread($this->rSocket, $iStrlen),
                    "score" => (integer) $this->toInteger(fread($this->rSocket, 4)),
                );
            }
        }
        
        return $aDetails;
    }
    
    /**
     * This function gets a detailed list of all the players on the server.
     *
     * @return array Array of player information.
     */
    public function getDetailedPlayers()
    {
        @fwrite($this->rSocket, $this->createPacket('d'));
        fread($this->rSocket, 11);
    
        $iPlayerCount = ord(fread($this->rSocket, 2));
        $aDetails = array();
        
        for($iIndex = 0; $iIndex < $iPlayerCount; ++$iIndex)
        {
            $aPlayer['playerid'] = (integer) ord(fread($this->rSocket, 1));
            
            $iStrlen = ord(fread($this->rSocket, 1));
            $aPlayer['nickname'] = (string) fread($this->rSocket, $iStrlen);
            
            $aPlayer['score'] = (integer) $this->toInteger(fread($this->rSocket, 4));
            $aPlayer['ping'] = (integer) $this->toInteger(fread($this->rSocket, 4));
            
            $aDetails[] = $aPlayer;
            unset($aPlayer);
        }
        
        return $aDetails;
    }
    
    /**
     * @ignore
     */
    private function toInteger($sData)
    {
        if($sData === "")
        {
            return null;
        }
        
        $iInteger = 0;
        $iInteger += (ord($sData[0]));
 
        if(isset($sData[1]))
        {
            $iInteger += (ord($sData[1]) << 8);
        }
        
        if(isset($sData[2]))
        {
            $iInteger += (ord($sData[2]) << 16);
        }
        
        if(isset($sData[3]))
        {
            $iInteger += (ord($sData[3]) << 24);
        }
        
        if($iInteger >= 4294967294)
        {
            $iInteger -= 4294967296;
        }
        
        return $iInteger;
    }
    
    /**
     * @ignore
     */
    private function createPacket($sPayload)
    {
        $sPacket = 'VCMP';
        $sPacket .= chr(strtok($this->aServer[0], '.'));
        $sPacket .= chr(strtok('.'));
        $sPacket .= chr(strtok('.'));
        $sPacket .= chr(strtok('.'));
        $sPacket .= chr($this->aServer[1] & 0xFF);
        $sPacket .= chr($this->aServer[1] >> 8 & 0xFF);
        $sPacket .= $sPayload;
    
        return $sPacket;
    }
}
?>
