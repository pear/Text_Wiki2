<?php
// vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4:
/**
 * Parse structured wiki text and render into arbitrary formats such as XHTML.
 * This is the Text_Wiki2 extension for Cowiki markup
 *
 * PHP version 5
 *
 * @category   Text
 * @package    Text_Wiki
 * @author     Justin Patrin <papercrane@reversefold.com>
 * @author     Bertrand Gugger <bertrand@toggg.com>
 * @author     Paul M. Jones <pmjones@php.net>
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/Text_Wiki
 */

/**
 * "master" class for handling the management and convenience
 */
require_once('Text/Wiki2.php');

/**
 * Base Text_Wiki2 handler class extension for Cowiki markup
 *
 * @category   Text
 * @package    Text_Wiki
 * @author     Justin Patrin <papercrane@reversefold.com>
 * @author     Bertrand Gugger <bertrand@toggg.com>
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version    Release: @package_version@
 * @link       http://pear.php.net/package/Text_Wiki
 * @see        Text_Wiki2::Text_Wiki2()
 */
class Text_Wiki2_Cowiki extends Text_Wiki2 {

    function Text_Wiki2_Cowiki() {
        parent::Text_Wiki2();
        $paths = $this->getPath('parse');
        $this->addPath('parse', str_replace('Default', 'Cowiki', $paths[0]));
    }
}
