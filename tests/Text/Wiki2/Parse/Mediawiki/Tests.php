<?php

require_once 'PHPUnit/Framework/TestCase.php';
require_once 'Text/Wiki2/Mediawiki.php';
require_once 'Text/Wiki2/Parse/Mediawiki/Break.php';
require_once 'Text/Wiki2/Parse/Mediawiki/Code.php';
require_once 'Text/Wiki2/Parse/Mediawiki/Comment.php';
require_once 'Text/Wiki2/Parse/Mediawiki/Deflist.php';
require_once 'Text/Wiki2/Parse/Mediawiki/Emphasis.php';
require_once 'Text/Wiki2/Parse/Mediawiki/Heading.php';
require_once 'Text/Wiki2/Parse/Mediawiki/List.php';
require_once 'Text/Wiki2/Parse/Mediawiki/Newline.php';
require_once 'Text/Wiki2/Parse/Mediawiki/Preformatted.php';
require_once 'Text/Wiki2/Parse/Mediawiki/Raw.php';
require_once 'Text/Wiki2/Parse/Mediawiki/Redirect.php';
require_once 'Text/Wiki2/Parse/Mediawiki/Subscript.php';
require_once 'Text/Wiki2/Parse/Mediawiki/Superscript.php';
require_once 'Text/Wiki2/Parse/Mediawiki/Table.php';
require_once 'Text/Wiki2/Parse/Mediawiki/Tt.php';
require_once 'Text/Wiki2/Parse/Mediawiki/Url.php';
require_once 'Text/Wiki2/Parse/Mediawiki/Wikilink.php';

// default parse rules used by Mediawiki parser
require_once 'Text/Wiki2/Parse/Default/Horiz.php';

class Text_Wiki2_Parse_Mediawiki_AllTests extends PHPUnit_Framework_TestSuite
{
    
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Text_Wiki2_Render_Mediawiki_TestSuite');
        $suite->addTestSuite('Text_Wiki2_Parse_Mediawiki_Break_Test');
        /*$suite->addTestSuite('Text_Wiki2_Parse_Mediawiki_Code_Test');
        $suite->addTestSuite('Text_Wiki2_Parse_Mediawiki_Comment_Test');*/
        $suite->addTestSuite('Text_Wiki2_Parse_Mediawiki_Deflist_Test');
        $suite->addTestSuite('Text_Wiki2_Parse_Mediawiki_Emphasis_Test');
        $suite->addTestSuite('Text_Wiki2_Parse_Mediawiki_Heading_Test');
        $suite->addTestSuite('Text_Wiki2_Parse_Mediawiki_Horiz_Test');
        $suite->addTestSuite('Text_Wiki2_Parse_Mediawiki_List_Test');
        //$suite->addTestSuite('Text_Wiki2_Parse_Mediawiki_Newline_Test');
        $suite->addTestSuite('Text_Wiki2_Parse_Mediawiki_Preformatted_Test');
        $suite->addTestSuite('Text_Wiki2_Parse_Mediawiki_Raw_Test');
        $suite->addTestSuite('Text_Wiki2_Parse_Mediawiki_Redirect_Test');
        /*$suite->addTestSuite('Text_Wiki2_Parse_Mediawiki_Subscript_Test');
        $suite->addTestSuite('Text_Wiki2_Parse_Mediawiki_Superscript_Test');*/
        $suite->addTestSuite('Text_Wiki2_Parse_Mediawiki_Table_Test');
        //$suite->addTestSuite('Text_Wiki2_Parse_Mediawiki_Tt_Test');
        $suite->addTestSuite('Text_Wiki2_Parse_Mediawiki_Url_Test');
        $suite->addTestSuite('Text_Wiki2_Parse_Mediawiki_Wikilink_Test');
        
        return $suite;
    }
    
}

class Text_Wiki2_Parse_Mediawiki_SetUp_Tests extends PHPUnit_Framework_TestCase
{

    protected function setUp()
    {
        $obj = Text_Wiki2::factory('Mediawiki');
        $testClassName = get_class($this);
        $ruleName = preg_replace('/Text_Wiki2_Parse_Mediawiki_(.+?)_Test/', '\\1', $testClassName);
        $this->className = 'Text_Wiki2_Parse_Mediawiki_' . $ruleName;
        $this->t = new $this->className($obj);

        if (file_exists(dirname(__FILE__) . '/fixtures/mediawiki_syntax_to_test_' . strtolower($ruleName) . '.txt')) {
            $this->fixture = file_get_contents(dirname(__FILE__) . '/fixtures/mediawiki_syntax_to_test_' . strtolower($ruleName) . '.txt');
        } else {
            $this->fixture = file_get_contents(dirname(__FILE__) . '/fixtures/mediawiki_syntax.txt');
        }

        preg_match_all($this->t->regex, $this->fixture, $this->matches);
    }
    
}

