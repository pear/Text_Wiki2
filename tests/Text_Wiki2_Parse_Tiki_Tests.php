<?php

require_once 'PHPUnit/Framework/TestCase.php';
require_once 'Text/Wiki2/Tiki.php';
require_once 'Text/Wiki2/Parse/Tiki/Anchor.php';
require_once 'Text/Wiki2/Parse/Tiki/Blockquote.php';
require_once 'Text/Wiki2/Parse/Tiki/Bold.php';
require_once 'Text/Wiki2/Parse/Tiki/Box.php';
require_once 'Text/Wiki2/Parse/Tiki/Break.php';
require_once 'Text/Wiki2/Parse/Tiki/Center.php';
require_once 'Text/Wiki2/Parse/Tiki/Code.php';
require_once 'Text/Wiki2/Parse/Tiki/Colortext.php';
require_once 'Text/Wiki2/Parse/Tiki/Deflist.php';
require_once 'Text/Wiki2/Parse/Tiki/Delimiter.php';
require_once 'Text/Wiki2/Parse/Tiki/Embed.php';
require_once 'Text/Wiki2/Parse/Tiki/Emphasis.php';
require_once 'Text/Wiki2/Parse/Tiki/Freelink.php';
require_once 'Text/Wiki2/Parse/Tiki/Heading.php';
require_once 'Text/Wiki2/Parse/Tiki/Horiz.php';
require_once 'Text/Wiki2/Parse/Tiki/Html.php';
require_once 'Text/Wiki2/Parse/Tiki/Image.php';
require_once 'Text/Wiki2/Parse/Tiki/Include.php';
require_once 'Text/Wiki2/Parse/Tiki/Interwiki.php';
require_once 'Text/Wiki2/Parse/Tiki/Italic.php';
require_once 'Text/Wiki2/Parse/Tiki/List.php';
require_once 'Text/Wiki2/Parse/Tiki/Newline.php';
require_once 'Text/Wiki2/Parse/Tiki/Page.php';
require_once 'Text/Wiki2/Parse/Tiki/Paragraph.php';
require_once 'Text/Wiki2/Parse/Tiki/Plugin.php';
require_once 'Text/Wiki2/Parse/Tiki/Prefilter.php';
require_once 'Text/Wiki2/Parse/Tiki/Preformatted.php';
require_once 'Text/Wiki2/Parse/Tiki/Raw.php';
require_once 'Text/Wiki2/Parse/Tiki/Revise.php';
require_once 'Text/Wiki2/Parse/Tiki/Smiley.php';
require_once 'Text/Wiki2/Parse/Tiki/Specialchar.php';
require_once 'Text/Wiki2/Parse/Tiki/Strong.php';
require_once 'Text/Wiki2/Parse/Tiki/Subscript.php';
require_once 'Text/Wiki2/Parse/Tiki/Superscript.php';
require_once 'Text/Wiki2/Parse/Tiki/Table.php';
require_once 'Text/Wiki2/Parse/Tiki/Tighten.php';
require_once 'Text/Wiki2/Parse/Tiki/Titlebar.php';
require_once 'Text/Wiki2/Parse/Tiki/Toc.php';
require_once 'Text/Wiki2/Parse/Tiki/Tt.php';
require_once 'Text/Wiki2/Parse/Tiki/Underline.php';
require_once 'Text/Wiki2/Parse/Tiki/Url.php';
require_once 'Text/Wiki2/Parse/Tiki/Wikilink.php';


class Text_Wiki2_Parse_Tiki_AllTests extends PHPUnit_Framework_TestSuite
{
    
    public static function suite()
    { 
        $suite = new PHPUnit_Framework_TestSuite('Text_Wiki2_Parse_Tiki_TestSuite');
        $suite->addTestSuite('Text_Wiki2_Parse_Tiki_Heading_Test');
        
        return $suite;
    }

}

class Text_Wiki2_Parse_Tiki_SetUp_Tests extends PHPUnit_Framework_TestCase
{

    protected function setUp()
    {
        $obj = Text_Wiki2::factory('Tiki');
        $testClassName = get_class($this);
        $ruleName = preg_replace('/Text_Wiki2_Parse_Tiki_(.+?)_Test/', '\\1', $testClassName);
        $this->className = 'Text_Wiki2_Parse_' . $ruleName;
        $this->t = new $this->className($obj);

        if (file_exists(dirname(__FILE__) . '/fixtures/tiki_syntax_to_test_' . strtolower($ruleName) . '.txt')) {
            $this->fixture = file_get_contents(dirname(__FILE__) . '/fixtures/tiki_syntax_to_test_' . strtolower($ruleName) . '.txt');
        } else {
            $this->fixture = file_get_contents(dirname(__FILE__) . '/fixtures/tiki_syntax.txt');
        }

        preg_match_all($this->t->regex, $this->fixture, $this->matches);
    }
    
}

class Text_Wiki2_Parse_Tiki_Heading_Test extends Text_Wiki2_Parse_Tiki_SetUp_Tests
{
    
    public function testTikiParseHeadingProcess()
    {
        $matches1 = array(
            0 => "
!! Heading 2

Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi vitae est sit amet metus consequat scelerisque at accumsan dolor. Quisque posuere, mauris a fermentum sagittis, sem quam blandit tortor, vitae ullamcorper nulla velit placerat lacus. Nullam rutrum quam id est convallis luctus. Vivamus et urna odio. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Ut at augue eget elit feugiat pretium.
                ",
            1 => "\n",
            2 => "!!",
            3 => "",
            4 => " Heading 2",
            5 => "

Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi vitae est sit amet metus consequat scelerisque at accumsan dolor. Quisque posuere, mauris a fermentum sagittis, sem quam blandit tortor, vitae ullamcorper nulla velit placerat lacus. Nullam rutrum quam id est convallis luctus. Vivamus et urna odio. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Ut at augue eget elit feugiat pretium.
"
        );

        $this->assertRegExp("/\n\d+? Heading 2\d+?\d+?$matches1[5]\d+?/", $this->t->process($matches1));

        $tokens = array(
            0 => array(0 => 'Heading', 1 => array('type' => 'start', 'level' => 2, 'text' =>  ' Heading 2', 'id' => 'toc0', 'collapse' => '')),
            1 => array(0 => 'Heading', 1 => array('type' => 'end', 'text' =>  ' Heading 2', 'level' => 2, 'collapse' =>  '', 'id' => 'toc0')),
            2 => array(0 => 'Heading', 1 => array('type' => 'startContent', 'id' => 'toc0', 'level' => 2, 'collapse' => '', 'text' =>  ' Heading 2')),
            3 => array(0 => 'Heading', 1 => array('type' => 'endContent', 'collapse' => '', 'level' => 2, 'id' => 'toc0', 'text' => ' Heading 2'))
        );

        $this->assertEquals(array_values($tokens), array_values($this->t->wiki->tokens));
    }
    
    public function testMediawikiParseHeadingRegex()
    {
        require_once dirname(__FILE__) . '/fixtures/test_tiki_heading_expected_matches.php';
        global $expectedHeadingMatches;

        $this->assertEquals($expectedHeadingMatches, $this->matches);
    }
    
}

?>
