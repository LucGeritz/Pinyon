<?php
/**
* Class to do url associated stuff
* Singleton, access through i()
* 
* @since 0.1.4
* @package Pinyon.Route
* @see <a href="http://www.pinyonpine.com">Pinyon Pine</a>
* @author <a href="http://www.tigrez.nl">Tigrez Software/ Luc Geritz</a>
*/
class pinUrl{

	private static $instance=null;
	
	public static function i(){
		
		if(self::$instance===null){
			self::$instance=new pinUrl();
			
		}
		
		return self::$instance;
	}	
	
	/* instance */
	private $root;
	
	private function __construct(){
		
		$parsed_url = parse_url(pig::fullUrl());
		
		$scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : ''; 
    	$host     = isset($parsed_url['host']) ? $parsed_url['host'] : ''; 
    	$port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : ''; 
    	$user     = isset($parsed_url['user']) ? $parsed_url['user'] : ''; 
    	$pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass']  : ''; 
    	$pass     = ($user || $pass) ? "$pass@" : ''; 
    	$path     = isset($parsed_url['path']) ? $parsed_url['path'] : ''; 

  		if($path){
	  		$folders = explode('/',$_SERVER['SCRIPT_NAME']);
			$folders = array_filter($folders,function($val){
					return $val !== '' && $val !=='index.php';
			});
			$folders = implode('/',$folders);
    	}
    	$this->root = "$scheme$user$pass$host$port/$folders/"; 
					
	}
	
	/**
	* compose an url based on a given pinyon route
	* @param mixed $route name of the route. tradition url use string (e.g. 'test' or with action 'test/delete'), semantic url use array where [0] is route and [1] is optional action
	* @param array $urlQuery array of paramName=>paramValue
	* @param array $unnamedParams array of unnamed parameters (e.g. array('id',1200') ) which become url elements
	* 
	* @return string url
	*/	
	public function getRouteBasedUrl($route, $urlQuery=array(), $unnamedParams=array()){

		$semUrl = is_array($route);
			
		$url = $this->root;
		
		if($semUrl) {
			
			if(isset($route[0])) $url.=$route[0];
		    if(isset($route[1])) $url.='/'.$route[1];

			if($unnamedParams){
				for($i=0; $i<sizeof($unnamedParams); $i++){
					$url.='/'.$unnamedParams[$i];
				}
			}
		}
		else{
			// traditional url
			$url .= '?r='.$route;			
		}
		
		
		if($urlQuery){
			$url.= $semUrl ? '?' : '&';	
			
			foreach($urlQuery as $key=>$val){
				$url.="$key=$val&";
			}			
			
			$url = trim($url, '&');

		}
		
		return $url; 
		
	}
	
	public function redirectRoute($route, $urlQuery=array(), $unnamedParams=array()){
		
		$url = $this->getRouteBasedUrl($route, $urlQuery, $unnamedParams);
		$this->redirect($url);
			
	}
	
	public function redirect($url){
		
		header("Location: ".$url);
    	die();
			
	}
	
	/**
	* Get the root url
	* 
	* @return string root url
	*/
	public function getRoot(){
	
		return $this->root;	
	
	}
	/**
	* set (override!) the root url
	* You'll only want to do this if the built-in algorithm fails, do it as early as possible (in pinyon.php)
	* 
	* @param string $root
	*/	
	public function setRoot($root){
		
		$this->root = $root;
		
	}

}