class Text_Wiki2_Parse_Mediawiki_Break_Test extends Text_Wiki2_Parse_Mediawiki_SetUp_Tests
{
    
    public function testMediawikiParseBreakProcess()
    {
        $matches1 = array(0 => '<br />');
        $matches2 = array(0 => '<br   />');
        
        $this->assertRegExp('/\d+?/', $this->t->process($matches1));
        $this->assertRegExp('/\d+?/', $this->t->process($matches2));

        $tokens = array(0 => array(0 => 'Break', 1 => array()),
                        1 => array(0 => 'Break', 1 => array()));

        $this->assertEquals(array_values($tokens), array_values($this->t->wiki->tokens));
    }
    
    public function testMediawikiParseBreakRegex()
    {
        $expectedResult = array(0 => array(0 => '<br />', 1 => '<br   />'));
        $this->assertEquals($expectedResult, $this->matches);
    }
    
}

class Text_Wiki2_Parse_Mediawiki_Deflist_Test extends Text_Wiki2_Parse_Mediawiki_SetUp_Tests
{
    
    public function testMediawikiParseDeflistProcess()
    {
        $matches1 = array(
            0 => "\n;Definition lists\n;item : definition\n;semicolon plus term\n:colon plus definition\n",
            1 => ";Definition lists\n;item : definition\n;semicolon plus term\n:colon plus definition\n",
        );

        $this->assertRegExp("/\d+?\d+?Definition lists\d+?\d+?item\d+?\d+?definition\d+?\d+?semicolon plus term\d+?\d+?colon plus definition\d+?\d+?/", $this->t->process($matches1));

        $tokens = array(
            2 => array(0 => 'Deflist', 1 => array('type' => 'list_start', 'level' => 0)),
            3 => array(0 => 'Deflist', 1 => array('type' => 'term_start', 'level' => 1, 'count' => 0, 'first' => true)),
            4 => array(0 => 'Deflist', 1 => array('type' => 'term_end', 'level' => 1, 'count' => 0)),
            5 => array(0 => 'Deflist', 1 => array('type' => 'term_start', 'level' => 1, 'count' => 1, 'first' => false)),
            6 => array(0 => 'Deflist', 1 => array('type' => 'term_end', 'level' => 1, 'count' => 1)),
            7 => array(0 => 'Deflist', 1 => array('type' => 'narr_start', 'level' => 1, 'count' => 2, 'first' => false)),
            8 => array(0 => 'Deflist', 1 => array('type' => 'narr_end', 'level' => 1, 'count' => 2)),
            9 => array(0 => 'Deflist', 1 => array('type' => 'term_start', 'level' => 1, 'count' => 3, 'first' => false)),
            10 => array(0 => 'Deflist', 1 => array('type' => 'term_end', 'level' => 1, 'count' => 3)),
            11 => array(0 => 'Deflist', 1 => array('type' => 'narr_start', 'level' => 1, 'count' => 4, 'first' => false)),
            12 => array(0 => 'Deflist', 1 => array('type' => 'narr_end', 'level' => 1, 'count' => 4)),
            13 => array(0 => 'Deflist', 1 => array('type' => 'list_end', 'level' => 0))
        );

        $this->assertEquals(array_values($tokens), array_values($this->t->wiki->tokens));
    }
    
    public function testMediawikiParseDeflistRegex()
    {
        $expectedResult = array(
            0 => array(
                0 => "
;Definition lists
;item : definition
;semicolon plus term
:colon plus definition
",
            ),
            1 => array(
                0 => ";Definition lists
;item : definition
;semicolon plus term
:colon plus definition
",
            ),
        );
        $this->assertEquals($expectedResult, $this->matches);
    }
    
}


class Text_Wiki2_Parse_Mediawiki_Emphasis_Test extends PHPUnit_Framework_TestCase 
{

    public function testMediawikiParseEmphasisParse()
    {
        $obj = $this->getMock(
            'Text_Wiki2_Parse_Mediawiki_Emphasis',
            array('process'),
            array(),
            'Text_Wiki2_Parse_Emphasis_Parse_Mock',
            false
        );

        $obj->wiki         = $this->getMock('Text_Wiki2');
        $obj->wiki->source = file_get_contents(dirname(__FILE__) . '/fixtures/mediawiki_syntax.txt');

        $lines = explode("\n", $obj->wiki->source);
        $i = count($lines);
        $obj->expects($this->exactly($i))->method('process');

        $obj->parse();
    }

