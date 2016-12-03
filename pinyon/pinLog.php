<?php
/**
* Class for logging
* Singleton, access through i()
* @config (class) logger IpinLogger
* @config (class) msgprefix string: the text used as prefix for each message
* @config (class) dologging boolean: true means logging is on, false logging is off
* 
* @package Pinyon.Log
* @see <a href="http://www.tigrez.nl">Pinyon Pine</a>
* @author Tigrez Software L.Geritz
*/
class pinLog extends pinConfigurable
{
	private static $pinlog;
    private static $file;
    const FATAL=true;

    private $logger;
        
    public function __construct(){ 
        
        parent::__construct();
        if(!$this->cfg_logger) trigger_error( pinBasalError::E005,E_USER_ERROR); 
        
        $this->cfg_msgprefix=pinConfig::resolve($this->cfg_msgprefix);
        
        $this->logger=$this->toObject($this->cfg_logger,'IpinLogger');
        $this->logger->setDoLog($this->cfg_dologging);
        $this->logger->setMsgPrefix($this->cfg_msgprefix);
         
    } 
    
    protected $cfg_logger=null;
    protected $cfg_dologging=true;
    protected $cfg_msgprefix;
            
    protected function prefix(){
        return 'cfg_';
    }
    
    public function doLogging($do){
        $this->logger->setDoLog($do);
    }        
    
    public function log($msg,$fatal=false){
        $this->logger->log($msg,$fatal);
    }
    
    /**
    * get instance of pinLog
    * 
    * @return pinLog instance
    */
    public static function i(){
		
        if(!isset(self::$pinlog)){
            
            self::$pinlog=new pinLog();
            
        }

		return self::$pinlog;
	} 
    
}
?>