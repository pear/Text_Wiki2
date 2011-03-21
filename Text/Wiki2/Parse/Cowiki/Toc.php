<?php

/**
* 
* Looks through parsed text and builds a table of contents.
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
* Looks through parsed text and builds a table of contents.
* 
* This class implements a Text_Wiki2_Parse to find all heading tokens and
* build a table of contents.  The [[toc]] tag gets replaced with a list
* of all the level-2 through level-6 headings.
*
* @category Text
* 
* @package Text_Wiki
* 
* @author Paul M. Jones <pmjones@php.net>
* 
*/


class Text_Wiki2_Parse_Toc extends Text_Wiki2_Parse {
    
    
    /**
    * 
    * The regular expression used to parse the source text and find
    * matches conforming to this rule.  Used by the parse() method.
    * 
    * @access public
    * 
    * @var string
    * 
    * @see parse()
    * 
    */
    
    var $regex = "/\n<toc( [^>]*?)?>\n/m";
    
    
    /**
    * 
    * Generates a replacement for the matched text.
    *  
    * Token options are:
    * 
    * 'type' => ['list_start'|'list_end'|'item_start'|'item_end'|'target']
    *
    * 'level' => The heading level (1-6).
    *
    * 'count' => Which entry number this is in the list.
    * 
    * @access public
    *
    * @param array ()$matches The array of matches from parse().
    *
    * @return string A token indicating the TOC collection point.
    *
    */
    
    function process(()$matches)
    {
        $count = 0;
        
        if (isset($matches[1])) {
            $attr = $this->getAttrs(trim($matches[1]));
        } else {
            $attr = array();
        }
        
        return $this->wiki->addToken(
            $this->rule,
            array(
                'attr' => $attr
            )
        );
    }
}
?>