    public function testMediawikiParseEmphasisProcess()
    {
        $textwiki = Text_Wiki2::factory('Mediawiki');
        $obj = new Text_Wiki2_Parse_Mediawiki_Emphasis($textwiki);

        $lines = array(
            "'''Bold text''' and ''italic text'' and even '''''bold italic text'''''",
            "'''Bold text''' and ''italic text'' and even '''''bold italic text''''' some text '''bold then ''italic'' then bold''' more text ''italic then '''bold''' then italic again'' some text '''''bold and italic'''''",
            "'''''bold and italic''' and italic''",
            "''italic and '''bold and italic'''''"
        );

        foreach ($lines as $line) {
            $obj->process($line);
        }

        $expectedResult = array(
            14 => array(0 => 'Strong', 1 => array('type' => 'start')),
            15 => array(0 => 'Strong', 1 => array('type' => 'end')),
            16 => array(0 => 'Emphasis', 1 => array('type' => 'start')),
            17 => array(0 => 'Emphasis', 1 => array('type' => 'end')),
            18 => array(0 => 'Emphasis', 1 => array('type' => 'start')),
            19 => array(0 => 'Strong', 1 => array('type' => 'start')),
            20 => array(0 => 'Strong',1 => array('type' => 'end')),
            21 => array(0 => 'Emphasis', 1 => array('type' => 'end')),
            22 => array(0 => 'Strong', 1 => array('type' => 'start')),
            23 => array(0 => 'Strong', 1 => array('type' => 'end')),
            24 => array(0 => 'Emphasis', 1 => array('type' => 'start')),
            25 => array(0 => 'Emphasis', 1 => array('type' => 'end')),
            26 => array(0 => 'Emphasis', 1 => array('type' => 'start')),
            27 => array(0 => 'Strong', 1 => array('type' => 'start')),
            28 => array(0 => 'Strong', 1 => array('type' => 'end')),
            29 => array(0 => 'Emphasis', 1 => array('type' => 'end')),
            30 => array(0 => 'Strong', 1 => array('type' => 'start')),
            31 => array(0 => 'Emphasis', 1 => array('type' => 'start')),
            32 => array(0 => 'Emphasis', 1 => array('type' => 'end')),
            33 => array(0 => 'Strong', 1 => array('type' => 'end')),
            34 => array(0 => 'Emphasis', 1 => array('type' => 'start')),
            35 => array(0 => 'Strong', 1 => array('type' => 'start')),
            36 => array(0 => 'Strong', 1 => array('type' => 'end')),
            37 => array(0 => 'Emphasis', 1 => array('type' => 'end')),
            38 => array(0 => 'Emphasis', 1 => array('type' => 'start')),
            39 => array(0 => 'Strong', 1 => array('type' => 'start')),
            40 => array(0 => 'Strong', 1 => array('type' => 'end')),
            41 => array(0 => 'Emphasis', 1 => array('type' => 'end')),
            42 => array(0 => 'Emphasis', 1 => array('type' => 'start')),
            43 => array(0 => 'Strong', 1 => array('type' => 'start')),
            44 => array(0 => 'Strong', 1 => array('type' => 'end')),
            45 => array(0 => 'Emphasis', 1 => array('type' => 'end')),
            46 => array(0 => 'Emphasis', 1 => array('type' => 'start')),
            47 => array(0 => 'Strong', 1 => array('type' => 'start')),
            48 => array(0 => 'Strong', 1 => array('type' => 'end')),
            49 => array(0 => 'Emphasis', 1 => array('type' => 'end')),
        );
        
        $this->assertEquals(array_values($expectedResult), array_values($obj->wiki->tokens));
    }
    
}

class Text_Wiki2_Parse_Mediawiki_Heading_Test extends Text_Wiki2_Parse_Mediawiki_SetUp_Tests
{
    
