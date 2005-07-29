<?php
// vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4:
/**
 * Parse structured wiki text and render into arbitrary formats such as XHTML.
 *
 * PHP versions 4 and 5
 *
 * @category   Text
 * @package    Text_Wiki
 * @author     Paul M. Jones <pmjones@php.net>
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/Text_Wiki
 */

/**
 * The baseline abstract parser class.
 */
require_once 'Text/Wiki/Parse.php';

/**
 * The baseline abstract render class.
 */
require_once 'Text/Wiki/Render.php';

/**
 * Parse structured wiki text and render into arbitrary formats such as XHTML.
 *
 * This is the "master" class for handling the management and convenience
 * functions to transform Wiki-formatted text.
 *
 * @category   Text
 * @package    Text_Wiki
 * @author     Paul M. Jones <pmjones@php.net>
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version    Release: @package_version@
 * @link       http://pear.php.net/package/Text_Wiki
 */
class Text_Wiki {

    /**
    *
    * The default list of rules, in order, to apply to the source text.
    *
    * @access public
    *
    * @var array
    *
    */

    var $rules = array(
        'Prefilter',
        'Delimiter',
        'Code',
        'Function',
        'Html',
        'Raw',
        'Include',
        'Embed',
        'Anchor',
        'Heading',
        'Toc',
        'Horiz',
        'Break',
        'Blockquote',
        'List',
        'Deflist',
        'Table',
        'Image',
        'Phplookup',
        'Center',
        'Newline',
        'Paragraph',
        'Url',
        'Freelink',
        'Interwiki',
        'Wikilink',
        'Colortext',
        'Strong',
        'Bold',
        'Emphasis',
        'Italic',
        'Underline',
        'Tt',
        'Superscript',
        'Subscript',
        'Revise',
        'Tighten'
    );


    /**
    *
    * The list of rules to not-apply to the source text.
    *
    * @access public
    *
    * @var array
    *
    */

    var $disable = array(
        'Html',
        'Include',
        'Embed'
    );


    /**
    *
    * Custom configuration for rules at the parsing stage.
    *
    * In this array, the key is the parsing rule name, and the value is
    * an array of key-value configuration pairs corresponding to the $conf
    * property in the target parsing rule.
    *
    * For example:
    *
    * <code>
    * $parseConf = array(
    *     'Include' => array(
    *         'base' => '/path/to/scripts/'
    *     )
    * );
    * </code>
    *
    * Note that most default rules do not need any parsing configuration.
    *
    * @access public
    *
    * @var array
    *
    */

    var $parseConf = array();


    /**
    *
    * Custom configuration for rules at the rendering stage.
    *
    * Because rendering may be different for each target format, the
    * first-level element in this array is always a format name (e.g.,
    * 'Xhtml').
    *
    * Within that first level element, the subsequent elements match the
    * $parseConf format. That is, the sub-key is the rendering rule name,
    * and the sub-value is an array of key-value configuration pairs
    * corresponding to the $conf property in the target rendering rule.
    *
    * @access public
    *
    * @var array
    *
    */

    var $renderConf = array(
        'Docbook' => array(),
        'Latex' => array(),
        'Pdf' => array(),
        'Plain' => array(),
        'Rtf' => array(),
        'Xhtml' => array()
    );


    /**
    *
    * Custom configuration for the output format itself.
    *
    * Even though Text_Wiki will render the tokens from parsed text,
    * the format itself may require some configuration.  For example,
    * RTF needs to know font names and sizes, PDF requires page layout
    * information, and DocBook needs a section hierarchy.  This array
    * matches the $conf property of the the format-level renderer
    * (e.g., Text_Wiki_Render_Xhtml).
    *
    * In this array, the key is the rendering format name, and the value is
    * an array of key-value configuration pairs corresponding to the $conf
    * property in the rendering format rule.
    *
    * @access public
    *
    * @var array
    *
    */

    var $formatConf = array(
        'Docbook' => array(),
        'Latex' => array(),
        'Pdf' => array(),
        'Plain' => array(),
        'Rtf' => array(),
        'Xhtml' => array()
    );


    /**
    *
    * The delimiter for token numbers of parsed elements in source text.
    *
    * @access public
    *
    * @var string
    *
    */

    var $delim = "\xFF";


