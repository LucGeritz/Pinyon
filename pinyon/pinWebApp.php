<?php
require_once(PIN_INCLUDE.'pinConfigurable.php');
/**
* Represents a Pinyon web application
* @note pinWebApp constructor makes a copy of its settings which can be accessed e.g. anywhere in the application you can refer to the viewdir name as pinReg::i()->app->viewdir;
* @note in the layout file you can refer to any setting just by $this->settingname 
* Other settings added to app are:
* - action string: the action requested
* - authenabled: boolean true yes enabled, based on useauth and authisoff settings 
* - currentlang: string the current language
* - fullurl string: the full url  
* - fullurlnoparms: the full url without query parms
* - initialaction string: action as requested
* - initialroutename string: routename as requested
* - ip string ip address from caller (not trustworthy!)
* - prevurl string: the previous url 
* - routename string: routename taken
* - self string: current script (not file) same as $_SERVER['PHP_SELF'];
* - semanticurls: boolean are semantic urls used, set to false, is overwritten by pinSURLWebApp to true 
* - urlpath string: url path
* - urlpathplusroute string: url path plus route but no other params, !!makes no sense if sematicurls are used
* - urlquery array: param=>value (is, of course, same as $_GET)
* - urlquerystr string: query as string
* pinWebApp also adds the settings from pinRouter.
* @config class markdown IpinMarkdown; name of class or instance of class implementing markdown  
* @config class translator IpinTranslator: name of class or instance for translation, is added to the pinReg as trans
* @config class isclosed boolean: if true pinWebApp will start the route as specified in pinRouter»closedroute
* @config class layoutfile string: file that is used for layout, i.e. in which the context is echoed
* @config class dologging boolean: if true pinWebApp will do some logging 
*
* @package Pinyon.Route
* @see <a href="http://www.pinyonpine.com">Pinyon Pine</a>
* @author <a href="http://www.tigrez.nl">Tigrez Software/ Luc Geritz</a>
*/
class pinWebApp extends pinConfigurable{
	
    protected $settings=array('semanticurls'=>false);
	
	private $loginController;
    private $routename;
    private $action;
	private $get;
		
	private function log($msg){
		if($this->settings['dologging']){
			pinLog::i()->log($msg);
		}    
	} 
               
    private function splitRouteParm($routename){
    	
    	$routename=strip_tags($routename);
        $arr=explode('/',$routename);
        $this->routename=$arr[0];
        if(isset($arr[1])) $this->action=$arr[1];
    }
    
    private function getIp(){
		$ip='';
		if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != '') {
    		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} 
		else {
    		$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}
	
    /**
    * Pseudo-Event called just after transaction has started
    * 
    * @return void
    */
    protected function onStart(){
       // please override
    }
	/**
    * Pseudo-Event called after onBegin denied access to start (i.e. returned false)
    * Any communication as to why start is canceled can be done by properties or methods of the controller, e.g. by a cancelCode.
    * @param pinBaseController $ctrl
    * @return void
    */
    protected function onCanceled($ctrl){
		// please override
	}
    /**
    * Pseuso-event called just before transaction is finished
    * Call parent in override
    * @return void
    */
    protected function onEnd(){
        pinTmpFileList::sanitate();
        $_SESSION['prevurl']=$this->settings['fullurl'];
        $_SESSION['prevroute']=$this->routename;
        $_SESSION['prevaction']=$this->action;
    }
    	
    /// 'event' called before content is included in layoutfile, last chance to alter it!
    /// @note override if you want to alter content at this stage. No need to call parent::onBeforeRender in override
    /// @param $content string the content build up just before it will be shown
    /// @return string the altered content
    protected function onBeforeRender($content){
        return $content;
        // please override
    }    

	protected function getGet(){
		return $this->get;
	}    
	
	protected function setGet(array $get){
		$this->get = $get;	
	}
	
	protected function additionalSettings(){
		
		$this->settings['prevurl']=   pig::getStrElement($_SESSION,'prevurl','');
        $this->settings['prevroute']= pig::getStrElement($_SESSION,'prevroute','');
        $this->settings['prevaction']=pig::getStrElement($_SESSION,'prevaction','');
        $fullurl=pig::fullUrl();
        
		
		$this->settings['fullurl']=$fullurl;
		$this->settings['fullurlnoparms']=pig::fullUrlNoParms($fullurl);
        $this->settings['urlpath']=parse_url($fullurl, PHP_URL_PATH);
        $this->settings['urlquery']=$_GET;
		$this->settings['urlquerystr']=parse_url($fullurl,PHP_URL_QUERY);
		
        $this->settings['self']=htmlentities(pig::getStrElement($_SERVER,'PHP_SELF',''));
        $this->settings['ip']=$this->getIp();
          
        }
	