    public function testMediawikiParseHeadingProcess()
    {
        $matches1 = array(0 => "======Level 6 heading======", 1 => '======', 2 => 'Level 6 heading');
        $matches2 = array(0 => "=Level 1 heading=", 1 => '=', 2 => 'Level 1 heading');
        $matches3 = array(0 => "==Level 2 heading==", 1 => '==', 2 => 'Level 2 heading');

        $this->assertRegExp("/\d+?Level 6 heading\d+?\n/", $this->t->process($matches1));
        $this->assertRegExp("/\d+?Level 1 heading\d+?\n/", $this->t->process($matches2));
        $this->assertRegExp("/\d+?Level 2 heading\d+?\n/", $this->t->process($matches3));

        $tokens = array(
            0 => array(0 => 'Heading', 1 => array('type' => 'start', 'level' => 6, 'text' => 'Level 6 heading', 'id' => 'toc0')),
            1 => array(0 => 'Heading', 1 => array('type' => 'end', 'level' => 6)),
            2 => array(0 => 'Heading', 1 => array('type' => 'start', 'level' => 1, 'text' => 'Level 1 heading', 'id' => 'toc1')),
            3 => array(0 => 'Heading', 1 => array('type' => 'end', 'level' => 1)),
            4 => array(0 => 'Heading', 1 => array('type' => 'start', 'level' => 2, 'text' => 'Level 2 heading', 'id' => 'toc2')),
            5 => array(0 => 'Heading', 1 => array('type' => 'end', 'level' => 2))
        );
        
        $this->assertEquals(array_values($tokens), array_values($this->t->wiki->tokens));
    }
    
    public function testMediawikiParseHeadingRegex()
    {
        $expectedResult = array(
            0 => array(0 => "=Level 1 heading=", 1 => "==Level 2 heading==", 2 => "==Level 2 heading==", 3 => "===Level 3 heading===", 4 => "====Level 4 heading====", 5 => "===Level 3 heading===", 6 => "===Level 3 heading===", 7 => "=====Level 5 heading=====", 8 => "======Level 6 heading======"),
            1 => array(0 => '=', 1 => '==', 2 => '==', 3 => '===', 4 => '====', 5 => '===', 6 => '===', 7 => '=====', 8 => '======'),
            2 => array(0 => 'Level 1 heading', 1 => 'Level 2 heading', 2 => 'Level 2 heading', 3 => 'Level 3 heading', 4 => 'Level 4 heading', 5 => 'Level 3 heading', 6 => 'Level 3 heading', 7 => 'Level 5 heading', 8 => 'Level 6 heading')
        );
        $this->assertEquals($expectedResult, $this->matches);
    }
    
}

// Mediawiki parse uses horiz rule from default parser
class Text_Wiki2_Parse_Mediawiki_Horiz_Test extends Text_Wiki2_Parse_Mediawiki_SetUp_Tests
{
    
    public function testMediawikiParseHorizProcess()
    {
        $matches1 = array(0 => '----', 1 => '----');
        $matches2 = array(0 => '------', 1 => '------');

        $this->assertRegExp("/\d+?/", $this->t->process($matches1));
        $this->assertRegExp("/\d+?/", $this->t->process($matches2));

        $tokens = array(
            0 => array(0 => 'Horiz', array()),
            1 => array(0 => 'Horiz', array()),
        );
        
        $this->assertEquals(array_values($tokens), array_values($this->t->wiki->tokens));
    }
    
    public function testMediawikiParseHeadingRegex()
    {
        $expectedResult = array(
            0 => array(0 => '----', 1 => '------'),
            1 => array(0 => '----', 1 => '------'),
        );
        $this->assertEquals($expectedResult, $this->matches);
    }
    
}

class Text_Wiki2_Parse_Mediawiki_List_Test extends Text_Wiki2_Parse_Mediawiki_SetUp_Tests
{
     
