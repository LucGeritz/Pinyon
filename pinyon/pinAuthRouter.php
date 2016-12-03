<?php
/**
* Class that manages the authorization aspect of a route request
* @note pseudoEvents onBeforeAuth and onAfterAuth are not part of the IpinAuthRouter interface. Should they be?
* 
* @config loginroute string: name of route in case of login needed
* @config forgetroute string: name of route in case of 'i forgat my password'
* @config riskroute string: name of route in case a risk is detected
* @config authback IpinAuthBack: instance or name of class to access persistant auth info
* @config authfront IpinAuthFront: instance or name of class to access user provided auth info
* @config dologging bool: true means logging is on
* 
* @package Pinyon.Route
* @see <a href="http://www.pinyonpine.nl">Pinyon Pine</a>
* @author <a href="http://www.tigrez.nl">Tigrez Software / Luc Geritz</a>
*/
class pinAuthRouter extends pinConfigurable implements IpinAuthRouter
{
    private $routename;
    private $loggedIn;

    /**
    * log a message if logger is injected
    * 
    * @param string $msg text to log
    * @return void
    */
    private function log($msg)
    {
        if($this->_dologging)
        {
            pinLog::i()->log($msg);
        }
    }
    
    /**
	* save data 
	* 
	* @param boolean $loggedIn is user logged in?
	* @return void
	*/
    private function saveData($loggedIn)
    {
    	$this->_authfront->saveData($loggedIn,$loggedIn ? $this->_authback->GetHashedPw() : '' );    
    }

    /**
	* load data
	* 
	* @return void
	*/
    private function loadData()
    {
		$this->_authfront->loadData();
    }

	// set by class config
	protected $_loginroute='login';
	protected $_forgetroute= 'forget';
	protected $_riskroute='risk';
	protected $_authback;
	protected $_authfront='pinAuthFront';
	protected $_dologging=true;
	
    /**
    * called just before authorization starts
    * @note meant to be overridden
    * 
    * @return boolean true continue with auth, false: stop and call riskroute
    */
    protected function onBeforeAuth(){
        return true;
    }
    /**
    * called after authorization pine, routename known
    * @note meant to be overridden
    *  
    * @return void
    */
    protected function onAfterAuth(){
    }
    
    /**
	* @see IpinAuthRouter
	*/
    public function getAuthBack()
    {
        return $this->_authback;
    }
    
	/**
	* @see IpinAuthRouter
	*/
    public function getAuthFront()
    {
        return $this->_authfront;
    }

 	/**
 	* @see IpinAuthRouter
    */
    public function isLoggedInAdmin()
    {
        return $this->loggedIn && $this->_authback->isAdmin();
    }

 	/**
 	* @see IpinAuthRouter
    */
    public function isLoggedIn()
    {
        return $this->loggedIn;
    }

 	/**
 	* @see IpinAuthRouter
    */
    public function logOut()
    {
        $this->log('logOut called by '.$this->_authfront->getUser());

        $this->loggedIn = false;

        $this->routename = '';
        
		$this->_authfront->eraseData();
    }
    /**
 	* @see IpinAuthRouter
    */
    public function getRouteNameForLogin(){
		return $this->_loginroute;	
	}
	/**
 	* @see IpinAuthRouter
    */
    public function getRouteNameForForget(){
		return $this->_forgetroute;
	}
	/**
 	* @see IpinAuthRouter
    */
    function getRouteNameForRisk(){
		return $this->_riskroute;
	}

    /**
 	* @see IpinAuthRouter
    */
    public function getRouteName($needsAdmin)
    {

        $this->loggedIn = false;

        $trans = pinReg::i()->trans;

        $this->loadData();

        $this->routename = $this->_loginroute;

        $reason='authorized';
        $allow=$this->_authfront->getAllowances();

        $this->log('User found: '.$this->_authfront->getUser());
    	$this->log('Action: '.$this->_authfront->getUserAction());

        if($this->onBeforeAuth()){
        
             // must always have user
            if($this->_authfront->getUser()){

	            if($this->_authback->loadUser($this->_authfront->getUser()) && !$this->_authback->isUserBlocked()){

	                $this->log('User is valid'); // and not blocked

        	        // possible now: forget (only needs valid user)
            	    if($this->_authfront->getUserAction() == $this->_authfront->getForgetActionName()){
                    	// request for new password
                    	if($allow['forget']===true){ // @@@@@@ allowances ook via provision
                        	$this->routename = $this->_forgetroute;
                        	$reason="forgat-pw requested";
                        }
                        else{
                             $this->routename= $this->_riskroute;
                             $reason="forgat-pw requested though not allowed by config";
                        }
                    }
                    else{
	                    if($this->_authback->checkPw($this->_authfront->getPassword(),$this->_authfront->isPasswordHashed())){   
    	                    // password ok
        	                $this->loggedIn=true;
            	            $this->routename = '';
                	        $this->log('Password is valid');
                    	}
                    	else
                    	{
                        	// passw was invalid, generalize to creds - error
                        	$this->log('Creds invalid: '.$trans->t($this->_authback->GetError()));
                        	$this->authdata['msg'] = $this->_authfront->getMsgForCredError();
                        	$reason="Password wrong";
                    	}
                	}
            	}
            	else{
                	$this->log('Creds invalid: '.$trans->t($this->_authback->GetError()));
                	$this->authdata['msg'] = $this->_authfront->getMsgForCredError();
                	$reason="User name illegal or user blocked";
            	}

            	if($this->routename == ''){
                
                	// seems ok but do we need admin
                	if($needsAdmin){
                    	$this->log('Route requires admin rights');
                    	if(!$this->_authback->isAdmin()){
                        	$this->authdata['msg'] = $this->_authfront->getMsgForAdminReqError();
                        	$reason='User is no admin';
                        	$this->routename = $this->_loginroute;
                    	}
                    	else{
                        	$this->log('Ok, user is admin');
                    	}
                	}
            	}
        	}
            else{
            	// no user found\
            	$this->authdata['msg'] = '';
            	$reason='No user found or user empty';
            	$this->routename = $this->_loginroute;
        	}
        }
        else{
            $route='Suggested by onBeforeAuth';
            $this->routename=$this->_riskroute;
        }
        
        $this->saveData($this->loggedIn);
        
        $this->log('Suggested route for auth: '.(!$this->routename ? 'none' : $this->routename)." (Reason: $reason)");
		
        $this->onAfterAuth();
        
        return $this->routename;
    }

    public function __construct()
    {
        // get class config
        parent::__construct('_'); // put _ prefix before variables
        
        $this->_authback=$this->toObject($this->_authback,'IpinAuthBack');
        $this->_authfront=$this->toObject($this->_authfront,'IpinAuthFront');
    }
}
?>