<?php
/**
* Base class for a Pinyon view
* Usage: In the render method you should fill $this->content with the content.
* 
* @package Pinyon.Route
* @see <a href="http://www.pinyonpine.nl">Pinyon Pine</a>
* @author <a href="http://www.tigrez.nl">Tigrez Software / Luc Geritz</a>
*/
abstract
class pinBaseView extends pinConfigurable
{
	protected $app;
    protected $content='';
	protected $description;
	
	/**
	* Get description of view embedded in meta-description tag. 
	* Typically you'd do that in the layout file. Example echo $controller->hasView() ? $controller->getView()->getDescription() : '';
	* the view should set description by $this->description 
	* @since 0.1.4
	* @return string if description filled a meta tag is returned, orherwise ''
	*/
	public function getDescription(){
		if(empty($this->description)) return '';
		return '<meta name="description" content="'.$this->description.'">';
	}
    /**
    * Render content
    * 
    * @param array $params mixed array allows for a generic way of passing parameters to the view by the associated controller
    * This function must populate $this->content
    * 
    * @return void
    */
    public abstract function render($params = array()); 

    /**
    * Helper function to convert array to instance variables
    * Example: array('ape'=>'gorilla') leads to $this->ape containing 'gorilla'
    * 
    * @param array $params mixed array of key=>value
    * 
    * @return void
    */
    protected function promoteToVar($params)
    {
        foreach($params as $key=>$val){
            $this->$key = $val;
        }
    }
    
    protected function prefix(){
        return false;    
    }
    
    public function __construct(){
        // load config
        parent::__construct();
	
		// easy reference to app
        $this->app=pinReg::i()->app;

    }
    
    /**
    * Return content of view
    * 
    * @return string the content of the view
    */
    public function getContent()
    {
    	
        return $this->content;
    }

}
?>
