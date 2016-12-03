<?php
/**
* A Html structure for a select
* 
* @package Pinyon.Html
* @see <a href="http://www.pinyonpine.com">Pinyon Pine</a>
* @author <a href="http://www.tigrez.nl">Tigrez Software/ Luc Geritz</a>
*/
class pinHtmlSelect extends pinHtml{
    
   public static function make($options=array(),$selKey='',$attribs=array()){
       $html=new self($options,$selKey,$attribs);
       return $html;
   }

   public function __construct($options=array(),$selKey='',$attribs=array()){
        
        parent::__construct('select');
        
        $this->addAttrList($attribs);
        
        foreach($options as $k=>$v){
                $opt=pinHtml::make('option')->value($k);
                
                if($selKey && $k==$selKey){
                     $opt->selected();
                }
                
                $opt->addChild($v);
                 
                $this->addChild($opt);
            }
   }
}
