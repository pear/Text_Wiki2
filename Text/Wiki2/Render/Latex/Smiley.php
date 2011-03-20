<?php
// vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4:
/**
 * Smiley rule Latex renderer
 *
 * PHP version 5
 *
 * @category   Text
 * @package    Text_Wiki
 * @author     Bertrand Gugger <bertrand@toggg.com>
 * @copyright  2005 bertrand Gugger
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/Text_Wiki
 */

/**
 * Smiley rule Latex render class
 *
 * @category   Text
 * @package    Text_Wiki
 * @author     Bertrand Gugger <bertrand@toggg.com>
 * @copyright  2005 bertrand Gugger
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version    Release: @package_version@
 * @link       http://pear.php.net/package/Text_Wiki
 * @see        Text_Wiki2::Text_Wiki2_Render()
 */
class Text_Wiki2_Render_Latex_Smiley extends Text_Wiki2_Render {

    /**
      * Renders a token into text matching the requested format.
      * process the Smileys
      *
      * @access public
      * @param array $options The "options" portion of the token (second element).
      * @return string The text rendered from the token options.
      */
    function token($options)
    {
        return $options['symbol'];
    }
}
?>
