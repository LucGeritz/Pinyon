<?php
/**
* Interface for a translator class
*
* @package Pinyon.i18n
* @see <a href="http://www.pinyonpine.nl">Pinyon Pine</a>
* @author <a href="http://www.tigrez.nl">Tigrez Software / Luc Geritz</a>
*/
interface IpinTranslator{

    /**
    * @param string $language the language to translate to, defaults to the default language as read from class config
    */
    public function __construct($language='');

    /**
    * Resolve variables in a text
    * 
    * @param string $text
    * @param array $vars array of key=>value 
    * @param string $bracket 2 byte string. Messagekey must be embedded between these
    * @return text with variables resolved
    */
    public function resolve($text,$vars=array(),$bracket='{}');
    
    /**
    * Resolve using a template
    * @note there is no way to use parameters in messages in a template file
    * 
    * @param string $file file name, as is, in which messages will be translated
    * @param array $vars array of string, $key=>$value extra translations, have precedence, default empty array
    * @param string $bracket array of char, messagekey must be embedded between these, default '{}'
    * @return template as string with messages translated and variables resolved
    */
    public function template($file,$vars=array(),$bracket='{}');

    /**
    * Get content of whole messagefile
    * 
    * @param string $key
    * @return array string messages as key=>text
    */
    public function getMsgs($key);
    /**
    * Get text from file depending on current language
    * File may contain messages and variables (see params $vars, $brackets)
    * 
    * @param string $file name of file, no prefix or language suffix
    * @param array $vars optional array of varname=>varvalue to resolve within text
    * @param string $brackets characters which make up the brackets to identify variables
    * @return string text from a language specific version of $file
    */
    public function text($file,$vars=array(),$brackets='{}');
    /**
    * Translate a message
    * @note If key not found the original msg is returned if ReturnKey is set to true (default) or empty string if ReturnKey is set to false. See method SetReturnKey 
    * 
    * @param string $msg message, should start with key followed by 0 to n characters
    * @param array $params parameters to insert in message
    * @return string translated text
    */
    public function t($msg,$params=array());
    /**
    * Set the prefix for message files
    * 
    * @param string $pfx prefix (if a dirname make sure it ends with slash)
    * @return pinTrans itself
    */
    public function setFilePrefix($pfx);
    /**
    * Set the suffix for message files
    * 
    * @param string $sfx suffix 
    * @return pinTrans itself
    */
    public function setFileSuffix($sfx);
    /**
    * Return fileprefix for message files
    * 
    * @return string file prefix
    */
    public function getFilePrefix();
    /**
    * Return file suffix for message files
    * 
    * @return string suffix
    */
    public function getFileSuffix();
    /**
    * Set the default language
    * 
    * @param string $lang default language
    * @return IpinTranslator instance itself
    */
    public function setDefaultLanguage($lang);
    
    /**
    * Return default language
    */
    public function getDefaultLanguage();
    /**
    * Set the requested language
    * 
    * @param string $lang
    * @return IpinTranslator instance itself
    */
    public function setLanguage($lang);

	/**
	* return if t should return the key if no message found (true) or empty string (false)
	* 
	* @return returnkey setting
	*/    
    public function getReturnKey();
    /**
	* @param boolean $doReturnKey true t-method should return key itself if message not found, otherwise empty string
	* @return IpinTranslator instance itself
	*/
    public function setReturnKey($doReturnKey);
}

?>