    /**
    *
    * The tokens generated by rules as the source text is parsed.
    *
    * As Text_Wiki applies rule classes to the source text, it will
    * replace portions of the text with a delimited token number.  This
    * is the array of those tokens, representing the replaced text and
    * any options set by the parser for that replaced text.
    *
    * The tokens array is sequential; each element is itself a sequential
    * array where element 0 is the name of the rule that generated the
    * token, and element 1 is an associative array where the key is an
    * option name and the value is an option value.
    *
    * @access private
    *
    * @var array
    *
    */

    var $tokens = array();


    /**
    *
    * The source text to which rules will be applied.
    *
    * This text will be transformed in-place, which means that it will
    * change as the rules are applied.
    *
    * @access private
    *
    * @var string
    *
    */

    var $source = '';


    /**
    *
    * Array of rule parsers.
    *
    * Text_Wiki creates one instance of every rule that is applied to
    * the source text; this array holds those instances.  The array key
    * is the rule name, and the array value is an instance of the rule
    * class.
    *
    * @access private
    *
    * @var array
    *
    */

    var $parseObj = array();


    /**
    *
    * Array of rule renderers.
    *
    * Text_Wiki creates one instance of every rule that is applied to
    * the source text; this array holds those instances.  The array key
    * is the rule name, and the array value is an instance of the rule
    * class.
    *
    * @access private
    *
    * @var array
    *
    */

    var $renderObj = array();


    /**
    *
    * Array of format renderers.
    *
    * @access private
    *
    * @var array
    *
    */

    var $formatObj = array();


    /**
    *
    * Array of paths to search, in order, for parsing and rendering rules.
    *
    * @access private
    *
    * @var array
    *
    */

    var $path = array(
        'parse' => array(),
        'render' => array()
    );



    /**
    *
    * The directory separator character.
    *
    * @access private
    *
    * @var string
    *
    */

    var $_dirSep = DIRECTORY_SEPARATOR;


    /**
    *
    * Constructor.
    *
    * @access public
    *
    * @param array $rules The set of rules to load for this object.
    *
    */

    function Text_Wiki($rules = null)
    {
        if (is_array($rules)) {
            $this->rules = $rules;
        }

        $this->addPath(
            'parse',
            $this->fixPath(dirname(__FILE__)) . 'Wiki/Parse/Default/'
        );

        $this->addPath(
            'render',
            $this->fixPath(dirname(__FILE__)) . 'Wiki/Render/'
        );

    }


    /**
    *
    * Set parser configuration for a specific rule and key.
    *
    * @access public
    *
    * @param string $rule The parse rule to set config for.
    *
    * @param array|string $arg1 The full config array to use for the
    * parse rule, or a conf key in that array.
    *
    * @param string $arg2 The config value for the key.
    *
    * @return void
    *
    */

    function setParseConf($rule, $arg1, $arg2 = null)
    {
        $rule = ucwords(strtolower($rule));

        if (! isset($this->parseConf[$rule])) {
            $this->parseConf[$rule] = array();
        }

        // if first arg is an array, use it as the entire
        // conf array for the rule.  otherwise, treat arg1
        // as a key and arg2 as a value for the rule conf.
        if (is_array($arg1)) {
            $this->parseConf[$rule] = $arg1;
        } else {
            $this->parseConf[$rule][$arg1] = $arg2;
        }
    }


    /**
    *
    * Get parser configuration for a specific rule and key.
    *
    * @access public
    *
    * @param string $rule The parse rule to get config for.
    *
    * @param string $key A key in the conf array; if null,
    * returns the entire conf array.
    *
    * @return mixed The whole conf array if no key is specified,
    * or the specific conf key value.
    *
    */

    function getParseConf($rule, $key = null)
    {
        $rule = ucwords(strtolower($rule));

        // the rule does not exist
        if (! isset($this->parseConf[$rule])) {
            return null;
        }

        // no key requested, return the whole array
        if (is_null($key)) {
            return $this->parseConf[$rule];
        }

        // does the requested key exist?
        if (isset($this->parseConf[$rule][$key])) {
            // yes, return that value
            return $this->parseConf[$rule][$key];
        } else {
            // no
            return null;
        }
    }


    /**
    *
    * Set renderer configuration for a specific format, rule, and key.
    *
    * @access public
    *
    * @param string $format The render format to set config for.
    *
    * @param string $rule The render rule to set config for in the format.
    *
    * @param array|string $arg1 The config array, or the config key
    * within the render rule.
    *
    * @param string $arg2 The config value for the key.
    *
    * @return void
    *
    */

