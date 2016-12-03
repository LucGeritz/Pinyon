<?php
/**
* Represents container for html radio buttons
* 
* @package Pinyon.Html
* @see <a href="http://www.pinyonpine.com">Pinyon Pine</a>
* @author <a href="http://www.tigrez.nl">Tigrez Software/ Luc Geritz</a>
*/
class pinHtmlRadioButtonsContainer extends pinHtml{
   
   /**
   * @param string $tag
   * @return a new instance of pinHtmlRadioButtonsContainer
   */
   public static function make($tag){
       $html=new self($tag);
       return $html;
   }
   
   /// create instance of pinHtml
   /// @param $tag string tag
   public function __construct($tag){
       parent::__construct($tag);
   }
   
   /**
   * Add a set of buttons to the container
   * Does not overwrite previous added buttons 
   *
   * @param array $buttonValues prompt=>value
   * @param string $name all buttons get this name so they form a set
   * @param string $selValue button with this value will be preselected
   * @return pinHtmlRadioButtonsContainer itself
   */ 
   public function addButtons(array $buttonValues, $name, $selValue=''){
       foreach($buttonValues as $key=>$value){

           $this->addChild(pinHtml::make('label'),pinHtml::ReturnChild)->addChild($key); 
           
           $but=pinHtml::make('input')->type('radio')->value($value)->name($name);
           if($value===$selValue){
               $but->selected('selected');
           }
           $this->addChild($but);
       } 
       return $this;
   }
   
}
