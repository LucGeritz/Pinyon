<?php
/**
* Contract for a markdown implementation
*
* @package Pinyon.Markdown 
* @see <a href="http://www.pinyonpine.nl">Pinyon Pine</a>
* @author <a href="http://www.tigrez.nl">Tigrez Software / Luc Geritz</a>
*/
Interface IpinMarkdown{
    
    /**
    * Parse a markdown text 
    * @param string $markdown markdown text
    * @return string html text
    */
    public function parse($markdown);
     
}
?>
