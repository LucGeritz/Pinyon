<?php
/**
* Comfort class with several shortcuts for adding common used includes.
* Singleton, access with i()
* @note where a setting points to a file an array can be specified instead of a string. In this case the 1st element is the file loaded when PIN_DEBUG is true, the second is the one loaded when PIN_DEBUG is false. Allows for loading minified scripts on production sites.
* @config (class) dojolink string: complete path to dojo javascript 
* @config (class) jquerylink string: complete path to jquery javascript
* @config (class) jqueryuilink string: complete path to jqueryUi javascript
* @config (class) jqueryuicsslink string: complete path to jquery theme css file. You can use {theme} in path as variable 
* @config (class) jqueryuitheme string: theme for jquery ui, e.g.. cupertino, smoothness, swanky-purse 
* @config (class) jsincdir string: js include from which whole sources are includes 
* CSS location is specified in pinInclusion class config
* JS to use in link location is specified in pinInclusion class config
*
* @package Pinyon.Incl 
* @see <a href="http://www.pinyonpine.com">Pinyon Pine</a>
* @author <a href="http://www.tigrez.nl">Tigrez Software/ Luc Geritz</a>
*/
class pinEZInclude extends pinConfigurable{
    
    ################ static ################
    const InclJQueryUI=true;
    const InclJQueryUITheme=true;
    
    private static $instance;
    
    /// Get the instance of pinEZInclude
    public static function i(){
        if(self::$instance==null){
            self::$instance=new pinEZInclude();
        }    
        return self::$instance;
    }

    ################ instance ################
    
    private function getFileName($file){
        if(is_array($file)){
               $file=$file[PIN_DEBUG===true ? 0 : 1];
        }
        return $file;
    }
    
    // filled by class config    
    protected $cfg_jsincdir;
    protected $cfg_jquerylink;
    protected $cfg_jquerylinkmin;
    protected $cfg_jqueryuilink;
    protected $cfg_jqueryuilinkmin;
    protected $cfg_jquerytheme;
    protected $cfg_jqueryuicsslink;
    protected $cfg_dojolink;
    
    protected function prefix(){
        return 'cfg_';
    }
    
    // must be public 'cos parent construct is as well
    public function __construct(){

        parent::__construct();
      
        $this->cfg_jsincdir=$this->getFileName($this->cfg_jsincdir);
        $this->cfg_jquerylink=$this->getFileName($this->cfg_jquerylink);
        $this->cfg_jquerylinkmin=$this->getFileName($this->cfg_jquerylinkmin);
        $this->cfg_jqueryuilink=$this->getFileName($this->cfg_jqueryuilink);
        $this->cfg_jqueryuicsslink=$this->getFileName($this->cfg_jqueryuicsslink);
        $this->cfg_dojolink=$this->getFileName($this->cfg_dojolink);    
    
    }
    
    /**
    * Add a CSS link to the includer
    * Shortcut for pinCSSLinkInclusion::make($file)->file($file) and adding result to pinIncluder
    * Csslinkinclusions become always type 'csslink'
    * 
    * @param mixed $file string or array, see note at class description
    * @param string $hook where to hook to, default empty (not hooked) 
    * 
    * @return pinCSSLinkInclusion inclusion
    */
    public function CSS($file,$hook=''){
        
           $file=$this->getFileName($file);
           $incl=pinCSSLinkInclusion::make($file,$hook)->file($file);
           pinIncluder::i()->add($incl);
           return $incl;           
    }
    
    /**
    * Add a javascript link to the includer
    * shortcut for pinJavaScriptLinkInclusion::make($file)->file($file) and adding result to pinIncluder
    * jslinkinclusion become always type 'jslink'
    * Inclusion is added to pinIncluder
    *   
    * @param mixed $file string or array, see note at class description
    * @param string $hook where to hook to, default empty (not hooked)
    * 
    * @return pinJavaScriptLinkInclusion inclusion
    */
    public function JS($file,$hook=''){
           
           $file=$this->getFileName($file);
                     
           $incl=pinJavaScriptLinkInclusion::make($file,$hook)->file($file);
           pinIncluder::i()->add($incl);
           return $incl;           
    }
      
    /**
    * include a javascript from a file in include Directory
    * Inclusion is added to pinIncluder
    * 
    * @param mixed $file string or array, see note at class description
    * @param $hook string hook up to this hook
    * @param $vars array (default empty) var=>resolvedvalue
    */
   public function jsSource($file,$hook,$vars=array()){
       
       $file=$this->getFileName($filename);
       
       $content=@file_get_contents($this->cfg_jsincdir.$file.'js');
       if($content){
            $content=str_replace(array_keys($vars),array_values($vars),$content);
       }
       $in=pinJavaScriptInclusion::make($file,$hook)->setContent($content);
       pinIncluder::i()->add($in);
       return $in;
    }
    /**
    * include a link to dojo
    * Inclusion is added to pinIncluder
    * @note see class settings in class description!
    * 
    * @param string $hook is the hook to hang include on,, default empty string
    * @param boolean $async default true which means use Async Module Definition (AMD)
    * 
    * @return pinInclusion the inclusion
    */
    public function dojo($hook='',$async=true){
        
        $html=pinHtml::make('script')->type('text/javascript')->src($this->cfg_dojolink);
        $async and $html->addAttr( 'data-dojo-config','async: true');
        
        // cannot use jsscriptlink incl. for external links!
        $in=pinInclusion::make('dojo',$hook);
        $in->setContent($html->render());
        pinIncluder::i()->add($in);
    
        return $in;
    }
    /**
    * include a link to jquery, optional to jquery as well
    * Inclusion is added to pinIncluder
    * @note see class settings in class description!
    * 
    * @param string $hook is the hook to hang include on,, default empty string
    * @param boolean $addJqUi if true add a link to jqueryui as well, default false
    * @param boolean $addUiTheme if true add a theme, default false.
    * 
    * @return pinInclusion the inclusion
    */
    public function jquery($hook='',$addJqUi=false,$addUiTheme=false){
        
        $html=pinHtml::make('script')->type('text/javascript')->src($this->cfg_jquerylink);

        $in=pinInclusion::make('jq',$hook);
        $in->setContent($html->render());
        pinIncluder::i()->add($in);
        
        if($addJqUi){
   
            $html=pinHtml::make('script')->type('text/javascript')->src($this->cfg_jqueryuilink);

            $in=pinInclusion::make('jqui',$hook);
            $in->setContent($html->render());
            pinIncluder::i()->add($in);
            
            if($addUiTheme){
                
                $link=$this->cfg_jqueryuicsslink;
                $link=str_replace('{theme}',$this->cfg_jqueryuitheme,$link);
    
                $html=pinHtml::make('link')->rel('stylesheet')->href($link);
    
                $in=pinInclusion::make('jquicss',$hook)->overrideType('css');
                $in->setContent($html->render());
                
                pinIncluder::i()->add($in);
                                            
            }
            
        }   
        
        return $in;
             
    }
    
}
?>