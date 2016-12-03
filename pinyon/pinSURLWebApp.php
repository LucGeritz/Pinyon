<?php
/**
* pinWebApp extension with support for semantic URLs.
* Uses the onStart pseudo-event
* @package Pinyon.Route
* @since 0.1.4
*/
class pinSURLWebApp extends pinWebApp
{
	private function updateGet($route,$action,$unnamed){
		
		$get = $this->getGet();
		
		if(!empty($route)){
			if(isset($get['r'])){
				 pinLog::i()->log("URL param 'r' ignored when using semantic URLs");
			}			
			$get['r']=$route; 
			if(isset($action)) $get['r'].='/'.$action;
		}
		
		if(!empty($unnamed)){
			 if(isset($get['unnamed'])){
				 pinLog::i()->log("URL param 'unnamed' ignored when using semantic URLs");
			 }
			 $get['unnamed']=$unnamed;
		}
		
		$this-> setGet($get);
	
	}
	
    protected function onStart(){

        pinLog::i()->log('(');

		$this->settings['semanticurls']=true;
		        
        $path = pig::getElement($_SERVER,array('PATH_INFO','ORIG_PATH_INFO'),'');
        
        $parts = explode( '/' , $path);
        
        $parts = array_values(array_filter($parts)); // remove empty and renumber

		$route='';
		$action='';
		
		$unnamed=array();
		
		for($i=0; $i < sizeof($parts); $i++ ){
			switch($i){
				case 0:
				$route=$parts[$i];
				break;
				case 1:
				$action=$parts[$i];
				break;
				default:
				$unnamed[]=$parts[$i];
			}			
		}
		
		$this->updateGet($route, $action, $unnamed);			

    }
    
    protected function onEnd(){
        parent::onEnd();
        pinLog::i()->log(')');
    }
    
	public function __construct(){

		parent::__construct();

	}
}