    function setRenderConf($format, $rule, $arg1, $arg2 = null)
    {
        $format = ucwords(strtolower($format));
        $rule = ucwords(strtolower($rule));

        if (! isset($this->renderConf[$format])) {
            $this->renderConf[$format] = array();
        }

        if (! isset($this->renderConf[$format][$rule])) {
            $this->renderConf[$format][$rule] = array();
        }

        // if first arg is an array, use it as the entire
        // conf array for the render rule.  otherwise, treat arg1
        // as a key and arg2 as a value for the render rule conf.
        if (is_array($arg1)) {
            $this->renderConf[$format][$rule] = $arg1;
        } else {
            $this->renderConf[$format][$rule][$arg1] = $arg2;
        }
    }


    /**
    *
    * Get renderer configuration for a specific format, rule, and key.
    *
    * @access public
    *
    * @param string $format The render format to get config for.
    *
    * @param string $rule The render format rule to get config for.
    *
    * @param string $key A key in the conf array; if null,
    * returns the entire conf array.
    *
    * @return mixed The whole conf array if no key is specified,
    * or the specific conf key value.
    *
    */

    function getRenderConf($format, $rule, $key = null)
    {
        $format = ucwords(strtolower($format));
        $rule = ucwords(strtolower($rule));

        if (! isset($this->renderConf[$format]) ||
            ! isset($this->renderConf[$format][$rule])) {
            return null;
        }

        // no key requested, return the whole array
        if (is_null($key)) {
            return $this->renderConf[$format][$rule];
        }

        // does the requested key exist?
        if (isset($this->renderConf[$format][$rule][$key])) {
            // yes, return that value
            return $this->renderConf[$format][$rule][$key];
        } else {
            // no
            return null;
        }

    }

    /**
    *
    * Set format configuration for a specific rule and key.
    *
    * @access public
    *
    * @param string $format The format to set config for.
    *
    * @param string $key The config key within the format.
    *
    * @param string $val The config value for the key.
    *
    * @return void
    *
    */

    function setFormatConf($format, $arg1, $arg2 = null)
    {
        if (! is_array($this->formatConf[$format])) {
            $this->formatConf[$format] = array();
        }

        // if first arg is an array, use it as the entire
        // conf array for the format.  otherwise, treat arg1
        // as a key and arg2 as a value for the format conf.
        if (is_array($arg1)) {
            $this->formatConf[$format] = $arg1;
        } else {
            $this->formatConf[$format][$arg1] = $arg2;
        }
    }



    /**
    *
    * Get configuration for a specific format and key.
    *
    * @access public
    *
    * @param string $format The format to get config for.
    *
    * @param mixed $key A key in the conf array; if null,
    * returns the entire conf array.
    *
    * @return mixed The whole conf array if no key is specified,
    * or the specific conf key value.
    *
    */

    function getFormatConf($format, $key = null)
    {
        // the format does not exist
        if (! isset($this->formatConf[$format])) {
            return null;
        }

        // no key requested, return the whole array
        if (is_null($key)) {
            return $this->formatConf[$format];
        }

        // does the requested key exist?
        if (isset($this->formatConf[$format][$key])) {
            // yes, return that value
            return $this->formatConf[$format][$key];
        } else {
            // no
            return null;
        }
    }


    /**
    *
    * Inserts a rule into to the rule set.
    *
    * @access public
    *
    * @param string $name The name of the rule.  Should be different from
    * all other keys in the rule set.
    *
    * @param string $tgt The rule after which to insert this new rule.  By
    * default (null) the rule is inserted at the end; if set to '', inserts
    * at the beginning.
    *
    * @return void
    *
    */

    function insertRule($name, $tgt = null)
    {
        $name = ucwords(strtolower($name));
        if (! is_null($tgt)) {
            $tgt = ucwords(strtolower($tgt));
        }

        // does the rule name to be inserted already exist?
        if (in_array($name, $this->rules)) {
            // yes, return
            return null;
        }

        // the target name is not null, and not '', but does not exist
        // in the list of rules. this means we're trying to insert after
        // a target key, but the target key isn't there.
        if (! is_null($tgt) && $tgt != '' &&
            ! in_array($tgt, $this->rules)) {
            return false;
        }

        // if $tgt is null, insert at the end.  We know this is at the
        // end (instead of resetting an existing rule) becuase we exited
        // at the top of this method if the rule was already in place.
        if (is_null($tgt)) {
            $this->rules[] = $name;
            return true;
        }

        // save a copy of the current rules, then reset the rule set
        // so we can insert in the proper place later.
        // where to insert the rule?
        if ($tgt == '') {
            // insert at the beginning
            array_unshift($this->rules, $name);
            return true;
        }

        // insert after the named rule
        $tmp = $this->rules;
        $this->rules = array();

        foreach ($tmp as $val) {
            $this->rules[] = $val;
            if ($val == $tgt) {
                $this->rules[] = $name;
            }
        }

        return true;

    }