	/**
	* sets auth object in Registry, defaults to instance of pinAuthRouter class
	* 
	* @return void
	*/
	protected function setAuthRouterObject(){
        
        $auth=null;
     
        if( pig::getStrElement($this->settings,'authrouter','' )){
            $auth=$this->toObject($this->settings['authrouter'],'IpinAuthRouter');
        }
        else{
            $auth= new pinAuthRouter();
        }
        pinReg::i()->auth=$auth;
        
   }            
	
  	
   protected function setTransObject(){
       
       $trans=$this->toObject(pig::getStrElement($this->settings,'translator',''),'IpinTranslator');

       pinReg::i()->trans=$trans;
       return $trans;
   } 
	
    public function __construct(){ 
		
		// settings are read by baseclass pinConfigurable
        parent::__construct();
        
        
        if(!is_array($this->settings)) trigger_error(pinBasalError::E007,E_USER_ERROR);
        $this->settings=array_merge($this->settings,pinConfig::someClass('pinRouter')->getSettings());
		
		if(pig::getStrElement($this->settings,'markdown','')){
			pinReg::i()->markdown=$this->toObject($this->settings['markdown'],'IpinMarkdown');
		}
		
		// put entry of authrouter in registry if we use authorization
		// .. setting comes from pinRouter but is merged into webapp settings 
		$this->settings['authenabled']=pinRouter::useAuth();
	    
        if($this->settings['authenabled']) $this->setAuthRouterObject();
        
        if($this->settings['translator']){
             $trans=$this->setTransObject();
             
            $lang=pig::getStrElement($_GET,'lang','');
			
            if(!$lang){
				$lang=pig::getStrElement($_SESSION,'lang','');
                // empty string becomes default language
                if(!$lang){
                    $lang=pinConfig::someClass('pinTrans')->defaultlang;
                }  
			}
			$_SESSION['lang']=$lang;
            $trans->setLanguage($lang);            
            $this->settings['currentlang']=$lang;			
			
        }    
        
		$this->additionalSettings();
		
		// finally add myself to the registry as 'app'
		pinReg::i()->app=$this;      
	}
       
       
    /**
    * redirect to given url
	* @deprecated use pinUrl::i()->redirect(), supports semantic urls
	* @param string $tourl url to redirect to
	*/
    public function redirect($tourl){
        $this->log('Redirecting to '.$tourl);
        header("Location: ".$tourl);
    	die();
    }
	/**
	* redirect based on route
	* @deprecated use pinUrl::i()->redirectRouteBasedUrl(), supports semantic urls
	* @param string $route
	* @param array $params url query parms
	*/    
    public function redirectRoute($route,$params=array()){
        $url=$this->settings['self'].'?r='.$route;
        if(count($params)>0){
            foreach($params as $key=>$val){
                $url.='&'.urlencode($key).'='.urlencode($val);
            }            
        }
        $this->redirect($url);
    }
    
    /**
    * __get magic method is used to read from app settings array as if it were properties
    * 
    * @param string $var
    * @return mixed value
    */
	public function __get($var)
	{
		if(!isset($this->settings[$var])) return '';
		$val=$this->settings[$var];
		return pinConfig::resolve($val);
	}
	/**
	* Return all settings as array
	* 
	* @return mixed array all settings as array 
	*/
    public function getSettings(){
		return $this->settings;
	}   
	/**
    * __set magic method is used to add to the app settings array as if it were properties
    * In this case also means all class variables added by pinConfigurable end up is settings as well!  
    * @param string $var
    * @param string $val
    * @return void
    */
    public function __set($var,$val)
	{
		$this->settings[$var]=$val;
	}
	
	/**
    * Start the controller which implements a given route 
    * When starting succeeds it includes the controller 
    * @param string $routename string name of a route which is resolved to a controller to start
    */
    public function start(){
				
		$this->get = $_GET;
		
        $this->onStart();
        	
        $routename = pig::getStrElement($this->get, 'r');
        	
		$this->log("Requested by user: $routename");
		
        // get routename/action
        if($routename) $this->splitRouteParm($routename);
                                
        $this->settings['urlpathplusroute']=$this->settings['urlpath'].'?r='.$this->routename;
		$this->settings['initialroutename']=$this->routename;
	    $this->settings['initialaction']=$this->action;
	
		$controller=pinRouter::getController($this->routename,$this->action);
		
        $this->settings['action']=$this->action;
        
		$this->log('Suggested route by router: '.$this->settings['routename'].' '.$this->settings['action']);

		if($controller->onBegin($this->action)){
			
        	if($this->action){
            	$controller->{'start'.$this->action}(array('get'=>$this->get));            
        	}
        	else{
    			$controller->start(array('get'=>$this->get));
			}
			
		}
		else{
			$this->onCanceled($controller);
		}
		
		$content = $controller->getContent();
        
        $controller->onEnd($this->action);
         
        $content=$this->onBeforeRender($content);
        
		include($this->settings['layoutfile']);
      
        flush();
        
	    $this->onEnd();	
	}
}
