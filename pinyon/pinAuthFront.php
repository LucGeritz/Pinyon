<?php
/**
* Implementation of IpinAuthFront which assumes User Provided data to be stored in POST, SESSION or (if allowed) COOKIE
* 
* @config fieldnames array fieldname=>variablename. Defines variable names as exposed in 'user provision' (=Session in this implementation). Adds some obscurity for what it's worth. fieldname are  'password', 'user', 'action', 'save', 'message', 'authorized'. Values are variable names in session. 
* @config allowances array allowance=>allowed; allowance  are 'safe' and 'forget'. safe (boolean) true means allowed to remember user + password (=client cookie in thi implementation). forget (boolean) true means users can request new password
* @config dayskeepcookie int days cookies are kept (if allowance['safe']==true)
* @config forgetactionname name as it appears on forget button in form and as variable in POST. Preferably a i18n key
* @config loginactionname name as it appears on login button in form and as variable in POST. Preferably a i18n key
* @config crederrmsg message in user provision (=session) signalling there was a login error. Preferably a i18n key
* @config adminreqerrmsg message in user provision (=session) signalling user-is-not-admin error. Preferably a i18n key
* 
* @package Pinyon.Route
* @see <a href="http://www.pinyonpine.nl">Pinyon Pine</a>
* @author <a href="http://www.tigrez.nl">Tigrez Software / Luc Geritz</a>
*/
class pinAuthFront extends pinConfigurable implements IpinAuthFront{

    const SOURCE_POST='P';
    const SOURCE_SESSION='S';
    const SOURCE_COOKIE='C';
    const SOURCE_NOTFOUND='N';
    const SECONDS_PER_DAY=86400;    
    
    private $user;
    private $usersrc;
    private $pw;
    private $pwsrc;
    private $msg;
    private $doSave=false;
    private $action='';
    private $forgetAction;
    private $loginAction;
    
   /**
   * Get a variable from [P]ost, [S]ession or [C]ookie, [N] for not found
   */
   private function getFromPSC($varname,$allowcookie,&$source){

        $source=SOURCE_NOTFOUND;
        if(isset($_POST[$varname])){
            $source=self::SOURCE_POST;
            return $_POST[$varname];
        }
        if(isset($_SESSION[$varname])){
            $source=self::SOURCE_SESSION;
            return $_SESSION[$varname];
        }
        if($allowcookie && isset($_COOKIE[$varname])){
            $source=self::SOURCE_COOKIE;
            return $_COOKIE[$varname];
        }
        
        return false;
    }

	// can (should) be overridden by class config
    protected $cfg_fieldnames=array('password'=>  'O900801RrwerAre3lkjrw432j2jfdsf04243',
                                    'user'=>      'P043992eqgsdowfv93Rf03000wWzDerfsAde',
                                    'action'=>    'P340434434434ZAPAi3dcfewweE43D330001',
                                    'save'=>      'R4596034053323safd234adsDf23eqwedeEE',
                                    'message'=>   'Z0349VV2840457VV28404asE433K44dfFF01',
                                    'authorized'=>'Da3VB100WE433XxV0l3NLvlZ4KK1007e1K3I');
    protected $cfg_allowances=array('save'=>false,'forget'=>false); 
    protected $cfg_dayskeepcookie=2;
    protected $cfg_forgetactionname='Forget';
    protected $cfg_loginactionname='Login';
    protected $cfg_crederrmsg='Cred error';
    protected $cfg_adminreqerrmsg='Admin required';
    
	protected function prefix(){
		return 'cfg_';
	}
    
    public function __construct(){
 
        parent::__construct();
    }

