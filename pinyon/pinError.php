<?php
require_once(PIN_INCLUDE.'pig.php');
/**
* Error Class
* 
* @package Pinyon.Error
* @see <a href="http://www.pinyonpine.com">Pinyon Pine</a>
* @author <a href="http://www.tigrez.nl">Tigrez Software/ Luc Geritz</a>
*/
class pinError{
    
    private static function maybelog($msg,$fatal=false){
		if(class_exists('pinLog')){
			pinLog::i()->log($msg,$fatal);
		}
	}
	
    private static function prefixMsg($msg,$file,$class,$function,$line){
        return '['.$file .':'.$line.']'.$class .'.'.$function. ': '.$msg;
    }

    /**
    * Callback called on shutdown 
    * @note Catches any unexpected error and tries to log them. Must be registred first like so: register_shutdown_function('pinError::onShutDown');
    * 
    * @return void
    */
    public static function onShutDown(){
      
      $err = error_get_last();
      
      if(defined('PIN_DUMPERROR')) print_r($err);
      
      if (($err['type'] & 101) || $err['type']==256) {   // dec101 = bin1100101 and are the fatal errors we're interested in, see http://www.php.net/manual/en/errorfunc.constants.php
           echo pig::inTag('strong',"An unexpected error occurred");
           $prefixedMsg=self::prefixMsg($err['message'],$err['file'],'','',$err['line']);
           self::maybelog($prefixedMsg,true);
      } 
    }

    /**
    * Catch uncaught exceptions
    * @note Prints simple message to user (unexpected error) and logs real message text and call stack. If PIN_DEBUG is true it will print call stack as well
    * 
    * @param Exception $exception the exception that occured
    * @return void
    */
    public static function exceptionHandler($exception){
        
        echo pig::inTag('strong',PIN_DEBUG ? $exception->getMessage() : "An unexpected error occurred");
        
        self::maybelog($exception->getMessage(),true);

        $traces=$exception->getTrace();
        $traces=array_merge(array(array('file'=>$exception->getFile(),'line'=>$exception->getLine())),$traces);
        $elips='.';
        
        foreach($traces as $level){
            $msg=$elips.'file: '.pig::inColor($level['file'],'red');
            if(isset($level['class'])) $msg.= ', class: '.$level['class'];
            if(isset($level['function'])) $msg.= ', function: '.$level['function'];
            $msg.=', line: '.$level['line'];
            self::maybelog(strip_tags($msg));
            if(PIN_DEBUG){
                echo '<br/>'.pig::inTag('tt',$msg);
            }
            $elips.='.';
        }
        if(PIN_DEBUG){
           echo '<br/>The backtrace above is shown because the setting PIN_DEBUG is TRUE.';
        }    
                    
    }   
     
    /**
    * Log a message
    * 
    * @param string $msg the message to log
    * @return string message as logged with prefix
    */
    public static function log($msg){
    
        $bt=debug_backtrace(false); // optimize from 5.4 on!
        $msg=self::prefixMsg($msg,$bt[1]['file'],$bt[1]['class'],$bt[1]['function'],$bt[1]['line']);
  
        self::maybelog($msg,true);
        
        return $msg;    
    }
    
    /**
    * Log a message and then throw exception
    * 
    * @param string $msg message to log, also exception message
    * @return void (will not return :)
    */
    public static function raise($msg){ // throw is reserved word
        
        $bt=debug_backtrace(false); // optimize from 5.4 on!
        $msg=self::prefixMsg($msg,$bt[1]['file'],$bt[1]['class'],$bt[1]['function'],$bt[1]['line']);
        
        self::maybelog($msg,true);
  
        throw new Exception($msg);
    
    }
}
?>