    public function testMediawikiParseListProcess()
    {
        $matches1 = array(
            1 => "
* List example
** List example
**# List example
**# List example
*** List example
**** List example
** List example
",
            2 => "* List example
** List example
**# List example
**# List example
*** List example
**** List example
** List example
"
        );

        $this->assertRegExp("/\d+?\d+? List example\d+?\d+?\d+? List example\d+?\d+?\d+? List example\d+?\d+? List example\d+?\d+? List example\d+?\d+?\d+? List example\d+?\d+?\d+?\d+? List example\d+?\d+?\d+?/", $this->t->process($matches1));

        $tokens = array(
            432 => array(0 => 'List', 1 => array('type' => 'bullet_list_start', 'level' => 1)),
            433 => array(0 => 'List', 1 => array('type' => 'bullet_item_start', 'level' => 1, 'count' => 0, 'first' => true)),
            434 => array(0 => 'List', 1 => array('type' => 'bullet_item_end', 'level' => 1, 'count' => 0)),
            435 => array(0 => 'List', 1 => array('type' => 'bullet_list_start', 'level' => 2)),
            436 => array(0 => 'List', 1 => array('type' => 'bullet_item_start', 'level' => 2, 'count' => 0, 'first' => false)),
            437 => array(0 => 'List', 1 => array('type' => 'bullet_item_end', 'level' => 2, 'count' => 0)),
            438 => array(0 => 'List', 1 => array('type' => 'number_list_start', 'level' => 3)),
            439 => array(0 => 'List', 1 => array('type' => 'number_item_start', 'level' => 3, 'count' => 0, 'first' => false)),
            440 => array(0 => 'List', 1 => array('type' => 'number_item_end', 'level' => 3, 'count' => 0)),
            441 => array(0 => 'List', 1 => array('type' => 'number_item_start', 'level' => 3, 'count' => 1, 'first' => false)),
            442 => array(0 => 'List', 1 => array('type' => 'number_item_end', 'level' => 3, 'count' => 1)),
            443 => array(0 => 'List', 1 => array('type' => 'bullet_item_start', 'level' => 3, 'count' => 2, 'first' => false)),
            444 => array(0 => 'List', 1 => array('type' => 'bullet_item_end', 'level' => 3, 'count' => 2)),
            445 => array(0 => 'List', 1 => array('type' => 'bullet_list_start', 'level' => 4)),
            446 => array(0 => 'List', 1 => array('type' => 'bullet_item_start', 'level' => 4, 'count' => 0, 'first' => false)),
            447 => array(0 => 'List', 1 => array('type' => 'bullet_item_end', 'level' => 4, 'count' => 0)),
            448 => array(0 => 'List', 1 => array('type' => 'bullet_list_end', 'level' => 3)),
            449 => array(0 => 'List', 1 => array('type' => 'number_list_end', 'level' => 2)),
            450 => array(0 => 'List', 1 => array('type' => 'bullet_item_start', 'level' => 2, 'count' => 1, 'first' => false)),
            451 => array(0 => 'List', 1 => array('type' => 'bullet_item_end', 'level' => 2, 'count' => 1)),
            452 => array(0 => 'List', 1 => array('type' => 'bullet_list_end', 'level' => 1)),
            453 => array(0 => 'List', 1 => array('type' => 'bullet_list_end', 'level' => 0)),
        );
        
        $this->assertEquals(array_values($tokens), array_values($this->t->wiki->tokens));
    }
    
    public function testMediawikiParseListRegex()
    {
        $expectedResult = array(
            0 => array(
                0 => "
* List
* List
** List
** List
*** List
* List
* List
",
                1 => "
# List
# List
## List
## List
### List
# List
# List
",
                2 => "
* List example
** List example
**# List example
**# List example
*** List example
**** List example
*# List example
*# List example
** List example
* List example
** List example
** List example
**# List example
",
            ),
            1 => array(
                0 => "* List
* List
** List
** List
*** List
* List
* List
",
                1 => "# List
# List
## List
## List
### List
# List
# List
",
                2 => "* List example
** List example
**# List example
**# List example
*** List example
**** List example
*# List example
*# List example
** List example
* List example
** List example
** List example
**# List example
"
            )
        );

        $this->assertEquals($expectedResult, $this->matches);
    }
    
}

class Text_Wiki2_Parse_Mediawiki_Preformatted_Test extends Text_Wiki2_Parse_Mediawiki_SetUp_Tests
{
     
    public function testMediawikiParsePreformattedProcess()
    {
        $matches1 = array(0 => "<pre>pre tag without line break</pre>", 1 => 'pre tag without line break');
        // not sure why Text_Wiki2_Parse_Preformatted uses $matches[2]
        $matches2 = array(0 => "<pre>pre tag without line break</pre>", 1 => 'pre tag without line break', 2 => 'some text');
        
        $this->assertRegExp("/\d+?/", $this->t->process($matches1));
        $this->assertRegExp("/\d+?/", $this->t->process($matches2));

        $tokens = array(
            0 => array(0 => 'Preformatted', 1 => array('text' => 'pre tag without line break')),
            1 => array(0 => 'Preformatted', 1 => array('text' => 'some text'))
        );
        
        $this->assertEquals(array_values($tokens), array_values($this->t->wiki->tokens));
    }
    