    /**
    *
    * Delete (remove or unset) a rule from the $rules property.
    *
    * @access public
    *
    * @param string $rule The name of the rule to remove.
    *
    * @return void
    *
    */

    function deleteRule($name)
    {
        $name = ucwords(strtolower($name));
        $key = array_search($name, $this->rules);
        if ($key !== false) {
            unset($this->rules[$key]);
        }
    }


    /**
    *
    * Change from one rule to another in-place.
    *
    * @access public
    *
    * @param string $old The name of the rule to change from.
    *
    * @param string $new The name of the rule to change to.
    *
    * @return void
    *
    */

    function changeRule($old, $new)
    {
        $old = ucwords(strtolower($old));
        $new = ucwords(strtolower($new));
        $key = array_search($old, $this->rules);
        if ($key !== false) {
            $this->rules[$old] = $new;
        }
    }


    /**
    *
    * Enables a rule so that it is applied when parsing.
    *
    * @access public
    *
    * @param string $rule The name of the rule to enable.
    *
    * @return void
    *
    */

    function enableRule($name)
    {
        $name = ucwords(strtolower($name));
        $key = array_search($name, $this->disable);
        if ($key !== false) {
            unset($this->disable[$key]);
        }
    }


    /**
    *
    * Disables a rule so that it is not applied when parsing.
    *
    * @access public
    *
    * @param string $rule The name of the rule to disable.
    *
    * @return void
    *
    */

    function disableRule($name)
    {
        $name = ucwords(strtolower($name));
        $key = array_search($name, $this->disable);
        if ($key === false) {
            $this->disable[] = $name;
        }
    }


    /**
    *
    * Parses and renders the text passed to it, and returns the results.
    *
    * First, the method parses the source text, applying rules to the
    * text as it goes.  These rules will modify the source text
    * in-place, replacing some text with delimited tokens (and
    * populating the $this->tokens array as it goes).
    *
    * Next, the method renders the in-place tokens into the requested
    * output format.
    *
    * Finally, the method returns the transformed text.  Note that the
    * source text is transformed in place; once it is transformed, it is
    * no longer the same as the original source text.
    *
    * @access public
    *
    * @param string $text The source text to which wiki rules should be
    * applied, both for parsing and for rendering.
    *
    * @param string $format The target output format, typically 'xhtml'.
    *  If a rule does not support a given format, the output from that
    * rule is rule-specific.
    *
    * @return string The transformed wiki text.
    *
    */

    function transform($text, $format = 'Xhtml')
    {
        $this->parse($text);
        return $this->render($format);
    }


    /**
    *
    * Sets the $_source text property, then parses it in place and
    * retains tokens in the $_tokens array property.
    *
    * @access public
    *
    * @param string $text The source text to which wiki rules should be
    * applied, both for parsing and for rendering.
    *
    * @return void
    *
    */

    function parse($text)
    {
        // set the object property for the source text
        $this->source = $text;

        // reset the tokens.
        $this->tokens = array();

        // apply the parse() method of each requested rule to the source
        // text.
        foreach ($this->rules as $name) {
            // do not parse the rules listed in $disable
            if (! in_array($name, $this->disable)) {

                // load the parsing object
                $this->loadParseObj($name);

                // load may have failed; only parse if
                // an object is in the array now
                if (is_object($this->parseObj[$name])) {
                    $this->parseObj[$name]->parse();
                }
            }
        }
    }


    /**
    *
    * Renders tokens back into the source text, based on the requested format.
    *
    * @access public
    *
    * @param string $format The target output format, typically 'xhtml'.
    * If a rule does not support a given format, the output from that
    * rule is rule-specific.
    *
    * @return string The transformed wiki text.
    *
    */

