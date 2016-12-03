<?php
/**
* Interface for logger
* 
* @package Pinyon.Log 
* @see <a href="http://www.pinyonpine.nl">Pinyon Pine</a>
* @author <a href="http://www.tigrez.nl">Tigrez Software / Luc Geritz</a>
*/
interface IpinLogger{
    
    /// @param $msg string message to log
    /// @return void
    public function Log($msg);
    public function setMsgPrefix($prefix);
    public function setDoLog($is_on);
}
?>
