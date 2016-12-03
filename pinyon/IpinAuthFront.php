<?php
/**
* Interface for class which allows access to user provided authorization data
* @since 0.1
* 
* @package Pinyon.Route
* @see <a href="http://www.pinyonpine.nl">Pinyon Pine</a>
* @author <a href="http://www.tigrez.nl">Tigrez Software/ Luc Geritz</a>
*/
interface IpinAuthFront{
    /**
	* return name of forget 'action'
	* @since 0.1
	* @note likely candidate to use for value of 'forget' submit button in login form 
	* 
	* @return string name for forget action
	*/
    public function getForgetActionName();
	
	/**
	* return name of login 'action' 
	* @since 0.1
  	* @note likely candidate to use for value of 'login' submit button in login form 
	* 
	* @return string name for login action
	*/
	public function getLoginActionName();
	/**
	* return message for a cred error that can be displayed by login view
	* @since 0.1
	* 
	* @return string
	*/
	public function getMsgForCredError();
	/**
	* return message for a cred error that can be displayed by login view
	* @note do not confuse with getMessage which retrieves the message that actually is set
	* @since 0.1
	*  
	* @return string
	*/
	public function getMsgForAdminReqError();
	
	/**
	* return all userprovision fieldnames 
	* @note do not confuse with getMessage which retrieves the message that actually is set 
	* @return array fieldname=>real_fieldname, fieldnames are 'password', 'user', 'save' and 'message'
	*/

    public function getFieldnames();
    
    /**
	* return all or one specific allowance
	* @since 0.1
	*  
	* @param string $specific name of a single allowance, pinAuthRouter knows 'forget' and 'save'
	* @return array allow=>boolean (true means allowed) or boolean if $specific is filled. 
	*/
	public function getAllowances($specific='');
	
    /**
    * Return user name
    * @since 0.1
    *  
    * @return string user name
    */
    public function getUser();
    
    /**
    * Return user action
    * @since 0.1
    *  
    * @return string user action
    */
    public function getUserAction();
    
    /**
    * Return password
    * @since 0.1
    *  
    * @return string password
    */
    public function getPassword();

	/**
	* Get message (from $_POST)
	* @since 0.1.3
	* 
	* @return string the message
	*/    
    public function getMessage();
    /**
	* Set message (in $_POST)
	* 
	* @since 0.1.3
	*/
    public function setMessage($msg);
    
    /**
	* load the data the user provided
	* @since 0.1
	* @note should be called before any of the other methods
	* 
	* @return void
	*/
    public function loadData();

    /**
	* erase the user provided data
	* @since 0.1
	* 
	* @return void
	*/
	public function eraseData();
	/**
	* save the user provided data
	* @since 0.1
	*  
	* @return void
	*/
	public function saveData();
}
?>