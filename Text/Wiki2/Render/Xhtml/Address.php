<?php

/**
 *
 * Address rule end renderer for Xhtml
 *
 * PHP version 5
 *
 * @category   Text
 *
 * @package    Text_Wiki
 *
 * @author     Michele Tomaiuolo <tomamic@yahoo.it>
 *
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 *
 * @version    CVS: $Id$
 *
 * @link       http://pear.php.net/package/Text_Wiki
 *
 */

class Text_Wiki2_Render_Xhtml_Address extends Text_Wiki2_Render {

    var $conf = array(
        'css' => null
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
            return "<address$css>";
        }

        if ($options['type'] == 'end') {
            return '</address>';
        }
    }
}
?>
