<?php
/**
* PiG (Pinyon General) is a static class with common functions
* 
* @package Pinyon.Misc
* @see <a href="http://www.pinyonpine.nl">Pinyon Pine</a>
* @author <a href="http://www.tigrez.nl">Tigrez Software / Luc Geritz</a>
*/

class pig
{

########################
# Pig = Pinyon General #
########################

/**
* Get name for temporary file
* @note does not create the file
* @note use '.' for current dir, not ''
* 
* @param string $dir directory for file. Default is "" which means system temp dir
* @param string $ext string extension for file including dot, default ""
* @param string $prefix prefix to put in front in file (after dir), default "" 
* @return string filename
*/
public static function getTempFileName($dir='', $ext='',$prefix=''){
    
    if(!$dir) $dir=sys_get_temp_dir();
    
    while(true){
        $filename = $dir . uniqid($prefix, true) . $ext;
        if (!file_exists($filename)) break;
    }
    
    return $filename;

}

/**
* Build tokenized string from associative array
* @note at the time probably didn't know about http_build_query which roughly does the same :) 
* 
* @param array $arr string array of key=>value
* @param string $gluechar is put between key-value pairs, default '&' is handy for url queries
* @param string $equalchar string is put between key and value, handy in most cases, default '='
* @return string array converted to string
*/
public static function keyValueArr2Str($arr,$gluechar='&',$equalchar='='){
    $str='';
    if($arr){
      foreach($arr as $key=>$val){
          $str.=($str=='' ? '' : $gluechar) . $key.$equalchar.$val; 
      }
    }
    return $str;
}

	/**
    * Concat all keys of an array to string
    * 
    * @param array $array
    * @param string $sep optional separator between strings, default ", "
    * @return string keys of concatinated to string
    */
    public static function arrayKeysAsString($array,$sep=', ')
	{
		$res='';
		foreach($array as $key=>$val)
		{
			$res.=($res=='' ? $key : $sep.$key);
		}
		return $res;
	}
    
/**
* build a url query string from array
* @note params described below are passed in a single array of name=>value pairs
* @note http_build_query! Kept for backward compatibility
* 
* @param $excludes string[] array with parameters to ignore, you specify either includes or excludes
* @param $includes string[] array with only those parameters to include, if excludes is present includes will be ignored
* @param $prefix string this string is prefixed to all parameters, optional
* @param $urlparams string[] assoc.array with name=>value pairs containing url parameters, optional, if not specified $_GET is used.
* @param $replacethis string string to be replaced in parameter **name**
* @param $replaceby string string replacing replacethis in parameter **name**, if empty and replacethis filled then effectively the replaceby is removed
* @return string string containing url query, does **not** contain the separating '?'
*/
public static function urlParams2Str($params=array())
{
   
   if(!$params['urlparams']){
       $params['urlparams']=$_GET;    
   }
   
   $including=array_key_exists('includes',$params);        
   $replacing=array_key_exists('replacethis',$params);
        
   $urlQuery='';     
   foreach($params['urlparams'] as $key=>$value){
       if(($including && array_key_exists($key,$params['includes'])) ||
          (!$including && !array_key_exists($key,$params['excludes']))){
              if(replacing) $value=str_replace($params['replacethis'],$params['replaceby']);
              $urlQuery.=(!$urlQuery ? '?' : '').$key.'='.$value.'&';
          }          
   }
   
   if($urlQuery) $urlQuer=substr($urlQuery,0,-1);    

   return $urlQuery;  

}
/**
* Copy the $_get entries to a string, prefix them and allow for excluded parameters
* @deprec 0.2 use urlparams2str which is more flexible
* 
* @param string $prefix prefix to put before all params
* @param array $exludes assoc array of (string)name=>(string)value specifying parameters to ignore 
* @return string url (see description above)
*/
public static function copy_get($prefix,$excludes=array()){
    $get=$_GET;
    foreach($get as $key=>$value){
        if(!in_array($key,$excludes)){
            $newgeturl.=($newgeturl==''?'':'&').$prefix.$key.'='.$value;
        }
    }
    return $newgeturl;
}
/**
* Get ancestors of a given class
* @note credits php.net by birkholz@web.de
* 
* @param class $class class of which we want ancestors
* @return array with ancestor classnames
*/
public static function getAncestors ($class) {
          
     for ($classes[] = $class; $class = get_parent_class ($class); $classes[] = $class);    
     return $classes;
      
}
/**
* Get TopClass of given class
* @note TopClass means the toplevel class in the inheritence line of a given class. 
* 
* @param $class string class(name) of which you want to know the topclass
* @return string topclass class(name)
*/
public static function getTopClass($class){
    $anc="";
    $ancestors=self::getAncestors($class);
    if(count($ancestors)>0) $anc=$ancestors[count($ancestors)-1];    
    return $anc;
}
/** 
* Check syntax of an email address
* 
* @param string $email the email address to check
* @return boolean true if valid, false if invalid
*/
  public static function checkEmail($email) {
     if(preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/",
               $email)){
    list($username,$domain)=split('@',$email);
    if(!checkdnsrr($domain,'MX')) {
      return false;
    }
    return true;
  }
  return false;
}
  
