<?php
/**
* Base class for a Pinyon controller which owns a view
* Main difference with pinBaseController (its ancestor) is a constructor which creates an instance of the corresponding view (pinBaseView) and a reference to it through $this->view
* 
* @package Pinyon.Route
* @see <a href="http://www.pinyonpine.nl">Pinyon Pine</a>
* @author <a href="http://www.tigrez.nl">Tigrez Software / Luc Geritz</a>
*/
abstract class pinBaseViewController extends pinBaseController
{
    protected $view=null; // the viewobject

    protected function prefix(){
		return false;
	}
	
    /**
	* Does this controller have a associated view?
	* 
	* @return boolean true if controller has corresponding view
	*/
    public function hasView(){
        return true;
    }


	/**
	* Is a view loaded 
	* 
	* @return boolean true if loaded
	*/
    public function viewIsLoaded(){
		return $this->view!==null;
	}    
	/**
	* Get content created by this controller's view
	* 
	* @return string content
	*/
    public function getContent(){
        return $this->view->getContent();    
    }    
	
    /**
	* return name of viewdir    
	* @note override if you want a different viewdir associated with this controller
	* 
	* @return string the dir which contains the view for this controller
	*/
    protected function getViewDir(){
        return $this->app->viewdir;   
    }
    
    /**
    * return the name of the view class for this controller
    * @note override if you want a different viewclass associated with this controller (or maybe a 1:n controller-view relation)
    * 
    * @return string name of the view class
    */
    protected function getViewClassName(){
        return $this->app->routename.$this->app->viewsuffix;
    }    
    
    /**
	* Constructor
	* @param String $action the action with which route is called
	* 
	* @return
	*/
    public function __construct($action=''){

		parent::__construct($action);
		        
        $this->view=null;
        
        $viewclassname=$this->getViewClassName();
        $viewdir=$this->getViewDir();
        
        // pinRouter will take care of missing views!
        if(pinAutoloader::loadIfExists($viewclassname)){
			$this->view=new $viewclassname;	
		} 
    }
}
?>
