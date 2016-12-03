<?php
require_once(PIN_INCLUDE.'pinConfigurable.php');
/**
* Class to implement internationalization (i18n)
* Supports single line messages, texts and templates
* @config (class) defaultlang string this is the language used if the requested language is not found, usually a two letter code
* @config (class) fileprefix string this is prefixed to every file
* @config (class) keylen int total length of key
* @config (class) filepartlen int length of key which is translated to key
* @config (class) extension string extension used
* @config (class) returnkey boolean default true means if t method finds no message the key is returned, false means return empty string
* @package Pinyon.i18n
* @see <a href="http://www.pinyonpine.com">Pinyon Pine</a>
* @author <a href="http://www.tigrez.nl">Tigrez Software/ Luc Geritz</a>
*/
class pinTrans extends pinConfigurable implements IpinTranslator{

    protected $msgcache=array();
    protected $language;
    
    protected $cfg_defaultlang;
    protected $cfg_fileprefix;
    protected $cfg_keylen;
    protected $cfg_filepartlen;
    protected $cfg_extension;
    protected $cfg_returnkey=true;
    /**
    * Derive file name where given key should be found
    * 
    * @param string $key
    * @return tring file name
    */    
    protected function deriveFileNameFromKey($key){
        return $this->cfg_fileprefix . substr($key,0,$this->cfg_filepartlen) . '_' . $this->language . $this->cfg_extension ;
        
    }
    
    /**
    * Get filename which should contain messages
    * 
    * @param string $key
    * @return mixed filename (checked for existance) or false if no file found
    */
    protected function getMsgFile($key){
        
        $fileorg=$this->deriveFileNameFromKey($key);
        
        // is in cache?
        if (array_key_exists($fileorg,$this->msgcache)){
           $file=$this->msgcache[$fileorg];             
        }
        else{
            if(!file_exists($fileorg)){
                if($this->cfg_defaultlang){
                    $file=$this->cfg_fileprefix . substr($key,0,$this->cfg_filepartlen) . '_' . $this->cfg_defaultlang . $this->cfg_extension ;
                    if(!file_exists($file)){
                        $file=false;
        		    }
        		}
            }
            else {
                $file=$fileorg;
            }    
    	}
        return $file;
    }
    
    /**
    * Get a 'block' of text from file
    * @todo if not found return empty string, is that sufficient?
    * 
    * @param string $file
    * @return string string content from file as one long string 
    */
    protected function getTextBlock($file){
        $text="";
        $orgfile=$file;
        
        $file=$this->cfg_fileprefix . $orgfile . "_" . $this->language . $this->cfg_extension;
        if(file_exists($file)){
            
        }
        else{
            $file=$this->cfg_fileprefix . $orgfile . "_" . $this->cfg_defaultlang . $this->cfg_extension;
            if($this->cfg_defaultlang!=$this->language && file_exists($file)){
            }
            else{
                $file=false;
            }
        }
        if($file){
            $text=file_get_contents($file);
        }
        return $text;
    }
    
    protected function prefix(){
        return 'cfg_';
    }
    
    /**
    * @param string $language the language to translate to, defaults to the default language as read from class config
    */
    public function __construct($language=''){
        
        parent::__construct();
        
        $this->language=$language=='' ? $this->cfg_defaultlang : $language ;
    }
    /**
    * Resolve variables in a text
    * 
    * @param string $text
    * @param array $vars array of key=>value 
    * @param string $bracket 2 byte string. Messagekey must be embedded between these
    * 
    * @return text with variables resolved
    */
    public function resolve($text,$vars=array(),$bracket='{}'){
        $retkey=$this->cfg_returnkey;
        
        // force t to return empty string if variable not found
        $this->cfg_returnkey = false;
        
        while($pos1=strpos($text,$bracket[0])){
            
            $pos2=strpos($text,$bracket[1]);
            if(!$pos2) $pos2=strlen($text)-1;
            $msgkey=substr($text,$pos1,($pos2-$pos1)+1);
            $translated='';
            
            $shortkey=substr($msgkey,1,-1);
          
            if(array_key_exists($shortkey,$vars)){
                  $translated=$vars[$shortkey];  
             }
           else {
             if(strlen($msgkey)>2){
                $translated=$this->t($shortkey);                
             }
             
            }            
            $text=str_replace($msgkey,$translated,$text);
        }
        
        $this->cfg_returnkey = $retkey;
        
        return $text;
        
    }
    
