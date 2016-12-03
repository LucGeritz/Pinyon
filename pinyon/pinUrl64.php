<?php
/**
* Compress current url to base64 format
*
* @package Pinyon.Route
* @see <a href="http://www.pinyonpine.com">Pinyon Pine</a>
* @author <a href="http://www.tigrez.nl">Tigrez Software/ Luc Geritz</a>
*/
class pinUrl64
{
    /**
    * Compress url (base64)
    *  
    * @param array $addpar additional parameters to add to current ones $paramname=>$paramvalue
    * @param array $exlpar
    * @return string compressed url (no xxx= prefix but just the value)
    */
    static function compress($addpar=array(),$exlpar=array()){
 
        $app=pinReg::i()->app;    
        $url=$app->self;
        
        $query=array_merge($app->urlquery,$addpar);
        if(count($query)>0){
            $i=0;
            $url.='?';
            foreach($query as $key=>$val){
                if(!array_key_exists($key,$exlpar)){
                    $url.= ($i==0 ? '': '&')  .$key .'='.$val  ;
                }
                $i++;
            }
        }
        $url=base64_encode($url);
        return $url;
 
    }
    
    /**
    * Decompress url from base64 format
    * Does not add functionality, just to balance the compress :)
    * 
    * @param string $url the compressed url
    * @return string url
    */
    static function decompress($url){
        $res=base64_decode($url,true);
        if($res) $url=$res;
        return $url;
    }
    
}
?>