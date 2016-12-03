#Version 0.1.4
This file *version.txt* became *version.md*

##pinAutoloader
-  **New method** `throwExceptionWhenNotFound()` Throwing an exception when class is not found can be surpressed by calling                 `pinAutoloader::throwExceptionWhenNotFound(false)`, for example in *pinyon.php*. 

##pinBaseView
- **New method** `getDescription()`; Get description of view embedded in meta-description tag. Typically you'd do that in the layout file. Example: `echo $controller->hasView() ? $controller->getView()->getDescription() : '';` The view should set description by `$this->description`. 
	
##pinBaseController
- **New method** `cancelReason()`; Needs to  be overridden in extended class. Returns an error code, useful in the pinWebApp::onCanceled routine to determine why router is canceled. Return value `0` should mean 'not canceled', no rules for any of the other codes. ( And this is PHP,  you might  want  to return  strings :o or whatever. )  Code will most likely be 
set in the `onStart()` pseudo event of your controller.
- **New method** `getView()` returns the view object. 

##pinConfig
- Use of any directory for config file if accessed by name
- Changes in custom config files (meaning those *not* in  the PIN_CONFIGDIR)  
  can be saved. Expression type values will be evaluated (e.g. 'a'=>1+1 will
  become 'a'=>2, not sure if it always works for complex values. 
  System config files (those in the PIN_CONFIGDIR) cannot be saved, no error
  raised.   If   you   really   want   to   save  system   configs   use   a 
  return(include('some_file_outside_system')) type of construction.
- new method save(file) save settings to file, optional file name to save it
  to different file
- **New method** `setSettings(array)` to set all settings at once
- **New method** `getFileName()` to get full filename of the file, 
  examples:
       
```
pinConfig::i()->getFileName(); // returns 'safe/configs/appl.cfg.php'
PinConfig::thisClass()->getFileName(); //  returns  'safe/configs/homeView.cfg.php'
```
- Handles, getHandle(), setHandle() to address a config file by handle
- In settings there's a new variable you can use: `%ROOT%` which is replaced by the result of `pinUrl::i()->getRoot()`.
 
##pinRouter
- Changes to run in E_STRICT mode

##pinHtml
- If any element contains an id attribute then the id-property of the element is set with this value. 
- **New method** `getElementById()`; Find element with given id mentioned above. Returned element can be main element or one of its children, or null if not found
- **New method** `getId()`; Retrieve the id, '' if not set.
 
##pinWebApp
- Changes to run in E_STRICT mode
- You do not pass the route name to the start method anymore. For backward
  compatibility any parameter passed is ignored. 
- **Deprecated method** `redirect`. If you want to redirect your flow                                               from within a controller use `pinUrl::i()->redirect`
- **Deprecated method** `pinWebApp::redirectRoute`. If you want to redirect your flow  based on  a route from within   a   controller  use `pinUrl::i()->redirectRoute`
- **New methods** `prependChild()`, `insertChildAt()` add child at begin or at given index. Child can be pinHtml or text.
  

##new class pinSURLWebApp
- Extends `pinWebApp` and allows for use of semantic urls. (This is just one
part of it, the other being url-rewrites and your routes might need to be-
come aware of semantic urls if you  use more parameter  then 'r' and 'a'.)

##new class pinUrl
- Singleton class, instance accessed by `pinUrl::i()`. Will  become the main
  access to url related things. For 0.1.4 it contains:
- method `getRoot()` which returns the root url.
- method `redirect()` redirects to a url
- method `redirectRoute()` redirects to a route
- method `getRouteBasedUrl()` constructs a route based url

##pig
- removed  bug  from `getElement` method, if index was array it only checked
  1st element.
- changed `getStrElement`, default value  for $default  became '', was null,
  makes it more useful for string operations
 
##general
Refactoring, better code comments here there..

