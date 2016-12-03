<?php
/**
* Base class for a Pinyon request
* 
* @package Pinyon.Route
* @see <a href="http://www.pinyonpine.nl">Pinyon Pine</a>
* @author <a href="http://www.tigrez.nl">Tigrez Software / Luc Geritz</a>
*/
abstract class pinBaseRequest 
{
    /// called by pinWebReq in case of error (detected by pinWebReq)
    /// implementation of request knows best how to return error (being it json, xml, plain text etc.)
    /// @return string with error message in format expected by caller (the webpage)
    public abstract function returnError($msg);
    
    /// @return string with result in format expected by caller (the webpage)
    public abstract function getResult($payload); // string
    
    /// Inquire if userneeds admin rights for the controller
    /// @mindyou pinWebApp will ignore this value is needsLogin is false! Seems the logical approach.
    /// @return boolean true if user needs admin-rights to use this controller, otherwise false
    public abstract function needsAdmin();

    /// Inquire if user needs to be logged in for use of this controller
    /// @return boolean true if user needs to be logged in to use this controller
    public abstract function needsLogin();

    /// Inquire if request needs an trans object in registry
    /// defaults to false (not needed) override if you want i18n initialized 
    /// @return boolean true if needed otherwise not needed
    public function needsI18N(){
        return false;
    }
    
    /**
    * force use of a method (good idea!) override to force this
    * 
    * @return string empty means either get or post. other values are 'POST' or 'GET'
    */
    public function forceMethod(){
        return '';
    }
}
?>
