<?php
/**
* Class to facilitate a registry for global objects/variables
* Singleton, access through i()
* 
* @package Pinyon.Misc 
* @see <a href="http://www.pinyonpine.com">Pinyon Pine</a>
* @author <a href="http://www.tigrez.nl">Tigrez Software/ Luc Geritz</a>
*/
class pinReg{
    private static $instance;
    private $regArray;
    
    /// add an object to the registry
    /// @param $key string key of the object/variable
    /// @param $val mixed object or variable to add to the registry
    /// @mindyou php does not allow magic methods to be private
    public function __set($key,$val){
        $this->regArray[$key]=$val;
    }
    /// set an object/variable by its key
    /// @param $key string key of the object/variable
    /// @return the object/variable requested, null if key not found
    /// @mindyou php does not allow magic methods to be private
    public function __get($key){
        $res=null;
        if(array_key_exists($key,$this->regArray)){
            $res=$this->regArray[$key];
        }    
        return $res;
    }
    /// constructor
    private function __construct(){
        $this->regArray=array();
    }    
    /// Get the instance of pinReg
    /// @return pinReg instance
    public static function i(){
        if(self::$instance==null){
            self::$instance=new pinReg();
        }    
        return self::$instance;
    }
    
}

?>