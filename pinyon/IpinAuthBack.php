<?php
/**
* Interface for class that gives access to Auth data (users, passwords etc) to your application, the auth "backside"
* Methods concerning the loaded user should check if a user is indeed already loaded. getError() should return msg if not.
* Methods which can return an error should set getError-result to "" if no error occurs. 
* @package Pinyon.Route
* @see <a href="http://www.pinyonpine.com">Pinyon Pine</a>
* @author <a href="http://www.tigrez.nl">Tigrez Software/ Luc Geritz</a>
*/
Interface IpinAuthBack{

     /**
     * Change a password for the *loaded* user
     * 
     * @param string $pwold the old password
     * @param string $pwnew1 the new password
     * @param string $pwnew2 confirmation of new password
     * @return boolean; true if succeeded, false if not, in which case getError returns error message 
     */
    public function changePw($pwold,$pwnew1,$pwnew2);

    /**
    * Force a new password to be set for the *loaded* user
    * 
    * @param string $pwnew1
    * @return boolean true if succeeded, false if not in which case getError returns error message
    */
    public function forceNewPw($pwnew1);

    /**
    * Retrieve text of last error empty string if none   
    * Guideline: empty message once it is read
    * @return string message
    */
    public function getError(); 
    
    /**
	* Check a password of the *loaded* user
	* 
	* @param string $pw
	* @param boolean $pwishashed
	* 
	* @return boolean true means password ok
	*/
    public function checkPw($pw,$pwishashed=false);

    /**
	* Check validity of user and if exists load it.
	* If user exists load it! Very different from doesUserExist
	* If user is blocked it should still load
	* 
	* @param string $user user (name) to be checked
	* @return boolean, true means valid
	*/
    public function loadUser($user);
    
    /**
	* Check if *some* user exists (either blocked or unblocked)
	* Does *not* load the user, see also loadUser
	* 
	* @param string $user user name to check existance of
	* @return boolean, true exists
	*/
    public function doesUserExist($user);
    
    /**
	* Check if a user is loaded
	* If user is loaded it might still be blocked!
	* 
	* @return boolean, true is a user is loaded
	*/
    public function isUserLoaded();

    /**
	* Check if a user is loaded which is not blocked
	* 
	* @note Still doesn't mean the user is logged in!
	* @return boolean, true if user is loaded which is not blocked, false if no user or a blocked user is loaded
	*/
	public function isNonBlockedUserLoaded();
	
    /**
	* Check if *some* or *loaded* user is blocked
	* Should ony be called if it exists. If it does not exist should return 'true' (is blocked) 
	* 
	* @param string  $user default null meaning check *loaded* user
	* @return boolean true means blocked
	*/
    public function isUserBlocked($user=null);
 
    /**
	* Block the *loaded* user
	* 
	* @return boolean true if succeeded, false should fill errormsg
	*/
    public function blockUser();

    /**
	* unBlock the *loaded* user
	* 
	* @return boolean true if succeeded, false should fill errormsg
	*/
    public function unblockUser();
    
    /**
	* Get admin status of *some* or *loaded* user
	* 
	* @param string $name user name default null means *loaded* user
	* @return true user is admin
	*/
    public function isAdmin($name=null);
    
    /**
	* load another user
	* 
	* @param string $name user name
	* 
	* @return boolean; true if user exists (can still be blocked!)
	*/
    public function setUser($name);

    /**
	* get *loaded* user's name
	* 
	* @return string name
	*/
    public function getName($name=null);
    
    /**
	* get *some* or *loaded* user's email
	* 
	* @param string $name user name default null means *loaded* user
	* @return string email
	*/
    public function getEmail($name=null);
    
    /**
	* get *loaded* user's id
	* 
	* @return mixed the id
	*/
    public function getId();
        
    /**
	* Retrieve a textual description of the rules for user name
	* Typical use would be in a form where user defines/changes user name
	* 
	* @return string description
	*/
    public function getNameRules();
    
    /**
	* Retrieve a textual description of the rules for password
	* Typical use would be in a form where user defines/changes password
	* 
	* @return string description
	*/
    public function getPwRules();
    
    /**
	* retrieve hashed password of *loaded* user
	* 
	* @return string
	*/
    public function getHashedPw();
    
    /**
	* get a random password
	* Make sure it's according your own pw rules!
	* 
	* @return string the password
	*/
    public function getNewPw(); 
     
}
?>
