<?php
/**
* Represents an html structure
*  Supports creating html from your code
* 
* @package Pinyon.Html
* @see <a href="http://www.pinyonpine.com">Pinyon Pine</a>
* @author <a href="http://www.tigrez.nl">Tigrez Software / Luc Geritz</a>
*/
class pinHtml
{
   protected $children;
   protected $attributes;
   protected $tag;
   protected $id='';

   // for addChild parameter
   const ReturnChild=true;   
   const ReturnParent=false; // default
   
   // for render parameter
   const InnerHtml=true;
   const AllHtml=false; // is default

	private function insertAt(array $arr, $at, $newElem){
		array_splice($arr , $at, 0, $newElem);
		return $arr;
	}
   
   private function closeTag($opentag){
       $ret='';
       // some 'void' elements can't have close tag
       if(!in_array( strtolower($opentag),array('input','img','base','br','col','command','hr', 'link', 'meta','param','source'))){
           $ret.='</'.$opentag.'>';
       }
       return $ret;
   }
   
	/**
	* get the id of outer tag 
	* @since 0.1.4
	* 
	* @return string id or '' if none
	*/   
   public function getId(){
   		return $this->id;	
   }
   
   /**
   * Find element with given @id
   * Returned element can be main element or one of its children, or null if not found
   * With id is meant the html id attribute
   * @since 0.1.4
   * 
   * @param string $idfind the id to find
   * 
   * @return pinHtml or null if not found
   */
   public function getById($idfind){
 		
   		if($idfind==$this->id){
			return $this;
		}	
		else{
			foreach($this->children as $child){
				if(is_a($child, 'pinHtml')){
					$childWithId = $child->getById($idfind);
					if($childWithId) return $childWithId;							
				}
			}
		}
		return null;
   }
   
   
   /**
   * create anonymous instance of pinHtml
   * @since 0.1
   * 
   * @return new instance of pinHtml
   */
   public static function make($tag=''){
       $html=new self($tag);
       return $html;
   }
   
   /**
   * @since 0.1
   */
   public function __construct($tag){
       $this->tag=$tag;
       $this->children=array();
       $this->attributes=array();    
   }
   
   /**
   * Render the html-element
   * @param boolean $innerHtml if true only inner html is returned, default false
   * 
   * @return string the rendered html
   */
   public function render($innerHtml=false){
       
       if(!$innerHtml){
           $ret='<'.$this->tag.(count($this->attributes)>0 ? ' ' : '');
           foreach($this->attributes as $key=>$value){
               if($value===null){
                   $ret.="$key ";    
               }
               else{
                   $ret.=$key.'="';
                   $ret.=$value.'" ';
               }
           }
           $ret.='>';       
       }
       
       foreach($this->children as $child){
           if(is_a($child,__CLASS__)){
               $ret.=$child->render();            
           }
           else{
               $ret.=$child;
           }
       }
       
       if(!$innerHtml) $ret.= $this->closeTag($this->tag);
       
       return $ret;    
   }   
   
   /**
   * Add a child html element
   * 
   * @param mixed $child either pinHtml; a valid html structure or string; a text
   * @param boolean $returnChild if true a reference to the new child is returned if false (default) a reference to the pinHtml itself
   * 
   * @return mixed, string or pinHtml depending on type of child, also see $returnChild parameter
   */
   public function addChild($child,$returnChild=false)
   {
       $this->children[]=$child;
       return $returnChild ? $child : $this; // allows for fluent interfaces    
   }
   /**
   * Add child to begin
   * @since 0.1.4
   * 
   * @param mixed $child either pinHtml; a valid html structure or string; a text
   * @param boolean $returnChild if true a reference to the new child is returned if false (default) a reference to the pinHtml itself
   * 
   * @return mixed, string or pinHtml depending on type of child, also see $returnChild parameter
   */
   public function prependChild($child, $returnChild = false){
   		array_unshift($this->children, $child);
   		return $returnChild ? $child : $this;
   }
   /**
   * Add child at given index
   * Element which resides at given index and higher are pushed up
   * @since 0.1.4
   * 
   * @param mixed $child either pinHtml; a valid html structure or string; a text
   * @param boolean $returnChild if true a reference to the new child is returned if false (default) a reference to the pinHtml itself
   * 
   * @return mixed, string or pinHtml depending on type of child, also see $returnChild parameter
   */
   public function insertChildAt($child, $at, $returnChild = false){
   		$this->children = $this->insertAt($this->children , $at, $child);
		return $returnChild ? $child : $this;   	
   }
   
   
   /**
   * Comfort function to add a Br
   * 
   * @return pinHtml itself
   */
   public function addBr()
   {
       $this->children[]='<br/>';
       return $this;
   }
   
   /**
   * Add an attribute 
   * @param string $name name of attribute
   * @param array $args value of attributes, null (default) denotes a valueless attribute like 'selected'
   * 
   * @return pinHtml itself
   */
   public function __call($name,$args=array(null)){
       $this->attributes[$name]=$args[0];
       if($name=='id'){
			$this->id = $args[0];	   	
	   }    
       return $this;
   }
   
   /**
   * Add an attribute
   * @note attributes can be added with the __call magic method. e.g. $obj->xxx("yyy") adds attribute xxx with value yyy. addAttr is an escape for attributes which are not valid as attribute name (like http-equiv) or happen to have a name which is also a function name. Or maybe you dislike magic methods..
   * @param string $name name of attribute
   * @param string $val value of attribute, null (default) denotes a valueless attribute like 'selected'
   * 
   * @return pinHtml itself
   */
   public function addAttr($name,$val=null){
       return $this->__call($name,array($val));
   }
   
   /**
   * Add multiple attributes
   * @param array $attrlist array of (string)name=>(string)value
   * 
   * @return pinHtml itself
   */
   public function addAttrList($attrlist){
       foreach($attrlist as $key=>$val){
           $this->__call($key,array($val));
       } 
       return $this;
   }
   
   
}
?>