    function render($format = 'Xhtml')
    {
        // the rendering method we're going to use from each rule
        $format = ucwords(strtolower($format));

        // the eventual output text
        $output = '';

        // when passing through the parsed source text, keep track of when
        // we are in a delimited section
        $in_delim = false;

        // when in a delimited section, capture the token key number
        $key = '';

        // load the format object, or crap out if we can't find it
        $result = $this->loadFormatObj($format);
        if ($this->isError($result)) {
        	return $result;
        }

        // pre-rendering activity
        if (is_object($this->formatObj[$format])) {
            $output .= $this->formatObj[$format]->pre();
        }

        // load the render objects
        foreach (array_keys($this->parseObj) as $rule) {
            $this->loadRenderObj($format, $rule);
        }

        // pass through the parsed source text character by character
        $k = strlen($this->source);
        for ($i = 0; $i < $k; $i++) {

            // the current character
            $char = $this->source{$i};

            // are alredy in a delimited section?
            if ($in_delim) {

                // yes; are we ending the section?
                if ($char == $this->delim) {

                    // yes, get the replacement text for the delimited
                    // token number and unset the flag.
                    $key = (int)$key;
                    $rule = $this->tokens[$key][0];
                    $opts = $this->tokens[$key][1];
                    $output .= $this->renderObj[$rule]->token($opts);
                    $in_delim = false;

                } else {

                    // no, add to the dlimited token key number
                    $key .= $char;

                }

            } else {

                // not currently in a delimited section.
                // are we starting into a delimited section?
                if ($char == $this->delim) {
                    // yes, reset the previous key and
                    // set the flag.
                    $key = '';
                    $in_delim = true;
                } else {
                    // no, add to the output as-is
                    $output .= $char;
                }
            }
        }

        // post-rendering activity
        if (is_object($this->formatObj[$format])) {
            $output .= $this->formatObj[$format]->post();
        }

        // return the rendered source text.
        return $output;
    }


    /**
    *
    * Returns the parsed source text with delimited token placeholders.
    *
    * @access public
    *
    * @return string The parsed source text.
    *
    */

    function getSource()
    {
        return $this->source;
    }


    /**
    *
    * Returns tokens that have been parsed out of the source text.
    *
    * @access public
    *
    * @param array $rules If an array of rule names is passed, only return
    * tokens matching these rule names.  If no array is passed, return all
    * tokens.
    *
    * @return array An array of tokens.
    *
    */

    function getTokens($rules = null)
    {
        if (is_null($rules)) {
            return $this->tokens;
        } else {
            settype($rules, 'array');
            $result = array();
            foreach ($this->tokens as $key => $val) {
                if (in_array($val[0], $rules)) {
                    $result[$key] = $val;
                }
            }
            return $result;
        }
    }


    /**
    *
    * Add a token to the Text_Wiki tokens array, and return a delimited
    * token number.
    *
    * @access public
    *
    * @param array $options An associative array of options for the new
    * token array element.  The keys and values are specific to the
    * rule, and may or may not be common to other rule options.  Typical
    * options keys are 'text' and 'type' but may include others.
    *
    * @param boolean $id_only If true, return only the token number, not
    * a delimited token string.
    *
    * @return string|int By default, return the number of the
    * newly-created token array element with a delimiter prefix and
    * suffix; however, if $id_only is set to true, return only the token
    * number (no delimiters).
    *
    */

    function addToken($rule, $options = array(), $id_only = false)
    {
        // increment the token ID number.  note that if you parse
        // multiple times with the same Text_Wiki object, the ID number
        // will not reset to zero.
        static $id;
        if (! isset($id)) {
            $id = 0;
        } else {
            $id ++;
        }

        // force the options to be an array
        settype($options, 'array');

        // add the token
        $this->tokens[$id] = array(
            0 => $rule,
            1 => $options
        );

        // return a value
        if ($id_only) {
            // return the last token number
            return $id;
        } else {
            // return the token number with delimiters
            return $this->delim . $id . $this->delim;
        }
    }


    /**
    *
    * Set or re-set a token with specific information, overwriting any
    * previous rule name and rule options.
    *
    * @access public
    *
    * @param int $id The token number to reset.
    *
    * @param int $rule The rule name to use.
    *
    * @param array $options An associative array of options for the
    * token array element.  The keys and values are specific to the
    * rule, and may or may not be common to other rule options.  Typical
    * options keys are 'text' and 'type' but may include others.
    *
    * @return void
    *
    */

    function setToken($id, $rule, $options = array())
    {
        // reset the token
        $this->tokens[$id] = array(
            0 => $rule,
            1 => $options
        );
    }


    /**
    *
    * Load a rule parser class file.
    *
    * @access public
    *
    * @return bool True if loaded, false if not.
    *
    */