    public function testMediawikiParsePreformattedRegex()
    {
        $expectedResult = array(
            0 => array(
                0 => "<pre>
The pre tag ignores [[Wiki]] ''markup''.
It also doesn't     reformat text.
It still interprets special characters:
 &amp;rarr;
</pre>",
                1 => "<pre>pre tag without line break</pre>",
                2 => "<pre>some ''text'' without '''wiki''' parsing</pre>"
            ),
            1 => array(
                0 => "The pre tag ignores [[Wiki]] ''markup''.
It also doesn't     reformat text.
It still interprets special characters:
 &amp;rarr;",
                1 => "pre tag without line break",
                2 => "some ''text'' without '''wiki''' parsing",
            ),
            2 => array(0 => '', 1 => '', 2 => '')
        );

        $this->assertEquals($expectedResult, $this->matches);
    }
    
}

class Text_Wiki2_Parse_Mediawiki_Raw_Test extends Text_Wiki2_Parse_Mediawiki_SetUp_Tests
{
     
    public function testMediawikiParseRawProcess()
    {
        $matches1 = array(0 => "<nowiki>nowiki tag without break line</nowiki>", 1 => 'nowiki tag without line break');
        
        $this->assertRegExp("/\d+?/", $this->t->process($matches1));

        $tokens = array(
            0 => array(0 => 'Raw', 1 => array('text' => 'nowiki tag without line break')),
        );
        
        $this->assertEquals(array_values($tokens), array_values($this->t->wiki->tokens));
    }
    
    public function testMediawikiParseRawRegex()
    {
        $expectedResult = array(
            0 => array(
                0 => "<nowiki>
The nowiki tag ignores [[Wiki]] ''markup''.
It reformats text by removing newlines 
and multiple spaces.
It still interprets special
characters: &rarr;
</nowiki>",
                1 => "<nowiki>''ignores markup''</nowiki>",
            ),
            1 => array(
                0 => "The nowiki tag ignores [[Wiki]] ''markup''.
It reformats text by removing newlines 
and multiple spaces.
It still interprets special
characters: &rarr;",
                1 => "''ignores markup''",
            )
        );

        $this->assertEquals($expectedResult, $this->matches);
    }
    
}

class Text_Wiki2_Parse_Mediawiki_Redirect_Test extends Text_Wiki2_Parse_Mediawiki_SetUp_Tests
{
    
    public function testMediawikiParseRedirectProcess()
    {
        $matches1 = array(0 => "#REDIRECT [[Some page name]]", 1 => 'Some page name');
        $matches2 = array(0 => "#redirect [[Other page name]]", 1 => 'Other page name');

        $this->assertRegExp("/\d+?Some page name\d+?/", $this->t->process($matches1));
        $this->assertRegExp("/\d+?Other page name\d+?/", $this->t->process($matches2));

        $tokens = array(
            0 => array(0 => 'Redirect', 1 => array('type' => 'start', 'text' => 'Some page name')),
            1 => array(0 => 'Redirect', 1 => array('type' => 'end')),
            2 => array(0 => 'Redirect', 1 => array('type' => 'start', 'text' => 'Other page name')),
            3 => array(0 => 'Redirect', 1 => array('type' => 'end')),
        );
        
        $this->assertEquals(array_values($tokens), array_values($this->t->wiki->tokens));
    }
    
    public function testMediawikiParseRedirectRegex()
    {
        $expectedResult = array(
            0 => array(0 => "#REDIRECT [[Some page name]]", 1 => "#redirect [[Other page name]]"),
            1 => array(0 => 'Some page name', 1 => 'Other page name'),
        );
        $this->assertEquals($expectedResult, $this->matches);
    }
    
}

class Text_Wiki2_Parse_Mediawiki_Table_Test extends Text_Wiki2_Parse_Mediawiki_SetUp_Tests
{
    
    public function testMediawikiParseTableProcess()
    {

        $matches = array(
            0 => '{| 
| A || B
|- 
| C || D 
|}',
            1 => ' 
',
            2 => '',
            3 => '| A || B
|- 
| C || D 
'
        );

        $this->assertRegExp("/\d+?\d+?\d+? A \d+?\d+? B\d+?\d+?\d+?\d+? C \d+?\d+? D \d+?\d+?\d+?/", $this->t->process($matches));

