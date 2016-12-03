<?php
/**
* Class to facilitate including of text by key, hook or type. 
* singleton, access through i()
* 
* @package Pinyon.Incl
* @see <a href="http://www.pinyonpine.com">Pinyon Pine</a>
* @author <a href="http://www.tigrez.nl">Tigrez Software/ Luc Geritz</a>
*/
class pinIncluder{
    
    /* static */
    
    private static $instance;
    
    /**
    * Get instance of pinIncluder
    * 
    * @return pinIncluder
    */
    public static function i(){
        if(self::$instance==null){
            self::$instance=new pinIncluder();
        }    
        return self::$instance;
    }
    
    /* instance */
    
    private $types;
    private $keys;
    private $hooks;
    
    private function __construct(){
        $this->types=array();
        $this->keys=array();
        $this->hooks=array();
    }
    
    /**
    * Add an inclusion to the includer
    * 
    * @note if the list contains already an inclusion with same key then it is overwritten
    * @param pinInclusion $incl inclusion to add to the includer
    * @return pinIncluder itself
    */
    public function add(pinInclusion $incl){
        
        $key=$incl->getKey();
     
        if(!$key){
            $this->keys[]='';
           end($this->keys);
            $key=key($this->keys);
        }

        $_type=$incl->getType();
        $this->types[$_type][$key]=$incl;            
        $this->keys[$key]=$incl;
        
       $hook=$incl->getHook();
       if($hook){
            $this->hooks[$hook][$key]=$incl;      
       }
       return $this;       
    }
    
    /// get content of inclusions by type
    /// @parm $_type string get all inclusions of this type
    /// @return string concatenated content of all inclusions of this type, empty if no inclusions with given type exist 
    public function getIncludeByType($_type){
       
        $ret='';
        
        if(array_key_exists($_type,$this->types)){
            foreach($this->types[$_type] as $incl){
                $ret .= $incl->getContent();        
            }
        }
        return $ret;
    }
    
    /// get content of an inclusion by key
    /// @parm $key string
    /// @return string content of inclusion with key, empty if key does not exist
    public function getIncludeByKey($key){
        $ret='';
        if(array_key_exists($key,$this->keys)){
            $ret=$this->keys[$key]->getContent();
        }
        return $ret;
    }

    /// get content of an inclusion by hook to which include is assigned
    /// @parm $hook string
    /// @return string concatinated content of inclusions with given hook, empty if hook does not exist
    public function getIncludeByHook($hook){
        $ret='';
        
        if(array_key_exists($hook,$this->hooks)){
            foreach($this->hooks[$hook] as $incl){
                $ret .= $incl->getContent();        
            }
        }
        return $ret;
    }
    
}
?>