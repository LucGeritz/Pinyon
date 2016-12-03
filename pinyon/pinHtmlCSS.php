<?php
/**
* Class representing a css link
* 
* @package Pinyon.Html
* @see <a href="http://www.pinyonpine.com">Pinyon Pine</a>
* @author <a href="http://www.tigrez.nl">Tigrez Software/ Luc Geritz</a>
* */
class pinHtmlCSS extends pinHtml{
    
    /**
    * @param string $file
    * @param string $media default 'all'
    * 
    * @return new instance of pinHtmlCSS
    */    
    public static function make($file,$media='all'){
       $html=new self($file,$media);
       return $html;
    }
    
    /**
    * @param string $file
    * @param string $media default 'all'
    */
    public function __construct($file,$media='all'){
        parent::__construct('link');
        $this->rel('stylesheet');
        $this->type('text/css');
        $this->href($file);
        $this->media($media);
    }
}