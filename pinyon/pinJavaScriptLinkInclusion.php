<?php
/**
* Represents an inclusion of a javascript link
* @note type of inclusion defaults to jslink
* @config (class) jsdir string path to prefix to file
* @config (class) jsext string extension to append to file
* 
* @package Pinyon.Incl
* @see <a href="http://www.pinyonpine.nl">Pinyon Pine</a>
* @author <a href="http://www.tigrez.nl">Tigrez Software / Luc Geritz</a>
*/
class pinJavaScriptLinkInclusion extends pinInclusion{

    private $attr=array();
       
    public static function make($key,$hook=''){
        $incl=new pinJavaScriptLinkInclusion($key,$hook);
       return $incl;    
    }
  
    protected function setType(){
        $this->incltype='jslink';
    }
    
    protected function processContent($content){
        
        $dir=pinConfig::thisClass(pinConfig::USE_TOPCLASS)->jsdir;
        $ext=pinConfig::thisClass(pinConfig::USE_TOPCLASS)->jsext;
        
        $prefix='<script type="text/javascript" src="'.$dir.$content.$ext.'" ';
        
        foreach($this->attr as $key=>$val){
            $prefix.=$key.'="'.$val.'" ';    
        }
        $prefix.='>';
        
        return $prefix.'</script>';
    } 
    /**
    * More descriptive alias for setContent 
    * 
    * @param string $name
    * @return pinJavaScriptLinkInclusion itself
    */
    public function file($name){
        $this->setContent($name);
        return $this;
    }
    /**
    * Add extra attributes to script tag 
    * 
    * @param array $attr
    * @return pinJavaScriptLinkInclusion itself 
    */
    public function extraAttr(array $attr){
        $this->attr=$attr;
        return $this;    
    }
    
    public function __construct($key,$hook=''){
        
        parent::__construct($key,$hook);
                
    }
}
?>
