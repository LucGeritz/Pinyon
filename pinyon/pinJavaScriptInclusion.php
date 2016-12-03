<?php
/**
* Represents an inclusion of a javascript
* @config (class) jsext string extension to append to file 
* @config (class) jsdir string path to prefix to file
* 
* @package Pinyon.Incl
* @see <a href="http://www.pinyonpine.com">Pinyon Pine</a>
* @author <a href="http://www.tigrez.nl">Tigrez Software/ Luc Geritz</a>
*/
class pinJavaScriptInclusion extends pinInclusion{
    
    public static function make($key,$hook=''){
        $incl=new pinJavaScriptInclusion($key,$hook);
        return $incl;    
    }
   
    protected function setType(){
        $this->incltype='js';
    }

    protected function processContent($content){
        $content='<script type="text/javascript">'.$content.'</script>';
        return $content;
    } 
    
    public function __construct($key,$hook=''){
        
        parent::__construct($key,$hook);
        $this->ext=pinConfig::thisClass(pinConfig::USE_TOPCLASS)->jsext;
    }
}
?>
