<?php
/**
* Basal errors 
* @note only to be used by Pinyon classes
* 
* @package Pinyon.Error
* @see <a href="http://www.pinyonpine.nl">Pinyon Pine</a>
* @author <a href="http://www.tigrez.nl">Tigrez Software / Luc Geritz</a>
* */
class pinBasalError {
    const E001='Class could not be loaded: ';
    const E002='Module could not be loaded: ';
    const E003=' is not an instance of ';
    const E004='The controller is only allowed in DEBUG mode (PIN_DEBUG must be set to true)';
    const E005='No IpinLogger class injected in pinLog (in class config file)';
    const E006='logfile not set for pinTextLogger (in class config file)';
    const E007='settings for pinWebApp not found (in class config file)';     
    const E008='Invalid request';     
    const E009='Not authorized for this request';
    const E010='Class file could not be found: ';
}