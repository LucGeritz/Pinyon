<?php
/**
* Temporary File Manager
* @package Pinyon.Misc 
* @see <a href="http://www.pinyonpine.com">Pinyon Pine</a>
* @author <a href="http://www.tigrez.nl">Tigrez Software/ Luc Geritz</a>
*/
class pinTmpFileList{
    
    private static $files=array();
   
    /// add file to the list
    /// @param $file string file name to add to list
    /// @return void
    public static function add($file){
        self::$files[]=$file;    
    }
        
    /// remove file from the list
    /// @note removal from list, not file system, is meant here!   
    /// @param $file string file name to remove from list
    /// @return void 
    public static function remove($file){
        if(in_array($file,self::$files)){
            unset(self::$files[$file]);    
        }    
    }
    
    /// Remove files from file system 
    /// @note 'clean' is reserved name in php
    /// @return void
    public static function sanitate(){
        for($i=0;$i<count(self::$files);$i++){
             if(!@unlink(self::$files[$i])){
                 pinLog::i()->log(__CLASS__.':'.__FUNCTION__.' Cannot delete tempfile: '.error_get_last());
             }           
        }
    }
    
   
}

?>