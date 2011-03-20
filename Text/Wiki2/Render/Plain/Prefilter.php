<?php
// vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4:
/**
 * This class implements a Text_Wiki2_Render_Xhtml to "pre-filter" source text so
 * that line endings are consistently \n, lines ending in a backslash \
 * are concatenated with the next line, and tabs are converted to spaces.
 *
 * PHP version 5
 *
 * @category   Text
 * @package    Text_Wiki
 * @author     Paul M. Jones <pmjones@php.net>
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/Text_Wiki
 */

class Text_Wiki2_Render_Plain_Prefilter extends Text_Wiki2_Render {
    function token()
    {
        return '';
    }
}
?>