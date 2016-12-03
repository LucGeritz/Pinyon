<?php
/**
* Represents an inclusion of a css link
* @note type of inclusion defaults to **csslink**
* @config cssdir string path to prefix to file
* @config cssext string extension to append to file
*
* @package Pinyon.Incl
* @see <a href="http://www.pinyonpine.nl">Pinyon Pine</a>
* @author <a href="http://www.tigrez.nl">Tigrez Software / Luc Geritz</a>
*/
class pinCSSLinkInclusion extends pinInclusion{

    private $attr=array();
       
    /// simulate constructor returning instance
    public static function make($key,$hook=''){
        $incl=new self($key,$hook);
        return $incl;    
    }
  
    protected function setType(){
        $this->incltype='csslink';
    }
    
    protected function processContent($content){
        
        $pubdir=pinConfig::thisClass(pinConfig::USE_TOPCLASS)->cssdir;
        if(!$this->ext) $this->ext=pinConfig::thisClass(pinConfig::USE_TOPCLASS)->cssext;
        
        $prefix='<link rel="stylesheet" type="text/css" href="'.$pubdir.$content.$this->ext.'" ';
        
        foreach($this->attr as $key=>$val){
            $prefix.=$key.'="'.$val.'" ';    
        }
        $prefix.='>';
        
        return $prefix.'</link>';
    } 
    
    // more descriptive alias for setContent 
    public function file($name){
        $this->setContent($name);
        return $this;
    }
    
    /// add extra attributes to script tag 
    public function extraAttr(array $attr){
        $this->attr=$attr;    
    }
    
    public function __construct($key,$hook=''){
        
        parent::__construct($key,$hook);
        // want the fromfiles to have extension css as well
        $this->ext=pinConfig::thisClass(pinConfig::USE_TOPCLASS)->cssext;        
    }
}
?>
