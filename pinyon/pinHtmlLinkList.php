<?php
/**
* A html structure for links (a href).
* Basic construction is [div][ul][li][a/][/li][/ul]
* For additional attributes for Div, Header and ul extend this class and override attrForXXX methods
* 
* @package Pinyon.Html
* @see <a href="http://www.pinyonpine.com">Pinyon Pine</a>
* @author <a href="http://www.tigrez.nl">Tigrez Software/ Luc Geritz</a>
*/
class pinHtmlLinkList extends pinHtml
{

    protected function attrForDiv(){
        // -- override --!
        return array();
    }
    
    protected function attrForHead(){
        // -- override --!
        return array();
    }
    protected function attrForUL(){
        // -- override --!
        return array();
    }
    
    protected function attrForLI(){
        // -- override --!
        return array();
    }
    
    protected function attrForA(){
        // -- override --!
        return array();
    }

    /**
    * @param array $links array of text=>linkinfo. Linkinfo is array with keys htmlattr=>value
    * @param string $header
    */
    public function __construct($links,$header=''){
        
        parent::__construct('div');
        
        $this->addAttrList($this->attrForDiv());
                
        $ul=pinHtml::make('ul')->addAttrList($this->attrForUL());
        
        if($header){
            $ul->addChild(new pinHtml('li'),true)->addAttrList($this->attrForHead())->addChild($header);
        } 

        $this->addChild($ul);
        
        $liAttr=$this->attrForLI();
        $aAttr=$this->attrForA();
        
        foreach($links as $key=>$val){
        
            $li=$ul->addChild(new pinHtml('li'),true);
            
            $li->addAttrList($liAttr);
            
            $a=$li->addChild(new pinHtml('a'),true);
            
            $a->addAttrList($val);
            $a->addAttrList($this->attrForA());
            
            $a->addChild($key);
            
        }
        
    } 
    
      
}
?>