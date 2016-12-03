<?php
require_once(PIN_INCLUDE.'pig.php')
/**
* Inclusion of css in the html itself (not a link!)
* 
* @config (in pinInclusion!) string cssdir directory  
* @config (in pinInclusion!) string cssext extension 
* @todo Consider moving config settings to pinCSSInclusion
* 
* @package Pinyon.Incl
* @see <a href="http://www.pinyonpine.nl">Pinyon Pine</a>
* @author <a href="http://www.tigrez.nl">Tigrez Software / Luc Geritz</a>
*/
class pinCSSInclusion extends pinInclusion{

    /// simulate constructor returning instance
    public static function make($key,$hook=''){
        $incl=new self($key,$hook);
        return $incl;    
    }

    private $dir;
    private $ext;
    
    protected function setType(){
        $this->type='css';
    }
    
    /**
    * Add text using a file.
    * Do not specify extension and path, config settings cssdir and cssext are used
    * 
    * @param string $name name of file
    * @return pinCSSInclusion instance itself
    */
    public function fromFile($name){
        $this->fileName=$this->dir.$name.$this->ext;
        return $this;    
    }
    
	/**
	* 
	*/    
    protected function processContent($content){
        // remove redundant spaces
        $content=pig::stripRed($content,' ');
        $content=str_replace("\t" , "", $content);
        // remove linefeeds
        $content=str_replace(array("\r\n", "\r", "\n"), "", $content);
        
        // wrap tags
        $content='<style type="text/css">'.$content.'</style>';
        return $content;
    } 
    
    public function __construct($key,$hook=''){
        
        parent::__construct($key,$hook);
        
        $this->dir=pinConfig::thisClass(pinConfig::USE_TOPCLASS)->cssdir;
        $this->ext=pinConfig::thisClass(pinConfig::USE_TOPCLASS)->cssext;
                
    }
}
?>