/**
* Force a trailing slash or other character
* @note misleading name, forces any trailing character 
* 
* @param $string string string to append a slash to
* @param $slash the character representing the 'slash'
* @return string $string with a slash if it wasn't there yet, otherwise unchanged
*/ 
public static function forceSlash($string,$slash='/')
{
  if ($string!="")
  {
     if($string[strlen($string)-1]!=$slash)
     {
        $string.=$slash;
     }
  }
  return $string;
}

/**
* force removal of a trailing slash or other character
* @note misleading name, forces any trailing character
* 
* @param $string string string to remove slash from
* @param $slash the character representing the 'slash'
* @return string $string without a trailing slash if it was there, otherwise unchanged
*/
public static function forceNoSlash($string,$slash='/')
{
  if ($string!="")
  {
     if($string[strlen($string)-1]==$slash)
     {
        $string=substr($string,0,strlen($string)-1);
     }
  }
 return $string;
}

/**
* add a linefeed (chr10) to a string
*  
* @param string $s input
* @return string input + linefeed
*/
public static function lf($s)
{
   return $s.chr(10);
}

/**
* return an URL as base ref tag
* @param string $url url, may contain _target property
* @return string url as base tag
*/
public static function base_href_element($url){
  return '<base href="' . $url . '" />';
}

/**
* Return a link-stylesheet tag
* 
* @param string $cssfile
* @return string cssfile as link-stylesheet element for inclusion in a html-document
*/
public static function link_stylesheet_element($cssfile)
{
  return '<link rel="stylesheet" href="'.$cssfile.'"
     type="text/css">';
}
/**
* show value in color
* @note uses <span> and style= syntax. Color must be valid in html
* @param $val string value to display
* @param $col string color in which to display value
* @return value embedded in a span tag with color
*/
	public static function inColor($val,$col)
	{
		return '<span style="color:'.$col.';">'.$val.'</span>';
	}
/**
* 
* @param string $tag tag
* @param string $val value to show in tag
* @param string $htmloptions array key=>value of additional attributes
* 
* @return
*/    
	public static function inTag($tag,$val,$htmloptions=array())
	{
		$ret='<'.$tag.' ';
		if(count($htmloptions)>0)
		{
			foreach($htmloptions as $key=>$optvalue)
			{
				$ret.=$key.'="'.$optvalue.'" ';
			}
		}
		$ret.='>'.$val.'</'.$tag.'>';
		return $ret;
	}
    
    
/// return a value within a table-tag    
	public static function inTable($val,$htmloptions=array())
	{
		return self::inTag('table',$val,$htmloptions);
	}
/// return a value within a TR-tag    
	public static function inTR($val,$htmloptions=array())
	{
		return self::inTag('tr',$val,$htmloptions);
	}
/// return a value within a TD-tag    
	public static function inTD($val,$htmloptions=array())
	{
		return self::inTag('td',$val,$htmloptions);
	}
    /**
    * return a value within a TH-tag    
    */
	public static function inTH($val,$htmloptions=array())
	{
		return self::inTag('th',$val,$htmloptions);
	}
    /**
    * return a value within a P-tag    
    */
	public static function inP($val,$htmloptions=array())
	{
		return self::inTag('p',$val,$htmloptions);
	}
    /**
    * return a value within a B-tag (bold)    
    */
	public static function inB($val,$htmloptions=array())
	{
		return self::inTag('b',$val,$htmloptions);
	}
    
/**
* Convert boolean to 1 or 0
* 
* @param undefined $bool
* @return int 1 for true, 0 for false
*/
public static function bool2bit($bool){
    return $bool ? 1 : 0;
}

