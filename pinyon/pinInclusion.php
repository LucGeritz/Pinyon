<?php
/**
* Represents text to include
*
* @package Pinyon.Incl
* @see <a href="http://www.pinyonpine.com">Pinyon Pine</a>
* @author <a href="http://www.tigrez.nl">Tigrez Software/ Luc Geritz</a>
*/
class pinInclusion
{

    /// simulate constructor returning instance
    /**
    * @param string $key
    * @param string $hook
    * @return new instance of pinInclusion
    */
    public static function make($key,$hook=''){
        $incl=new self($key,$hook);
        return $incl;    
    }

    const noKey=false;
        
    protected $fileName;
    protected $incltype;
    protected $key;
    protected $hook;
    protected $content;
    protected $fileContent;
    protected $isLoaded;
    protected $dir;
    protected $ext;
    
    public function __construct($key,$hook=''){
        $this->isLoaded=false;
        $this->setType();
        $this->key=$key;
        $this->hook=$hook;
    }
        
    /**
    * Set default type of inclusion
    * In pinInclusion it is set to 'text'
    * Override this in subclass if you want it to be of a different class
    * 
    * @return pinInclusion itself
    */    
    protected function setType(){
        // override to set your type
        $this->incltype='text';
        return $this;
    }
    
    /**
    * Override the default type
    * 
    * @param string $type
    * @return pinInclusion itself
    */
    public function overrideType($type){
        $this->incltype=$type;
        return $this;
    }
    
    /**
    * Process the content
    * This method is called by getContent and is the place to process your content.
    * In pinInclusion the content is returned unchanged
    * Override in your subclass to give your content the processing it needs
    * 
    * @param string $content
    * @return string processed content
    */
    protected function processContent($content){
        return $content;
    } 
    
    /**
    * Set the content of the inclusion
    * 
    * @param string $content
    * @return pinInclusion itself
    */
    public function setContent($content){
        $this->content=$content;
        $this->fileName='';
        return $this;    
    }
    
    /**
    * Get the content
    * The content returned is processes by processContent()
    * 
    * @return string processed content
    */
    public function getContent(){
        if($this->fileName && !$this->isLoaded){
                $this->content=file_get_contents($this->fileName);
                $this->isLoaded=true;
        }
        return $this->processContent($this->content);
    }
    
    /**
    * Set the content by specifying a filename
    * The content of the file will become the content
    *
    * @param string $name name of file that contains the content
    * @return pinInclusion itself
    */
    public function fromFile($name){
        if(!$this->dir) $this->dir=pinConfig::thisClass()->incdir;
        if(!$this->ext) $this->dir=pinConfig::thisClass()->incext;
        $this->fileName=$dir.$name.$ext;
        return $this;
    }
        
    /**
    * Return the type
    * 
    * @return string type
    */
    public function getType(){
        return $this->incltype;    
    }
    
    /**
    * Set the key of this content
    * A unique identifier.
    *  
    * @param string $key
    * @return pinInclusion itself
    */
    public function setKey($key){
        $this->key=$key;
        return $this;
    }
    
    /**
    * Return the key
    * 
    * @return string the key
    */
    public function getKey(){
        return $this->key;    
    }
    
    /**
    * Set the hook
    * 
    * @param string $hook
    * @return pinInclusion itself
    */
    public function setHook($hook){
        $this->hook=$hook;
       return $this;
    }
    
    /**
    * Return the hook
    * 
    * @return string the hook
    */
    public function getHook(){
        return $this->hook;    
    }
    
}
?>