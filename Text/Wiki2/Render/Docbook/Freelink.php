<?php
// vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4:
/**
 * Freelink rule end renderer for Docbook
 *
 * PHP version 5
 *
 * @category   Text
 * @package    Text_Wiki2_Docbook
 * @author     bertrand Gugger <bertrand@toggg.com>
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/Text_Wiki2_Docbook
 */

/**
 * The wikilink render class.
 */
require_once 'Text/Wiki2/Render/Docbook/Wikilink.php';

/**
 * This class renders free links in DocBook.
 *
 * @category   Text
 * @package    Text_Wiki2_Docbook
 * @author     bertrand Gugger <bertrand@toggg.com>
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version    Release: @package_version@
 * @link       http://pear.php.net/package/Text_Wiki2_Docbook
 */
class Text_Wiki2_Render_Docbook_Freelink extends Text_Wiki2_Render_Docbook_Wikilink {
    // renders identically to wikilinks, only the parsing is different :-)
}

?>
