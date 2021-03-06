<?php
// vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4:
/**
 * Parse structured wiki text and render into arbitrary formats such as XHTML.
 * This is the Text_Wiki2 extension for Mediawiki markup
 *
 * PHP version 5
 *
 * @category   Text
 * @package    Text_Wiki
 * @author     Bertrand Gugger <bertrand@toggg.com>
 * @author     Paul M. Jones <pmjones@php.net>
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/Text_Wiki
 */

/**
 * "master" class for handling the management and convenience
 */
require_once 'Text/Wiki2.php';

/**
 * Base Text_Wiki2 handler class extension for Mediawiki markup
 *
 * @category   Text
 * @package    Text_Wiki
 * @author     Bertrand Gugger <bertrand@toggg.com>
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version    Release: @package_version@
 * @link       http://pear.php.net/package/Text_Wiki
 * @see        Text_Wiki2::Text_Wiki2()
 */
class Text_Wiki2_Mediawiki extends Text_Wiki2
{
    var $rules = array(
        'Prefilter',
        'Delimiter',
        'Code',
        'Comment',
        'Preformatted',
//        'Plugin',
//        'Function',
//        'Html',
        'Raw',
//        'Include',
//        'Embed',
//        'Page',
//        'Anchor',
        'Heading',
        'Toc',
//        'Titlebar',
        'Horiz',
        'Redirect',
        'Break',
//        'Blockquote',
//        'Box',
        'Wikilink',
//        'Image', // done by Wikilink but still possible to disable/configure
//        'Interwiki', // done by Wikilink but still possible to disable/configure
        'Table',
//        'Phplookup',
//        'Center',
        'List',
        'Deflist',
//        'Strong',  ** will be only fake inserted by Emphasis if needed for render
        'Emphasis', // must run before Newline (see Text_Wiki2_Parse_Emphasis::parse())
        'Newline',
        'Paragraph',
        'Url',
//        'Freelink',
//        'Colortext',
//        'Bold',
//        'Italic',
//        'Underline',
        'Tt',
        'Superscript',
        'Subscript',
//        'Specialchar',
//        'Revise',
        'Tighten'
    );

	/**
     * Constructor: just adds the path to Mediawiki rules
     *
     * @access public
     * @param array $rules The set of rules to load for this object.
     */
    function __construct($rules = null)
    {
        parent::__construct($rules);
        $this->addPath('parse', $this->fixPath(__DIR__) . 'Parse/Mediawiki');
    }
}
