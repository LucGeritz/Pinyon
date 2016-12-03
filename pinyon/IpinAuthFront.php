<?php
/**
* Interface for class which allows access to user provided authorization data
* 
* @package Pinyon.Route
* @see <a href="http://www.pinyonpine.com">Pinyon Pine</a>
* @author <a href="http://www.tigrez.nl">Tigrez Software/ Luc Geritz</a>
*/
interface IpinAuthFront{
    /**
	* return name of forget 'action' 
	* @note likely candidate to use for value of 'forget' submit button in login form 
	* 
	* @return string name for forget action
	*/
    public function getForgetActionName();
	
	/**
	* return name of login 'action' 
	* @note likely candidate to use for value of 'login' submit button in login form 
	* 
	* @return string name for login action
	*/
	public function getLoginActionName();
	/**
	* return message for a cred error that can be displayed by login view
	* 
	* @return string
	*/
	public function getMsgForCredError();
	/**
	* return message for a cred error that can be displayed by login view
	* 
	* @return string
	*/
	public function getMsgForAdminReqError();
	
	/**
	* return all userprovision fieldnames 
	* @return array fieldname=>real_fieldname, fieldnames are 'password', 'user', 'save' and 'message'
	*/

    public function getFieldnames();
    
    /**
	* return all or one specific allowance
	* 
	* @param string $specific name of a single allowance, pinAuthRouter knows 'forget' and 'save'
	* @return array allow=>boolean (true means allowed) or boolean if $specific is filled. 
	*/
	public function getAllowances($specific='');
	
    /**
    * Return user name
    * 
    * @return string user name
    */
    public function getUser();
    
    /**
    * Return user action
    * 
    * @return string user action
    */
    public function getUserAction();
    
    /**
    * Return password
    * 
    * @return string password
    */
    public function getPassword();
    
    /**
	* load the data the user provided
	* @note should be called before any of the other methods
	* 
	* @return void
	*/
    public function loadData();

    /**
	* erase the user provided data
	* 
	* @return void
	*/
	public function eraseData();
	/**
	* save the user provide data
	* 
	* @return
	*/
	public function saveData();
}
?>