        $tokens = array(
            487 => array(0 => 'Table', 1 => array('type' => 'cell_start', 'attr' => '', 'span' => 1, 'rowspan' => 1, 'order' => 0)),
            488 => array(0 => 'Table', 1 => array('type' => 'cell_end', 'attr' => '', 'span' => 1, 'rowspan' => 1, 'order' => 0)),
            489 => array(0 => 'Table', 1 => array('type' => 'cell_start', 'attr' => '', 'span' => 1, 'rowspan' => 1, 'order' => 1)),
            490 => array(0 => 'Table', 1 => array('type' => 'cell_end', 'attr' => '', 'span' => 1, 'rowspan' => 1, 'order' => 1)),
            491 => array(0 => 'Table', 1 => array('type' => 'row_start', 'order' => 0, 'cols' => 2)),
            492 => array(0 => 'Table', 1 => array('type' => 'row_end', 'order' => 0, 'cols' => 2)),
            493 => array(0 => 'Table', 1 => array('type' => 'cell_start', 'attr' => '', 'span' => 1, 'rowspan' => 1, 'order' => 0)),
            494 => array(0 => 'Table', 1 => array('type' => 'cell_end', 'attr' => '', 'span' => 1, 'rowspan' => 1, 'order' => 0)),
            495 => array(0 => 'Table', 1 => array('type' => 'cell_start', 'attr' => '', 'span' => 1, 'rowspan' => 1, 'order' => 1)),
            496 => array(0 => 'Table', 1 => array('type' => 'cell_end', 'attr' => '', 'span' => 1, 'rowspan' => 1, 'order' => 1)),
            497 => array(0 => 'Table', 1 => array('type' => 'row_start', 'order' => 1, 'cols' => 2)),
            498 => array(0 => 'Table', 1 => array('type' => 'row_end', 'order' => 1, 'cols' => 2)),
            499 => array(0 => 'Table', 1 => array('type' => 'table_start', 'level' => 0, 'rows' => 2, 'cols' => 2)),
            500 => array(0 => 'Table', 1 => array('type' => 'table_end', 'level' => 0, 'rows' => 2, 'cols' => 2))
        );

        $this->assertEquals(array_values($tokens), array_values($this->t->wiki->tokens));
    }
    
    public function testMediawikiParseTableRegex()
    {

        $expectedResult = array(
            0 => array(
                0 => "{| 
| A || B
|- 
| C || D 
|}",
            ),
            1 => array(0 => " \n"),
            2 => array(0 => ''),
            3 => array(
                0 => "| A || B
|- 
| C || D 
"
            ),
            4 => array(
                0 => ''
            )
        );

        $this->assertEquals($expectedResult, $this->matches);
    }
    
}


class Text_Wiki2_Parse_Mediawiki_Wikilink_Test extends Text_Wiki2_Parse_Mediawiki_SetUp_Tests
{
    
    public function testMediawikiParseWikilinkProcessWithSpaceUnderscoreFalse()
    {
        $this->t->conf['spaceUnderscore'] = false;

        $matches1 = array(0 => '[[convallis elementum]]', 1 => '', 2 => '', 3 => 'convallis elementum', 4 => '', 5 => '', 6 => '');
        $matches2 = array(0 => '[[Etiam]]', 1 => '', 2 => '', 3 => 'Etiam', 4 => '', 5 => '', 6 => '');
        $matches3 = array(0 => '[[pt:Language link]]', 1 => '', 2 => 'pt:', 3 => 'Language link', 4 => '', 5 => '', 6 => '');
        $matches4 = array(0 => '[[Image:some image]]', 1 => '', 2 => 'Image:', 3 => 'some image', 4 => '', 5 => '', 6 => '');
        $matches5 = array(0 => '[[Etiam|description text]]', 1 => '', 2 => '', 3 => 'Etiam', 4 => '', 5 => 'description text', 6 => '');

        $this->assertRegExp("/\d+?/", $this->t->process($matches1));
        $this->assertRegExp("/\d+?/", $this->t->process($matches2));
        $this->assertRegExp("/\d+?/", $this->t->process($matches3));
        $this->assertRegExp("/\d+?/", $this->t->process($matches4));
        $this->assertRegExp("/\d+?/", $this->t->process($matches5));

        $tokens = array(
            0 => array(0 => 'Wikilink', 1 => array('page' => 'convallis elementum', 'anchor' => '', 'text' => 'convallis elementum')),
            1 => array(0 => 'Wikilink', 1 => array('page' => 'Etiam', 'anchor' => '', 'text' => 'Etiam')),
            2 => array(0 => 'Wikilink', 1 => array('page' => 'pt:Language link', 'anchor' => '', 'text' => 'pt:Language link')),
            3 => array(0 => 'Image', 1 => array('src' => 'some image', 'attr' => array('alt' => 'some image'))),
            4 => array(0 => 'Wikilink', 1 => array('page' => 'Etiam', 'anchor' => '', 'text' => 'description text')),
        );
        
        $this->assertEquals(array_values($tokens), array_values($this->t->wiki->tokens));
    }
     
