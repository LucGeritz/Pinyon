<?php
/**
* Base class for a Pinyon controller
* 
* @package Pinyon.Route
* @see <a href="http://www.pinyonpine.nl">Pinyon Pine</a>
* @author <a href="http://www.tigrez.nl">Tigrez Software / Luc Geritz</a>
*/
abstract class pinBaseController extends pinConfigurable 
{
	
	protected $app;
	protected $action;

	// no prefix
    protected function prefix(){
		return false;
	}

    /**
	* pseudo-event called when controller has been processed by pinWebApp
	* 
	* @return void
	*/
    public function onEnd($action=''){

    }    	
	
	/**
	* pseudo-event called before controller start() or startAction()
	* 
	* @return boolean true means continue by calling start(xxx)
	*/
    public function onBegin($action=''){
		return true;
    }    	

    /**
	* pseudo-event called when controller has been processed by pinWebApp
	* 
	* @return boolean true means continue
	*/
    public function onStart(){
		return true;
    }    	
	
	
    /**
    * Return content build up by the controller.
    * This basecontroller returns an empty string (no content)
    *
    * @return string content created by this controller
    */
    public function getContent(){
        return '';
    }

    /**
    * Return whether this controller has an associated view.
    * The idea is that descendant classes (pinBaseViewController!) override this function
    * This basecontroller returns false
    *  
    * @return boolean true if this controller has a view associated
    */
    public function hasView(){
        return false;
    }

    /**
    * Start the controller
    * Is called if no action is specified. Otherwise startXXX is called, XXX being the name of the action. 
    * 
    * @param array $params mixed array allows for a generic way of passing parameters to the controller. pinWebApp will pass the $_GET this way (as $params['get'])
    * @return void
    */
    public abstract function start($params=array());

    /**
    * Return whether user needs admin rights for this controller
    * This value is ignored if needsLogin is false!
    *
    * @param string $action name of action requested
    * @return boolean true if user needs admin rights to use this controller, otherwise false
    */
    public function needsAdmin($action=''){
        return false;
    }

    /**
    * Return whether user needs to login for this controller
    * 
    * @param string $action name of action requested
    * @return boolean true if user needs to login to use this controller, otherwise false
    */
    public function needsLogin($action=''){
        return false;
    }

    /**
    * Return whether this controller can only run in debug mode 
    * Debug mode means PIN_DEBUG==true
    * 
    * @return boolean true if route only allowed in debug
    */
    public function debugOnly(){
        return false;
    }
    
    public function __construct($action){
		
		parent::__construct();
		
		$this->action=$action;
		
		// easy reference to app
        $this->app=pinReg::i()->app;
        
	}
}
?>
