<?php
require_once(PIN_INCLUDE.'pinConfigurable.php');
require_once(PIN_INCLUDE.'pinConfig.php');
require_once(PIN_INCLUDE.'IpinLogger.php');
require_once(PIN_INCLUDE.'pinBasalError.php');

/**
* Logger which logs to a textfile
* @config (class) logfile string file to log to
*
* @package Pinyon.Log
* @see <a href="http://www.pinyonpine.com">Pinyon Pine</a>
* @author <a href="http://www.tigrez.nl">Tigrez Software/ Luc Geritz</a>
* */
class pinTextLogger extends pinConfigurable implements IpinLogger {

    private $msgprefix;
    private $dolog;
    
    protected $cfg_logfile;
    
    protected function prefix(){
        return 'cfg_';
    }
    
    public function __construct(){
       parent::__construct();    
       
       if(!$this->cfg_logfile) trigger_error( pinBasalError::E006,E_USER_ERROR);
       $this->cfg_logfile=pinConfig::resolve($this->cfg_logfile);
       
    } 
    
    /**
    * Log a message
    * 
    * @param string $msg The message to log
    * @param boolean $fatal
    * @return void
    */
	public function log($msg,$fatal=false){
    
        if($fatal || $this->dolog){

           $msg=$this->msgprefix.($fatal ? '**** ' : '').$msg;
       
           $filehandle=fopen($this->cfg_logfile,"a");
           flock($filehandle, LOCK_EX); 
           fwrite($filehandle,$msg. chr(10));
           flock($filehandle, LOCK_UN); 
           fclose($filehandle);
        }
    }

    public function setMsgPrefix($prefix){
        $this->msgprefix=$prefix;
    }
    
    public function setDoLog($is_on){
        $this->dolog=$is_on;
    }
 
}