<?php

/**
* 
* The rule removes all remaining newlines.
* 
* @category Text
* 
* @package Text_Wiki
* 
* @author Paul M. Jones <pmjones@php.net>
* 
* @license LGPL
* 
* @version $Id$
* 
*/


/**
* 
* The rule removes all remaining newlines.
*
* @category Text
* 
* @package Text_Wiki
* 
* @author Paul M. Jones <pmjones@php.net>
* 
* @version @package_version@
* 
*/

class Text_Wiki_Parse_Tighten extends Text_Wiki_Parse {
    
    
    /**
    * 
    * Apply tightening directly to the source text.
    *
    * @access public
    * 
    */
    
    function parse()
    {
        $this->wiki->source = str_replace("\n", '',
            $this->wiki->source);
    }
}
?>