<?php
/**
* Interface for a class that manages the authorization aspect of a route request
* 
* @package Pinyon.Route 
* @see <a href="http://www.pinyonpine.nl">Pinyon Pine</a>
* @author <a href="http://www.tigrez.nl">Tigrez Software / Luc Geritz</a>
*/
Interface IpinAuthRouter{

	/**
	* Return the class implementing the authorization backside 
	* 
	* @return IpinAuthBack object implementing the authorization backside
	*/
    function getAuthBack();

	/**
	* Return the class implementing the authorization frontside 
	* 
	* @return IpinAuthFront object implementing the authorization frontside
	*/
    function getAuthFront();
    
    /**
	* get **name** of the appropiate authorization controller 
	* @note only call if decided you are using authorization (check pinReg.App.useauth)
	* 
	* @param boolean $needsAdmin
	* @return string name of pinBaseController (or descendant) class needed for authorization, empty if none needed (logged in)
	*/
    function getRouteName($needsAdmin);
	
	/**
	* get the routename for a login
	* 
	* @return string
	*/
    function getRouteNameForLogin();
    /**
	* get the routename for a 'forgat my password' signal
	* 
	* @return string
	*/
    function getRouteNameForForget();
    /**
	* get the routename for a risk detected
	* 
	* @return string
	*/
    function getRouteNameForRisk();
    
    /**
	* Is user logged in as an admin
	* 
	* @return true if logged in as admin
	*/
    function isLoggedInAdmin();
    
    /**
	* Is the user logged in?
	* 
	* @return boolean true if logged in
	*/
    function isLoggedIn();
    
    /**
	* Log out user 
	* 
	* @return void
	*/
    function logOut();
}
?>
