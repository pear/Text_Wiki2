<?php
// vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4:
/**
 * Code rule end renderer for Docbook
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
 * This class renders code blocks in DocBook.
 *
 * @category   Text
 * @package    Text_Wiki2_Docbook
 * @author     bertrand Gugger <bertrand@toggg.com>
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version    Release: @package_version@
 * @link       http://pear.php.net/package/Text_Wiki2_Docbook
 */
class Text_Wiki2_Render_Docbook_Code extends Text_Wiki2_Render {

    /**
    *
    * Renders a token into text matching the requested format.
    *
    * @access public
    *
    * @param array $options The "options" portion of the token (second
    * element).
    *
    * @return string The text rendered from the token options.
    *
    */

    function token($options)
    {
        $filename = empty($options['attr']) || empty($options['attr']['filename']) ?
            '' : '<filename>' . $options['attr']['filename'] . "</filename>\n";
        $type = empty($options['attr']) || empty($options['attr']['type']) ?
            '' : ' language="' . strtolower($options['attr']['type']) . '"';
        return "<programlisting{$type}>\n{$filename}<![CDATA[\n" .
            str_replace("\t", '    ', $options['text']) .
            "\n]]>\n</programlisting>\n";
    }
}
?>
