<?php
// vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4:
/**
 * Titlebar rule end renderer for Docbook
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
 * This class renders a title bar in DocBook.
 *
 * @category   Text
 * @package    Text_Wiki2_Docbook
 * @author     bertrand Gugger <bertrand@toggg.com>
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version    Release: @package_version@
 * @link       http://pear.php.net/package/Text_Wiki2_Docbook
 */
class Text_Wiki2_Render_Docbook_Titlebar extends Text_Wiki2_Render {

    var $conf = array(
        'css' => 'titlebar'
    );

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
        if ($options['type'] == 'start') {
            $css = $this->formatConf(' class="%s"', 'css');
            return "<div$css>";
        }

        if ($options['type'] == 'end') {
            return '</div>';
        }
    }
}
?>
