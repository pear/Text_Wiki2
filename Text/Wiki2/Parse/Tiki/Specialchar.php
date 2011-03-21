<?php
// vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4:
/**
 * Specialchar rule end parser for tikiwiki
 *
 * PHP version 5
 *
 * @category   Text
 * @package    Text_Wiki
 * @author     Justin Patrin <papercrane@reversefold.com>
 * @author     Paul M. Jones <pmjones@php.net>
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/Text_Wiki
 */

/**
 * This class parses special chars markups for tikiwiki and replace them with a token
 *
 * @category   Text
 * @package    Text_Wiki
 * @author     Justin Patrin <papercrane@reversefold.com>
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version    Release: @package_version@
 * @link       http://pear.php.net/package/Text_Wiki
 */
class Text_Wiki2_Parse_SpecialChar extends Text_Wiki2_Parse {

    var $types = array('~bs~',
                       '~hs~',
                       '~amp~',
                       '~ldq~',
                       '~rdq~',
                       '~lsq~',
                       '~rsq~',
                       '~c~',
                       '~--~',
                       '~lt~',
                       '~gt~');

    function __construct(&$obj) {
        parent::__construct($obj);

        $this->regex = '';
        foreach ($this->types as $type) {
            if ($this->regex) {
                $this->regex .= '|';
            }
            $this->regex .= preg_quote($type);
        }
        $this->regex = '/('.$this->regex.'|("|&quot;) \-\- (?:\2)|\~\d+\~)/';
    }

    /**
    *
    * Generates a replacement token for the matched text. (option is 'char'=>initial code)
    *
    * @access public
    *
    * @param array &$matches The array of matches from parse().
    *
    * @return string A delimited token to be used as a placeholder in
    * the source text.
    *
    */

    function process(&$matches)
    {
        return $this->wiki->addToken($this->rule, array('char' => $matches[1]));
    }
}

?>
