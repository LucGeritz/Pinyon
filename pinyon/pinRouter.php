<?php
/**
* Class to determine the controller for a given route
* @note static!
* @config (class) defaultroute string: route(name) in case no route is specified
* @config (class) closedroute string: route(name) in case site is closed
* @config (class) notfoundroute string: route(name) in case of an error, for now this is only in case of route not found  
* @config (class) controllerdir string: where are the controller classes stored
* @config (class) viewdir string: where are the view classes stored
* @config (class) controllersuffix string: what is added to a route name to resolve the controller class
* @config (class) viewsuffix: string: what is added to a route name to resolve the view class
* @config (class) ipclosedexemption string: ip which is allowed to continue in case of closed 
* @package Pinyon.Route
* @see <a href="http://www.pinyonpine.com">Pinyon Pine</a>
* @author <a href="http://www.tigrez.nl">Tigrez Software/ Luc Geritz</a>
*/
class pinRouter{
	
	
    const aCTIONmETHODpREFIX='start';
    
    private static function instantiateController($classname,$routename,$action){
        pinReg::i()->app->routename=$routename;
		// create controller
        return new $classname($action);
    }
    
    private static function splitRouteName(&$route,&$action){
        list($route,$action)=explode('/',$route);
        if($action) $action = ucfirst(strtolower($action));
    }
    
    private static function getErrorClassAndRouteName(&$classname,&$routename,&$action){
        $routename=pinConfig::thisClass()->notfoundroute;
        
        self::splitRouteName($routename,$action,$bc);
                
	    $classname=$routename.pinConfig::thisClass()->controllersuffix;
		
    }
    
    /**
    * Get a controller based on route name
    * May return the errorcontroller if controller or action not found 
    * @param string $routename
    * @param string $action default empty
    * 
    * @return pinBaseController instance of controller
    */
	private static function getControllerForThisRoute($routename,&$action=''){
		
		$classname=self::getClassName($routename);
		
		if(!pinAutoloader::LoadIfExists($classname)){
            self::getErrorClassAndRouteName($classname,$routename,$action);
		}
		
		$controller=self::instantiateController($classname,$routename,$action);
        
        if(($action && !method_exists($controller,self::aCTIONmETHODpREFIX.$action)) 
           || ($controller->hasView() && !$controller->viewIsLoaded())
           ){
            // action not implemented
            unset($controller);
            self::getErrorClassAndRouteName($classname,$routename,$action);
            $controller=self::instantiateController($classname,$routename,$action);
        }
        
		return $controller;
	}
	
	private static function getClassName($routename){
		return $routename.pinConfig::thisClass()->controllersuffix;
	}

	/**
	* determine based on settings if auth is turned off
	* @note auth is only turned off if useauth=false && authisoff = true, otherwise always turned on! (The idea is to make it hard to turn of auth by accident)
	* 
 	* @return boolean true means auth is on
	*/
    public static function useAuth(){
		$useauth=pinConfig::thisClass()->useauth;
		$authisoff=pinConfig::thisClass()->authisoff;
		return !($useauth===false && $authisoff===true);
	}	
	/// get a controller instance based on a routename
	/// @param $routename string  the route (e.g. as read from $_GET)
    /// @return pinBaseController (or descendant) instance of controller implementing this route
	public static function getController(&$routename,&$action=''){
        
    
		$controller=null;
		
		$app=pinReg::i()->app;
		
        // closed or default..
		if($app->isclosed){
			if(!$app->ipclosedexemption || ($app->ipclosedexemption!=$app->ip)){
				$routename=$app->closedroute;	
			}
			else{
				if (!$routename) $routename=pinConfig::thisClass()->defaultroute;
			}
		}
		else{
			if (!$routename) $routename=pinConfig::thisClass()->defaultroute;
		}
		
		$controller=self::getControllerForThisRoute($routename,$action);

	
        // check if only allowed in debugmode
        if($controller->debugOnly() && !PIN_DEBUG){
            pinError::raise(pinBasalError::E004);
        }
        
        // always run authorization (if authorization is used) to set loggedin, user etc..
        // .. even if controller is not restricted
        if(self::useAuth()){
            $altroutename=pinReg::i()->auth->getRouteName($controller->needsAdmin());
            self::splitRouteName($altroutename,$altaction);
	
        	// only now we decide if we use the suggested alternative route
			switch($altroutename){
				case pinReg::i()->auth->getRouteNameForLogin():
					if($controller->needsLogin($action)){
		   				$controller=self::getControllerForThisRoute($altroutename,$altaction);
           				$routename=$altroutename;
           				$action=$altaction;
					}
				break;
				case pinReg::i()->auth->getRouteNameForForget():
				case pinReg::i()->auth->getRouteNameForRisk():
					$controller=self::getControllerForThisRoute($altroutename,$altaction);
           			$routename=$altroutename;
           			$action=$altaction;
				}
        }
		return $controller;
	}
}
?>