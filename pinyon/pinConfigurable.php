<?php
/**
* Base class for a class configurable by a config file
* 
* @package Pinyon.Config 
* @see <a href="http://www.pinyonpine.nl">Pinyon Pine</a>
* @author <a href="http://www.tigrez.nl">Tigrez Software / Luc Geritz</a>
*/
abstract class pinConfigurable 
{

    /**
    * Helper function to make object of configuration value
    * -If value is an object it is returned unchanged
    * -If value is a string it is assumed to be a class name and an instance is created and returned
    * -If value is array then the 1st member is assumed to be a class name, the second the parameter for the constructor. An instane is created and returned. Only allows for one parameter!
    * 
    * @throws Exception 'xxx is not an instance of yyy' if a type ($instcheck) is specified and resulting instance is not of this type
    * @param mixed $in string, array or object, the config value 
    * @param type $instcheck, type (class or interface). If not null the instance is checked before returning. If not the right type an exception is raised 
    * 
    * @return
    */    
    protected function toObject($in,$instcheck=null){
        $out=null;
        if(is_object($in)){
            $out=$in;
        }    
        else{
            // array must be [0] class [1] param (only 1!)
            if(is_array($in)){
                $out= new $in[0]($in[1]);
            }
            else{
                $out=new $in;
            }
        }
        
        if($instcheck && !($out instanceof $instcheck)){
            pinError::raise(get_class($out).pinBasalError::E003.$instcheck);
        }
        
        return $out;
    }
    
    /**
    * Return prefix attached to instance variables
    *  
    * @return string prefix
    */
    protected function prefix(){
        return false;
    }
    
    /**
    * Promote members of array to instance variables
    *
    * @param string $caller class name of class calling
    * @param array $arr mixed array to promote
    * @param string $prefix prefixed to variable names
    * 
    * @return
    */
    protected function promotoToVars($caller,$arr,$prefix){
        
        $pf=$caller::prefix();
        if($pf) $prefix = $pf;
        
        foreach($arr as $key=>$val){
            $var=$prefix.$key;
            $this->$var=$val;            
        }
    } 
    
    /**
    * @note Construction parameter $prefix is deprecated. Override prefix method instead
    * @param string $prefix the string prefixed to the settings when converted to variables, default none 
    */
    public function __construct($prefix=''){
        $caller=get_called_class();
        $parents=class_parents($caller);  // get all baseclasses for calling class
        unset($parents['pinConfigurable']); // remove pinConfigurable itself
        $parents[$caller]=$caller;          // add direct caller itself        
        
        foreach($parents as $key=>$val){
             $sets=pinConfig::someClass($val)->getSettings();
             if($sets) $this->promotoToVars($val,$sets,$prefix);                
        }

    }    

}

?>
