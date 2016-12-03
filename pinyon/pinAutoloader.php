<?php
require_once(PIN_INCLUDE.'pinBasalError.php');
require_once(PIN_INCLUDE.'pinError.php');

/**
* Facilitate autoloading
* @note requires php5.3 or up
* 
* @package Pinyon.Misc 
* @see <a href="http://www.pinyonpine.nl">Pinyon Pine</a>
* @author <a href="http://www.tigrez.nl">Tigrez Software / Luc Geritz</a>
*/
class pinAutoloader{

     const DEPFILE = 'dep.php';
     private static $dirs = array(PIN_INCLUDE);
     private static $mods = array();
     private static $throwException = true;
     
     /**
     * Throws exception
     * Tries to use pinError which will log the message as well
     * 
     * @param string $msg
     */     
     private static function raise($msg){
         if(class_exists('pinError')){
            pinError::raise($msg);                 
         }
         else{
            throw new Exception($msg);         
         }
     }

	 public static function throwExceptionWhenNotFound($throw){
	 	self::$throwException = $throw;
	 		 	
	 }     
	 
     /**
	 * get dir based on module name
	 * 
	 * @param string $name module name
	 * @return string directory name of module
	 */
     public static function getModuleDir($name){
         return PIN_MODULEDIR.str_replace('.','/',$name).'/';    
     }
     /**
	 * get module names loaded
	 * 
	 * @return array string module names
	 */
     public static function getModules(){
         return self::$mods;
     }
     /**
	 * get dirs 
	 * 
	 * @return array string dirs
	 */
     public static function getDirs(){
         return self::$dirs;
     }

	 /**
	 * is a module loaded
	 * 
	 * @param string $mod name of module
	 * @return true if loaded
	 */
     public static function isModuleLoaded($mod){
         return in_array($mod,self::$mods);
     }
          
     /**
     * specify directories to check for classes
     * @note index 0 should contain most likely directory etc.
     * @note overwrites any previously added dirs!
     * 
     * @param array $dirs array of string each element a directory to check
     */
     public static function loadAutoloadDirs(array $dirs){
         self::$dirs=$dirs;
     }
     
     /**
     * Add a single dir to the autoload dirs
     * 
     * @param string $dir
     */
     public static function addDir($dir){
         if(!in_array($dir,self::$dirs)){
             self::$dirs[]=$dir;
         }    
     }
     /**
     * Add multiple dirs to the autoload dirs
     * 
     * @param array $dirs
     */
     public static function addDirs($dirs){
          foreach($dirs as $dir){
              self::addDir($dir);
          }    
     }
     
     /**
     * Add a Pinyon 'module'
     * A module translates to a folder in the PIN_MODULEDIR folder
     * Use dots for subfolders e.g. module 'dbase.extra' translates to dbase/extra/ folder
     * Dependend modules will be loaded as well, same module will not be loaded twice 
     * @throws 'Module xxx could not be loaded' if not found
     * 
     * @param string $name name of module
     */
     public static function loadModule($name){
         if(!in_array($name,self::$mods)){
             self::$mods[]=$name;
             $name=self::getModuleDir($name);
             self::$dirs[]=$name;
             if(file_exists($name.self::DEPFILE)){
                 self::LoadModules(include($name.self::DEPFILE));
             }
             else{
                 if(!file_exists($name)){
                     self::raise(pinBasalError::E002.$name);
                 }
             }
         }
     }
     /**
     * load modules from array
     * 
     * @param array $modarr array of (string)names of modules to load
     * @return void
     */
     public static function loadModules($modarr){
         foreach($modarr as $module){
             self::loadModule($module);
             
         }
     }
     
     /**
     * Try to load a class, checks directories as set with loadAutoloadDirs
     * @note throwing an exception can be surpressed by pinAutoloader::throwExceptionWhenNotFound(false)
     * @throw  'class xxx could not be loaded'
     *  
     * @param string $class name of class
     * @return void
     */
     public static function Autoload($class){
        if(!self::_autoload($class)){
        	if(self::$throwException) self::raise(pinBasalError::E001.$class);
        }     
     } 
     /**
     * Load a class if it exists, do *not* raise exception if not 
     * Use this if your own code handles non-existance of class. It loads the class if found so do not use it to just check existance without the intention of using it.
     * 
     * @param string $class
     * @return boolean true if loaded
     */     
     public static function LoadIfExists($class){
         return self::_autoload($class);
     }
     
     private static function _autoload($class){
         $i=0;
         do{
            $filename=self::$dirs[$i].$class.'.php';
            $absfilename=dirname($_SERVER['SCRIPT_FILENAME']).'/'.$filename;
            if(file_exists($absfilename)){
                include($absfilename);
                $i=PHP_INT_MAX;
            }
            else{
                $i++;
            }
         }
         while($i<count(self::$dirs));
         
         return !($i<PHP_INT_MAX); 
                  
     }
            
}
?>