    public function testMediawikiParseWikilinkProcessWithSpaceUnderscoreTrue()
    {
        $this->t->conf['spaceUnderscore'] = true;

        $matches1 = array(0 => '[[convallis elementum]]', 1 => '', 2 => '', 3 => 'convallis elementum', 4 => '', 5 => '', 6 => '');

        $this->assertRegExp("/\d+?/", $this->t->process($matches1));

        $tokens = array(
            0 => array(0 => 'Wikilink', 1 => array('page' => 'convallis_elementum', 'anchor' => '', 'text' => 'convallis elementum')),
        );
        
        $this->assertEquals(array_values($tokens), array_values($this->t->wiki->tokens));
    }
    
    public function testMediawikiParseWikilinkRegex()
    {
        require_once(dirname(__FILE__) . '/fixtures/test_mediawiki_wikilink_expected_matches.php');
        global $expectedWikilinkMatches;
        $fixture = file_get_contents(dirname(__FILE__) . '/fixtures/mediawiki_syntax_to_test_wikilink.txt');
        preg_match_all($this->t->regex, $fixture, $matches);
        
        $this->assertEquals($expectedWikilinkMatches, $matches);
    }
    
}

class Text_Wiki2_Parse_Mediawiki_Url_Test extends PHPUnit_Framework_TestCase
{

    protected function setUp()
    {
        $textWiki = new Text_Wiki2('Mediawiki');
        $this->obj = new Text_Wiki2_Parse_Url($textWiki);
    }

    public function testMediawikiParseUrlParse()
    {
        // for some weird reason I was unable to mock this class calling the constructor that is why to
        // test the regular expression I'm testing the tokens created (instead of testing many times each process function was called)
        $this->obj->wiki->source = file_get_contents(dirname(__FILE__) . '/fixtures/mediawiki_syntax.txt');
        $this->obj->parse();

        $tokens = array(
            1 => array(0 => 'Url', 1 => array('type' => 'descr', 'href' => 'http://www.example.com', 'text' => 'See the example site')),
            2 => array(0 => 'Url', 1 => array('type' => 'descr', 'href' => 'http://exemple.com/index.php', 'text' => 'consectetur adipiscing')),
            3 => array(0 => 'Url', 1 => array('type' => 'descr', 'href' => 'http://exemple.com/index.php#anchor', 'text' => 'Pellentesque')),
            4 => array(0 => 'Url', 1 => array('type' => 'descr', 'href' => 'http://www.somelink.com/index.php', 'text' => 'http://www.somelink.com/index.php')),
            5 => array(0 => 'Url', 1 => array('type' => 'inline', 'href' => 'http://example.com/index.php', 'text' => 'http://example.com/index.php'))
        );

        $this->assertEquals(array_values($tokens), array_values($this->obj->wiki->tokens));
    }

/*    public function testMediawikiParseUrlParseWithMocking()
    {
        // NOT WORKING: unable to mock the class Text_Wiki2_Parse_Url using its constructor
        $textWiki = Text_Wiki2::factory('Mediawiki');
        $obj = $this->getMock('Text_Wiki2_Parse_Url',
            array('process', 'processWithoutProtocol', 'processInlineEmail', 'processFootnote', 'processOrdinary', 'processDescr'),
            array($textWiki),
        );
        $obj->expects($this->once())->method('process');
        $obj->expects($this->never())->method('processWithoutProtocol');
        $obj->expects($this->never())->method('processInlineEmail');
        $obj->expects($this->never())->method('processFootnote');
        $obj->expects($this->exactly(2))->method('processOrdinary');
        $obj->expects($this->exactly(5))->method('processDescr');
        $obj->wiki->source = file_get_contents(dirname(__FILE__) . '/fixtures/mediawiki_syntax.txt');
        $obj->parse();
    }*/

    public function testProcess()
    {
        $this->markTestIncomplete('Test incomplete');
    }

    public function testProcessWithoutProtocol()
    {
        $this->markTestIncomplete('Test incomplete');
    }

    public function testProcessInlineEmail()
    {
        $this->markTestIncomplete('Test incomplete');
    }

    public function testProcessFootnote()
    {
        $this->markTestIncomplete('Test incomplete');
    }

    public function testProcessOrdinary()
    {
        $this->markTestIncomplete('Test incomplete');
    }

    public function testProcessDescr()
    {
        $this->markTestIncomplete('Test incomplete');
    }
}

?>