    /**
	* @see IpinAuthFront
    */
    public function getForgetActionName(){
		return $this->forgetAction;
	}    
    /**
	* @see IpinAuthFront
    */
	public function getLoginActionName(){
		return $this->loginAction;
	}    
	/**
	* @see IpinAuthFront
    */
	public function getMsgForCredError(){
		return $this->cfg_crederrmsg;
	}
	/**
	* @see IpinAuthFront
    */
	public function getMsgForAdminReqError(){
		return $this->cfg_adminreqerrmsg;
	}
    /**
	* @see IpinAuthFront
    */
    public function getFieldnames(){
		return $this->cfg_fieldnames;
	}
    /**
	* @see IpinAuthFront
    */
	public function getAllowances($specific=''){
		if($specific){
			if(array_key_exists($specific,$this->cfg_allowances)){
                return $this->cfg_allowances[$specific];                
            }            
            else return false;
		}
		return $this->cfg_allowances;
	}
    /**
	* @see IpinAuthFront
    */
    public function getUser(){
        return $this->user;    
    }
	/**
	* @see IpinAuthFront
    */
    public function getUserAction(){
		return $this->action;
	}
    /**
	* @see IpinAuthFront
    */
    public function getPassword(){
        return $this->pw;
    }
    /**
	* @see IpinAuthFront
    */
    public function getMessage(){
		return $this->msg;
	}
    /**
    * @see IpinAuthFront
	* 
	* @return boolean true means is hashed
	*/
    public function isPasswordHashed(){
		return $this->pwsrc!==self::SOURCE_POST; 
	}
    /**
    * @see IpinAuthFront
    */    
    public function getUserSource(){
        return $this->usersrc;
    }
    /**
    * @see IpinAuthFront
    */    
    public function getPasswordSource(){
        return $this->pwsrc;
    }
    
    /**
	* @see IpinAuthFront
	* @note this implementation reads from post, session and if allowed cookie
	*/
	public function loadData(){

		$this->user=    $this->getFromPSC($this->cfg_fieldnames['user'],$this->cfg_allowances['save'],$this->usersrc);
        $this->pw =     $this->getFromPSC($this->cfg_fieldnames['password'],$this->cfg_allowances['save'],$this->pwsrc);
        $this->msg =    $_POST[$this->cfg_fieldnames['message']];
        $this->doSave = $this->cfg_allowances['save'] && $_POST[$this->cfg_fieldnames['save']]=='1';
        $this->action = $_POST[$this->cfg_fieldnames['action']];
        
        $this->forgetAction=pinReg::i()->trans->t($this->cfg_forgetactionname);
        $this->loginAction=pinReg::i()->trans->t($this->cfg_loginactionname);
        
	}
	/**
	* @see IpinAuthFront
	* @note this implementation saves to session and if allowed cookie. Only saves pw if loggedin. 
	*/
    public function saveData($loggedIn=false,$hashedPw=""){
	
		   if($loggedIn){
    	      	$_SESSION[$this->cfg_fieldnames['password']] = $this->isPasswordHashed() ? $this->pw : $hashedPw;
    	      	if($_SESSION[$this->cfg_fieldnames['authorized']]!==true){
					// state change so refresh 
					session_regenerate_id();
				}
    	      	$_SESSION[$this->cfg_fieldnames['authorized']] = true;
           }
           else{
			  	unset($_SESSION[$this->cfg_fieldnames['password']]);
		   		$_SESSION[$this->cfg_fieldnames['authorized']] = false;
		   }

           $_SESSION[$this->cfg_fieldnames['user']] = $this->user;
           $_SESSION[$this->cfg_fieldnames['message']] = $this->msg;
           
           if($this->doSave){
            	$endtime = time() + ($this->cfg_dayskeepcookie * self::SECONDS_PER_DAY);
            	setcookie($this->cfg_fieldnames['user'], $this->user, $endtime);
            	if($loggedIn){
            		setcookie($this->cfg_fieldnames['password'],$this->isPasswordHashed() ? $this->pw : $hashedPw , $endtime);
            	}	
        	}
	}
	/**
	* @see IpinAuthFront
	* @note this implementation erases session and cookie 
	*/
	public function eraseData(){
		if($_COOKIE[$this->cfg_fieldnames['user']]){
            setcookie($this->cfg_fieldnames['user'], '', time() - 1);
            setcookie($this->cfg_fieldnames['password'],'',time() - 1);
        }
        
        $_SESSION = array();

		if (ini_get("session.use_cookies")) {
    		$params = session_get_cookie_params();
    		setcookie(session_name(), '', time() - 42000,$params["path"], $params["domain"],$params["secure"], $params["httponly"] );
		}

		session_destroy();
	}
}
?>