    /**
    * Resolve using a template
    * @note you cannot use parameters in messages in a template file
    * @param string $file file name, as is, in which messages will be translated
    * @param array $vars array of string, $key=>$value extra translations, have precedence, default empty array
    * @param string $bracket array of char, messagekey must be embedded between these, default '{}'
    * 
    * @return template as string with messages translated and variables resolved
    */
    public function template($file,$vars=array(),$bracket='{}'){
        
        $s=@file_get_contents($file);
        
        $s=$this->resolve($s,$vars,$bracket);
        
        return $s;
                
    }
        
    /**
    * Get content of whole messagefile
    * @note file is added to cache 
    * 
    * @param string $key
    * @return array string messages as key=>text
    */
    public function getMsgs($key){
        
        $msgarray=array();
        
        if($file=$this->getMsgFile($key)){    
            if(is_array($file)){
                $msgarray=$file;    
            }
            else{
                $msgarray=include($file);
                // add to cache
                $this->msgcache[$this->deriveFileNameFromKey($key)]=$msgarray;    
            }
        }
        
        return $msgarray;
    }
    /**
    * Get text from file depending on current language
    * File may contain messages and variables (see params $vars, $brackets)
    * If not found default language is used
    * 
    * @param string $file name of file, no prefix or language suffix
    * @param array $vars optional array of varname=>varvalue to resolve within text
    * @param string $brackets characters which make up the brackets to identify variables
    * @return string text from a language specific version of $file
    */
    public function text($file,$vars=array(),$brackets='{}'){
        $text = $this->getTextBlock($file);
        if(count($vars)>0){
            $text=$this->resolve($text,$vars,$brackets);
        }
        return $text;
    }
    /**
    * Translate a message
    * If key not found the original msg is returned
    * 
    * @param string $msg message, should start with key followed by 0 to n characters
    * @param array $params parameters to insert in message
    * @return string translated text
    */
    public function t($msg,$params=array()){
        
        $msgarray=array();
        
        if(strlen($msg)>=$this->cfg_keylen){
            $key=substr($msg,0,$this->cfg_keylen);
            if($file=$this->getMsgFile($key)){    
                if(is_array($file)){
                    $msgarray=$file;    
                }
                else{
                    $msgarray=include($file);
                    // add to cache
                    $this->msgcache[$this->deriveFileNameFromKey($key)]=$msgarray;    
                }
            }
            
            if(array_key_exists($key,$msgarray)){
                $msg=vsprintf($msgarray[$key],$params);
            }
            else{
				if(!$this->cfg_returnkey) $msg='';
			}
            
        }
        else{
			if(!$this->cfg_returnkey) $msg='';
		}
        
        return $msg;
    }
    
    public function getReturnKey(){
		return $this->cfg_returnkey;
	}
    /**
	* @param boolean $doReturnKey true t-method should return key itself if message not found, otherwise empty string
	* @return IpinTranslator instance itself
	*/
    public function setReturnKey($doReturnKey){
		$this->cfg_returnkey = $doReturnKey;
		return $this;
	}
    /**
    * Set the prefix for message files
    * 
    * @param string $pfx prefix (if a dirname make sure it ends with slash)
    * @return pinTrans itself
    */
    public function setFilePrefix($pfx){
        $this->cfg_fileprefix=$pfx;
        return $this;
    }
    
    /**
    * Set the suffix for message files
    * 
    * @param string $sfx suffix 
    * @return pinTrans itself
    */
    public function setFileSuffix($sfx){
        $this->cfg_extension=$sfx;
        return $this;
    }
    /**
    * Return fileprefix for message files
    * 
    * @return string file prefix
    */
    public function getFilePrefix(){
        return $this->cfg_fileprefix;
    }
    /**
    * Return file suffix for message files
    * 
    * @return string suffix
    */
    public function getFileSuffix(){
        return $this->cfg_extension;
    }
    /**
    * Set the default language
    * 
    * @param string $lang default language
    * @return pinTrans itself
    */
    public function setDefaultLanguage($lang){
        $this->cfg_defaultlang=$lang;
        return $this;    
    }
    
    /**
    * Return default language
    * 
    * @return string suffix
    */
    public function getDefaultLanguage(){
        return $this->cfg_defaultlang;    
    }
    /**
    * Set the requested language
    * 
    * @param string $lang
    * @return pinTrans itself
    */
    public function setLanguage($lang){
        $this->language=$lang;    
        return $this;
    }
}

?>