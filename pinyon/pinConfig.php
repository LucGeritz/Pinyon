<?php
define('MAIN_CONFIG_FILE','appl.cfg.php');
define('CLASS_CONFIG_SUFFIX','.cfg.php');
/**
* Class for managing configuration files.
* @note Configuration files should return a php associative array (key--value). Through the pinConfig class these keys can be accessed as properties. These files should have extension '.cfg.php'  
* @note It uses the constant PIN_CONFIGDIR to locate your config files. Should be set at the very first occasion.
* @note As of 0.1.4 you can put a custom configfile (as accessed by pinConfig::i('myOwn.cfg')) anywhere by specifying a 2nd parameter containing the folder name.
* @note Singleton implementation. Access through i(filename) or thisClass(), someClass()
* @todo Config files are cached during transaction. Per session would be more efficient but is this a potential security risk.  
* 
* @package Pinyon.Config
* @see <a href="http://www.pinyonpine.nl">Pinyon Pine</a>
* @author <a href="http://www.tigrez.nl">Tigrez Software / Luc Geritz</a>
*/
class pinConfig
{
    const USE_TOPCLASS = true;
    
	private static $pincfg_instances=array();
    private $settings;
    private $currentFile;
    private $isSystemConfig;
    private $handle;
        
    /// @param $file string the name (complete with path and ext.) of the config file
	private function __construct($file){
		$this->currentFile=$file;
		$this->isSystemConfig=substr($file,0,strlen(PIN_CONFIGDIR))===PIN_CONFIGDIR;
        $this->settings=@include($file);
	}

    /**
	* Resolve a certain set of predefined variables. Called for every value read
	* @param mixed $msg message to resolve, only if string
	* @param boolean $resolvetime if true resolves %TIME%, resolvetime might not be the time you meant
	* 
	* @return string the resolved message
	* 
	* - %APPL% resolves to the value of PIN_APPL
    * - %PININCL% resolves to the value of PIN_INCLUDE
    * - %SELF% resolves to the value of $_SERVER['PHP_SELF']
    * - %CURDIR% resolves to value of the dirname of $_SERVER['PHP_SELF']
    * - %IP% resolves to the value of $_SERVER['REMOTE_ADDR']
    * - %AGENT% resolves to the value of $_SERVER['HTTP_USER_AGENT']
	* - %ROOT% resolves to pinUrl::i()->getRoot()    
	*/
    public static function resolve($msg,$resolvetime=true){
       if(is_string($msg) && (strpos($msg,'%')!==false)){
           $msg=str_replace('%APPL%',PIN_APPL,$msg);
           $msg=str_replace('%PININCL%',PIN_INCLUDE,$msg);
           $msg=str_replace('%SELF%',$_SERVER['PHP_SELF'],$msg);
           $msg=str_replace('%CURDIR%',dirname($_SERVER['PHP_SELF']),$msg);
           $msg=str_replace('%IP%',$_SERVER['REMOTE_ADDR'],$msg);
           $msg=str_replace('%AGENT%',$_SERVER['HTTP_USER_AGENT'],$msg);
           $msg=str_replace('%DATE%',date("Ymd"),$msg);
           $msg=str_replace('%ROOT%',pinUrl::i()->getRoot(),$msg);
           if($resolvetime) $msg=str_replace('%TIME%',date("Y-m-d H:i:s"),$msg);
       }
       return $msg;
    }
	
	public function getFileName(){
		return $this->currentFile;	
	}	
	
	public function setHandle($handle){
		$this->handle = $handle;	
		return $this;
	}
	
	public function getHandle(){
		return $this->handle;
	}
	
	public function __get($var)
	{
        $val=$this->settings[$var];
        return self::resolve($val);
	}
	
    public function __set($var,$val)
	{
		$this->settings[$var]=$val;
	}
    
    /**
	* get all settings at once
	* 
	* @return mixed array assoc.array of settingname=>settingvalue
	*/
    public function getSettings(){
        return $this->settings;
    }
    
    /**
	* set all settings as once
	* 
	* @param mixed array $settings
	*/
    public function setSettings(array $settings){
		$this->settings = $settings;
	}
	
    /**
	* Save settings to config file
	* @param string $file to save to. If not specified the current filename is used 
	* 
	* @note will not save if system config file (= from PIN_CONFIGDIR), no error though.
	* @note only supports string values and string keys!
	* 
	* @return void
	*/
    public function save($file=''){
		
		if(!$this->isSystemConfig){
			
			if(!$file){
				$file = $this->currentFile;
				if(!$file){
					throw new Exception('cannot save config file without a name');
				}
			}
			$outp='<?php return ';
			$outp.=var_export($this->settings,true);
			$outp .=  '; ?>';
			file_put_contents($file,$outp); 
		}
	}
	
	/**
	* check if setting exists
	* 
	* @param string $var var to check existance of
	* @return boolean true if $var exists, otherwise false	
	*/
    public function exists($var)
	{
		return isset($this->settings[$var]);
	}

	/**
	* Get instance of pinConfig for specific config file for the class in which it is called
	* Allows for class specific configuration
	* 
	* @param bool $useTopClass defaults to false, true means we want the cfg file of the toplevel superclass 
	* @return pinConfig instance for class from which function is called.
	*/
	public static function thisClass($useTopClass=false){
        $backtrace=debug_backtrace(false); // optimize from 5.4 on!
        $class=$backtrace[1]['class'];
        if($useTopClass){
            $class=pig::getTopClass($class);
        }
        $file=$class.CLASS_CONFIG_SUFFIX;
        return self::i($file);
   }

   	
   public static function someClass($class){
        $file=$class.CLASS_CONFIG_SUFFIX;
        return self::i($file);
   }
   
   /// Get the instance of pinConfig for a particulair config file
   /// @note no default extensions
   /// @param $file mixed if string the config file to open. If none specified the main config file is opened. if numeric it is a handle 
   /// @param $dir string the folder where config file resides. Ignored if file=''. So only usable for your custom files!
   /// @return pinConfig instance for particulair config file
   public static function i($file='',$dir=''){
		
		if(is_numeric($file)){
				$file=(int)$file;
				$keys=array_keys(self::$pincfg_instances);
				if(array_key_exists($file,$keys)){
					return self::$pincfg_instances[$keys[$file]];
				}
				else throw new Exception("$file is an unknown handle");
		}        
		
        if($dir===''){
			$dir=PIN_CONFIGDIR;		
		}
		else{
			$dir=pig::forceSlash($dir);
		}
		
        if($file===''){
            $file=PIN_CONFIGDIR.MAIN_CONFIG_FILE;     
        }
        else{
            $file=$dir.$file;
        }
		
       	if(!array_key_exists($file,self::$pincfg_instances)){
			self::$pincfg_instances[$file]=new pinConfig($file);
			$handle=array_search($file, array_keys(self::$pincfg_instances));
			self::$pincfg_instances[$file]->setHandle($handle);
		}
		
		return self::$pincfg_instances[$file];
		
	} 
}
