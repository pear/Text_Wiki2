<?php
// vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4:
/**
 * Revise rule end renderer for Docbook
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
 * This class renders revision marks in DocBook.
 *
 * @category   Text
 * @package    Text_Wiki2_Docbook
 * @author     bertrand Gugger <bertrand@toggg.com>
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version    Release: @package_version@
 * @link       http://pear.php.net/package/Text_Wiki2_Docbook
 */
class Text_Wiki2_Render_Docbook_Revise extends Text_Wiki2_Render {

    var $conf = array(
        'role_ins' => 'inserted',
        'role_del' => 'deleted'
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
        switch ($options['type']) {
            case 'del_start':
                return '<emphasis' . 
                    (($role = $this->getConf('role_del', 'deleted')) ?
                    ' role="' . $role . '"' : '') . '>';
            case 'ins_start':
                return '<emphasis' . 
                    (($role = $this->getConf('role_ins', 'inserted')) ?
                    ' role="' . $role . '"' : '') . '>';
        }
        return '</emphasis>';
    }
}
?>