/// return a value from $_GET first then, if not found from $_POST
/// @param $var string variable to search in either $_GET or $_POST
/// @return string if found in $_GET or $_POST it returns corresponding value, otherwise empty string
public static function getOrPost($var){
   $ret='';
   if(isset($_GET[$var])){
     $ret=$_GET[$var];
   }
   else{
     if(isset($_POST[$var])){
        $ret=$_POST[$var];
     }
   }
   return $ret;
}

/// return a value from $_POST first then, if not found from $_GET
/// @param $var string variable to search in either $_POST or $_GET
/// @return string if found in $_POST or $_GET it returns corresponding value, otherwise empty string
public static function postOrGet($var){
   $ret='';
   if(isset($_POST[$var])){
     $ret=$_POST[$var];
   }
   else{
     if(isset($_GET[$var])){
        $ret=$_GET[$var];
     }
   }
   return $ret;
}

/// assign a default value if value is empty
/// @mindyou <a href="http://php.net/manual/en/function.empty.php">definition of empty</a>
/// @param $var mixed the variable you want to asssign a value to
/// @param $dft mixed the default value for $var
/// @return mixed $var with default value $dft if $var was empty otherwise unchanged
public static function dft($var,$dft){
  return empty($var) ? $dft : $var;  
}
/// return integer as string of given length with leading zeroes
/// @param $num int number to convert
/// @param $numDigits int length of return string
/// @return string $num converted to string with leading zeroes
public static function zeroStr($num,$numDigits) {
   return sprintf("%0".$numDigits."d",$num);
}

/**
* Return a salt for md5 encryption
* @todo $pickfrom should become parameter
* @param int $length the length of the salt
* @return string md5-salt
*/
public static function md5Salt($length=10){
    $salt='$1$';
    $pickfrom='ABCDEFGHIJKLMNOPQRSTUVWXYZ'.
              'abcdefghijklmnopqrstuvwxyz'.
              '0123456789';
    $length-=strlen($salt);          
    for($i=0;$i<$length;$i++){
        $salt.=$pickfrom[rand(0,strlen($pickfrom)-1)];
    }
    return $salt;
}

/**
* Return a salt for blowfish  encryption
* @note Uses "$2a$" as the salt prefix for compit. with versions of PHP before 5.3.7
* @todo $pickfrom should become parameter
* 
* @param string $cost '04' to '31' 
* @return string blowfish-salt
*/
public static function blowfishSalt($cost='08'){
    // 
    $salt='$2a$'.$cost.'$';
    $pickfrom='ABCDEFGHIJKLMNOPQRSTUVWXYZ'.
              'abcdefghijklmnopqrstuvwxyz'.
              '0123456789';
    for($i=0;$i<22;$i++){
        $salt.=$pickfrom[rand(0,strlen($pickfrom)-1)];
    }
    return $salt;
}
/**
* Strip redundant characters from string
* @note If a character is defined in the redundant character string then if a sequence of 2 or more of this character is encountered only 1 will remain in the output string.
* @example 
* If we define 'r' as redundant then
* 'Strrrrrong!' will return 'Strong'
* 'Array("rr")' will return 'Aray("r")'
* Typical usage is a sentence from which we remove redundant spaces.
* 'Sentence with&nbsp;&nbsp;&nbsp;&nbsp;many&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;spaces&nbsp;&nbsp;' will return 'Sentence with many spaces 
*  
* @param string $source string from which to move redundant characters
* @param string $chars defining (potentially) redundant characters, default is space
* 
* @return string $source with redundant characters removed
*/
public static function stripRed($source,$chars=' '){
    $copy='';
    $last='';
    for($i=0;$i<strlen($source);$i++){
        $cur=substr($source,$i,1);
        if(strpos($chars,$cur)!==false){
            // current char is one to be remove redundancy of
            if($last != $cur) $copy.=$cur;
        }            
        else $copy.=$cur;
        $last=$cur;
    }
    return $copy;
}

/**
* Return current url
* @note source: http://snipplr.com/view/2734/get-full-url/
* 
* @return
*/
  public static function fullUrl()
  {
	$s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
	$protocol = substr(strtolower($_SERVER["SERVER_PROTOCOL"]), 0, strpos(strtolower($_SERVER["SERVER_PROTOCOL"]), "/")) . $s;
	$port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
	return $protocol . "://" . $_SERVER['SERVER_NAME'] . $port . $_SERVER['REQUEST_URI'];
   }
}