    function loadParseObj($rule)
    {
        $rule = ucwords(strtolower($rule));
        $file = $rule . '.php';
        $class = "Text_Wiki_Parse_$rule";

        if (! class_exists($class)) {
            $loc = $this->findFile('parse', $file);
            if ($loc) {
                // found the class
                include_once $loc;
            } else {
                // can't find the class
                $this->parseObj[$rule] = null;
                // can't find the class
                return $this->error(
                	"Parse rule '$rule' not found"
                );
            }
        }

        $this->parseObj[$rule] =& new $class($this);

    }


    /**
    *
    * Load a rule-render class file.
    *
    * @access public
    *
    * @return bool True if loaded, false if not.
    *
    */

    function loadRenderObj($format, $rule)
    {
        $format = ucwords(strtolower($format));
        $rule = ucwords(strtolower($rule));
        $file = "$format/$rule.php";
        $class = "Text_Wiki_Render_$format" . "_$rule";

        if (! class_exists($class)) {
            // load the class
            $loc = $this->findFile('render', $file);
            if ($loc) {
                // found the class
                include_once $loc;
            } else {
                // can't find the class
                return $this->error(
                	"Render rule '$rule' in format '$format' not found"
                );
            }
        }

        $this->renderObj[$rule] =& new $class($this);
    }


    /**
    *
    * Load a format-render class file.
    *
    * @access public
    *
    * @return bool True if loaded, false if not.
    *
    */

    function loadFormatObj($format)
    {
        $format = ucwords(strtolower($format));
        $file = $format . '.php';
        $class = "Text_Wiki_Render_$format";

        if (! class_exists($class)) {
            $loc = $this->findFile('render', $file);
            if ($loc) {
                // found the class
                include_once $loc;
            } else {
                // can't find the class
                return $this->error(
                	"Rendering format class '$class' not found"
                );
            }
        }

        $this->formatObj[$format] =& new $class($this);
    }


    /**
    *
    * Add a path to a path array.
    *
    * @access public
    *
    * @param string $type The path-type to add (parse or render).
    *
    * @param string $dir The directory to add to the path-type.
    *
    * @return void
    *
    */

    function addPath($type, $dir)
    {
        $dir = $this->fixPath($dir);
        if (! isset($this->path[$type])) {
            $this->path[$type] = array($dir);
        } else {
            array_unshift($this->path[$type], $dir);
        }
    }


    /**
    *
    * Get the current path array for a path-type.
    *
    * @access public
    *
    * @param string $type The path-type to look up (plugin, filter, or
    * template).  If not set, returns all path types.
    *
    * @return array The array of paths for the requested type.
    *
    */

    function getPath($type = null)
    {
        if (is_null($type)) {
            return $this->path;
        } elseif (! isset($this->path[$type])) {
            return array();
        } else {
            return $this->path[$type];
        }
    }


    /**
    *
    * Searches a series of paths for a given file.
    *
    * @param array $type The type of paths to search (template, plugin,
    * or filter).
    *
    * @param string $file The file name to look for.
    *
    * @return string|bool The full path and file name for the target file,
    * or boolean false if the file is not found in any of the paths.
    *
    */

    function findFile($type, $file)
    {
        // get the set of paths
        $set = $this->getPath($type);

        // start looping through them
        foreach ($set as $path) {
            $fullname = $path . $file;
            if (file_exists($fullname) && is_readable($fullname)) {
                return $fullname;
            }
        }

        // could not find the file in the set of paths
        return false;
    }


    /**
    *
    * Append a trailing '/' to paths, unless the path is empty.
    *
    * @access private
    *
    * @param string $path The file path to fix
    *
    * @return string The fixed file path
    *
    */

    function fixPath($path)
    {
        $len = strlen($this->_dirSep);

        if (! empty($path) &&
            substr($path, -1 * $len, $len) != $this->_dirSep)    {
            return $path . $this->_dirSep;
        } else {
            return $path;
        }
    }


    /**
    *
    * Simple error-object generator.
    *
    * @access public
    *
    * @param string $message The error message.
    *
    * @return object PEAR_Error
    *
    */

    function &error($message)
    {
    	if (! class_exists('PEAR_Error')) {
    		include_once 'PEAR.php';
    	}
    	return PEAR::throwError($message);
    }


    /**
    *
    * Simple error checker.
    *
    * @access public
    *
    * @param mixed $obj Check if this is a PEAR_Error object or not.
    *
    * @return bool True if a PEAR_Error, false if not.
    *
    */

    function isError(&$obj)
    {
    	return is_a($obj, 'PEAR_Error');
    }
}

?>
