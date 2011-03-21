<?php

require_once 'PHPUnit/Framework/TestCase.php';
require_once 'Text/Wiki2.php';

class Text_Wiki2_Tests extends PHPUnit_Framework_TestCase
{
    protected $obj;

    protected function setUp()
    {
        $this->obj = Text_Wiki2::factory();

/*
        $this->obj->renderConf = array();
        $this->obj->parseConf = array();
        $this->obj->formatConf = array();
        $this->obj->rules = array('Prefilter', 'Delimiter', 'Code', 'Function', 'Html', 'Raw', 'Include');
        $this->obj->disable = array('Html', 'Include', 'Embed');
        $this->obj->path = array('parse' => array(), 'render' => array());
*/

        $this->sourceText = 'A very \'\'simple\'\' \'\'\'source\'\'\' text. Not sure [[how]] to [http://example.com improve] the transform() tests.' . "\n";
        $this->tokens = array(
            0 => array(0 => 'Heading', 1 => array('type' => 'start', 'level' => 6, 'text' => 'Level 6 heading', 'id' => 'toc0')),
            1 => array(0 => 'Heading', 1 => array('type' => 'end', 'level' => 6)),
            2 => array(0 => 'Heading', 1 => array('type' => 'start', 'level' => 1, 'text' => 'Level 1 heading', 'id' => 'toc1')),
            3 => array(0 => 'Heading', 1 => array('type' => 'end', 'level' => 1)),
            4 => array(0 => 'Heading', 1 => array('type' => 'start', 'level' => 2, 'text' => 'Level 2 heading', 'id' => 'toc2')),
            5 => array(0 => 'Heading', 1 => array('type' => 'end', 'level' => 2)),
            6 => array(0 => 'Break', 1 => array()),
            7 => array(0 => 'Break', 1 => array())
        );
        $this->_countRulesTokens = array('Heading' => 6, 'Break' => 2);
    }

    protected function tearDown()
    {
        unset($this->obj);
    }

    public function testSingletonOfSameParserShouldReturnSameObject()
    {
        $obj1 = Text_Wiki2::singleton();
        $obj2 = Text_Wiki2::singleton();
        $this->assertEquals(spl_object_hash($obj1), spl_object_hash($obj2));
    }

    public function testSingletonOfDifferentParserShouldReturnDifferentObject()
    {
        $obj1 = Text_Wiki2::singleton('Tiki');
        $obj2 = Text_Wiki2::singleton();
        $this->assertNotEquals(spl_object_hash($obj1), spl_object_hash($obj2));
    }

    public function testFactoryReturnDefaultParserInstance()
    {
        $obj = Text_Wiki2::factory();
        $this->assertTrue(is_a($obj, 'Text_Wiki2_Default'));
    }

    public function testFactoryRestrictRulesUniverse()
    {
        $rules = array('Heading', 'Bold', 'Italic', 'Paragraph');
        $obj = Text_Wiki2::factory('Default', $rules);
        $this->assertEquals($rules, $obj->rules);
    }

    public function testTokenIdForMultipleParsingDontCollideWithDifferentObjects()
    {
        $sampleText = file_get_contents(dirname(__FILE__) . '/fixtures/text_wiki_sample_mediawiki_syntax.txt');
        $sampleTextTransformed2Xhtml = file_get_contents(dirname(__FILE__) . '/fixtures/text_wiki_sample_syntax_transformed_to_xhtml.txt');
        $sampleTextTransformed2Plain = file_get_contents(dirname(__FILE__) . '/fixtures/text_wiki_sample_syntax_transformed_to_plain.txt');
        $sampleTextTransformed2Plain2 = file_get_contents(dirname(__FILE__) . '/fixtures/text_wiki_sample_syntax_transformed_to_plain2.txt');
        $obj1 = Text_Wiki2::factory('Mediawiki');
        $this->assertEquals($sampleTextTransformed2Xhtml, $obj1->transform($sampleText, 'Xhtml'));
        $obj2 = Text_Wiki2::factory('Mediawiki');
        $this->assertEquals($sampleTextTransformed2Plain, $obj2->transform($sampleText, 'Plain'));
        $obj3 = Text_Wiki2::factory('Tiki');
        $this->assertEquals($sampleTextTransformed2Plain2, $obj3->transform($sampleText, 'Plain'));
    }

    public function testTokenIdForMultipleParsingDontCollideWithSameObjects()
    {
        $sampleText = file_get_contents(dirname(__FILE__) . '/fixtures/text_wiki_sample_mediawiki_syntax.txt');
        $sampleTextTransformed2Xhtml = file_get_contents(dirname(__FILE__) . '/fixtures/text_wiki_sample_syntax_transformed_to_xhtml.txt');
        $sampleTextTransformed2Plain = file_get_contents(dirname(__FILE__) . '/fixtures/text_wiki_sample_syntax_transformed_to_plain.txt');
        $sampleTextTransformed2Plain2 = file_get_contents(dirname(__FILE__) . '/fixtures/text_wiki_sample_syntax_transformed_to_plain2.txt');
        $obj1 = Text_Wiki2::singleton('Mediawiki');
        $this->assertEquals($sampleTextTransformed2Xhtml, $obj1->transform($sampleText, 'Xhtml'));
        $obj2 = Text_Wiki2::singleton('Mediawiki');
        $this->assertEquals($sampleTextTransformed2Plain, $obj2->transform($sampleText, 'Plain'));
        $obj3 = Text_Wiki2::singleton('Tiki');
        $this->assertEquals($sampleTextTransformed2Plain2, $obj3->transform($sampleText, 'Plain'));
    }

    public function testSetParseConf()
    {
        $expectedResult = array('Center' => array('css' => 'center'));
        $this->obj->setParseConf('center', 'css', 'center');
        $this->assertEquals($expectedResult, $this->obj->parseConf);
        
        $this->obj->parseConf = array();
        $expectedResult = array('Center' => array('css' => 'center'));
        $this->obj->setParseConf('center', array('css' => 'center'));
        $this->assertEquals($expectedResult, $this->obj->parseConf);
    }

    public function testGetParseConf()
    {
        $this->obj->parseConf = array('Include' => array('base' => '/path/to/scripts/',
                                                         'anotherKey' => 'anotherValue'),
                                      'Secondrule' => array('base' => '/other/path/'));
        
        $this->assertEquals(array('base' => '/other/path/'), $this->obj->getParseConf('Secondrule'));
        $this->assertEquals('/path/to/scripts/', $this->obj->getParseConf('include', 'base'));
        $this->assertNull($this->obj->getParseConf('inexistentRule', 'inexistentKey'));
    }
    
    public function testSetRenderConf()
    {
        $this->obj->setRenderConf('xhtml', 'center', 'css', 'center');
        $expectedResult = array('Center' => array('css' => 'center'));
        $this->assertEquals($expectedResult, $this->obj->renderConf['Xhtml']);
        
        $this->obj->setRenderConf('xhtml', 'center', 'secondConfig', 'secondConfigValue');
        $expectedResult = array('Center' => array('css' => 'center', 'secondConfig' => 'secondConfigValue'));
        $this->assertEquals($expectedResult, $this->obj->renderConf['Xhtml']);

        $arg = array('firstConfig' => 'firstConfigValue', 'secondConfig' => 'diferentValue');
        $this->obj->setRenderConf('xhtml', 'newrule', $arg);
        $expectedResult = array_merge($expectedResult, array('Newrule' => $arg));
        $this->assertEquals($expectedResult, $this->obj->renderConf['Xhtml']);
    }

    public function testGetRenderConfReturnRule()
    {
        $this->obj->renderConf['Xhtml'] = array('Center' => array('css' => 'center', 'align' => 'left')); 
        $this->assertEquals(array('css' => 'center', 'align' => 'left'), $this->obj->getRenderConf('Xhtml', 'Center'));
    }
    
    public function testGetRenderConfReturnEspecifKeyRule()
    {
        $this->obj->renderConf['Xhtml'] = array('Center' => array('css' => 'center', 'align' => 'left')); 
        $this->assertEquals('center', $this->obj->getRenderConf('Xhtml', 'Center', 'css'));
    }
    
    public function testGetRenderConfReturnFalseForInvalidFormatOrConfOrKey()
    {
        $this->obj->renderConf['Xhtml'] = array('Center' => array('css' => 'center', 'align' => 'left')); 
        $this->assertNull($this->obj->getRenderConf('InvalidFormat', 'InvalidRule', 'InvalidKey'));
        $this->assertNull($this->obj->getRenderConf('Xhtml', 'InvalidRule'));
        $this->assertNull($this->obj->getRenderConf('Xhtml', 'Center', 'InvalidKey'));
    }

    public function testSetFormatConfWithThreeArguments()
    {
        $expectedResult = array('Xhtml' => array('css' => 'center'));
        $this->obj->setFormatConf('Xhtml', 'css', 'center');
        $this->assertEquals($expectedResult, $this->obj->formatConf);
    }
    
    public function testSetFormatConfWithTwoArguments()
    {
        $expectedResult = array('Xhtml' => array('css' => 'center'));
        $this->obj->setFormatConf('Xhtml', array('css' => 'center'));
        $this->assertEquals($expectedResult, $this->obj->formatConf);
    }
    
    public function testGetFormatConf()
    {
        $this->obj->formatConf = array('Xhtml' => array('base' => '/path/to/scripts/',
                                                       'anotherKey' => 'anotherValue'),
                                      'Docbook' => array('base' => '/other/path/'));
        
        $this->assertEquals(array('base' => '/other/path/'), $this->obj->getFormatConf('Docbook'));
        $this->assertEquals('/path/to/scripts/', $this->obj->getFormatConf('Xhtml', 'base'));
        $this->assertNull($this->obj->getFormatConf('inexistentFormat'));
    }
    
    public function testInsertRuleShouldReturnNullIfRuleAlreadyExist()
    {
        $this->assertNull($this->obj->insertRule('Code', 'Prefilter'));
    }

    public function testInsertRuleShouldReturnNullIfInexistentRuleToInsertAfter()
    {
        $this->assertNull($this->obj->insertRule('Code', 'InexistentRule'));
    }

    public function testInsertRuleShouldInsertRuleAtTheEnd()
    {
        $return = $this->obj->insertRule('NewRule');
        $this->assertTrue($return);
        $this->assertEquals('Newrule', end($this->obj->rules));
    }
    
    public function testInsertRuleShouldInsertRuleAtTheBeginning()
    {
        $return = $this->obj->insertRule('NewRule', '');
        $this->assertTrue($return);
        $this->assertEquals('Newrule', $this->obj->rules[0]);
    }

    public function testInsertRuleShouldInsertRuleInExactPlace()
    {
        $key = array_search('Function', $this->obj->rules);
        $return = $this->obj->insertRule('NewRule', 'Function');
        $this->assertTrue($return);
        $this->assertEquals('Newrule', $this->obj->rules[$key+1]);
    }
    
    public function testDeleteRule()
    {
        $rules = array(0 => 'Prefilter', 1 => 'Delimiter', 2 => 'Code', 3 => 'Function', 5 => 'Raw', 6 => 'Include');
        $this->obj->deleteRule('Html');
        $this->assertEquals($rules, $this->obj->rules);
        
        $rules = array(0 => 'Prefilter', 2 => 'Code', 3 => 'Function', 5 => 'Raw', 6 => 'Include');
        $this->obj->deleteRule('Delimiter');
        $this->assertEquals($rules, $this->obj->rules);
    }

    public function testChangeRule()
    {
        $rules = array('Prefilter', 'Delimiter', 'Code', 'Function', 'Html', 'Newrulename', 'Include');
        $this->obj->changeRule('Raw', 'NewRuleName');
        $this->assertEquals($rules, $this->obj->rules);

        // should delete the 'Function' rule and rename 'Code' rule to 'Function'
        $rules = array(0 => 'Prefilter', 1 => 'Delimiter', 2 => 'Function', 4 => 'Html', 5 => 'Newrulename', 6 => 'Include');
        $this->obj->changeRule('Code', 'Function');
        $this->assertEquals($rules, $this->obj->rules);

        // no changes as inexistent old rule name
        $this->obj->changeRule('InexistentRule', 'NewRule');
        $this->assertEquals($rules, $this->obj->rules);
    }

    public function testEnableRule()
    {
        $this->obj->enableRule('Include');
        $disable = array(0 => 'Html', 2 => 'Embed');
        $this->assertEquals($disable, $this->obj->disable);
        $this->obj->enableRule('InvalidRule');
        $this->assertEquals($disable, $this->obj->disable);
    }

    public function testDisableRule()
    {
        $this->obj->disableRule('Newrule');
        $disable = array(0 => 'Html', 1 => 'Include', 2 => 'Embed', 3 => 'Newrule');
        $this->assertEquals($disable, $this->obj->disable);

        // nothing change as rule is already marked as disabled
        $this->obj->disableRule('Include');
        $this->assertEquals($disable, $this->obj->disable);
    }

    public function testTransform()
    {
        $obj = Text_Wiki2::factory('Mediawiki');
        $expectedResult = 'A very \'\'simple\'\' __source__ text. Not sure ((how)) to [http://example.com|improve] the transform() tests.' . "\n\n";
        $this->assertEquals($expectedResult, $obj->transform($this->sourceText, 'Tiki'));
    }

    public function testParse()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    public function testParseShouldSetSourceProperty()
    {
        $this->obj->parse($this->sourceText);
        // TODO: check why there is a line break at the beginning and end of $this->obj->source
        $this->assertEquals("\n".$this->sourceText."\n", $this->obj->source);
    }

    public function testRender()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    public function testRenderShouldReturnErrorIfInvalidFormat()
    {
        $result = $this->obj->render('InvalidFormat');
        $this->assertTrue(is_a($result, 'PEAR_Error'));
    }

    public function test_renderToken()
    {
        // $matches = array(0 => '0', 1 => 0);
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    public function testRegisterRenderCallback()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    public function testPopRenderCallback()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    public function testGetSource()
    {
        $this->obj->source = $this->sourceText;
        $this->assertEquals($this->sourceText, $this->obj->getSource());
    }

    public function testGetTokens()
    {
        $breakTokens = array_slice($this->tokens, 6, 2, true);

        $this->obj->tokens = $this->tokens;
        $this->assertEquals($this->tokens, $this->obj->getTokens());
        $this->assertEquals($breakTokens, $this->obj->getTokens('Break'));
    }

    public function testAddTokenReturnIdOnly()
    {
        $options = array('type' => 'someType', 'anotherOption' => 'value');
        $id = $this->obj->addToken('Test', $options, true);
        $this->assertEquals(0, $id);
    }

    public function testAddTokenReturnTokenNumberWithDelimiters()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
    
    public function testSetTokenShouldChangeOptionsOfAlreadyExistingRuleAndKeepName()
    {
        $this->markTestSkipped("Test uses supposedly private/protected vars, need to update.");

        $this->obj->tokens = $this->tokens;
        $this->obj->_countRulesTokens = $this->_countRulesTokens;

        $tokens = array(
            0 => array(0 => 'Heading', 1 => array('type' => 'start', 'level' => 6, 'text' => 'Level 6 heading', 'id' => 'toc0')),
            1 => array(0 => 'Heading', 1 => array('type' => 'end', 'level' => 6)),
            2 => array(0 => 'Heading', 1 => array()),
            3 => array(0 => 'Heading', 1 => array('type' => 'end', 'level' => 1)),
            4 => array(0 => 'Heading', 1 => array('type' => 'start', 'level' => 2, 'text' => 'Level 2 heading', 'id' => 'toc2')),
            5 => array(0 => 'Heading', 1 => array('type' => 'end', 'level' => 2)),
            6 => array(0 => 'Break', 1 => array()),
            7 => array(0 => 'Break', 1 => array())
        );
        $this->obj->setToken(2, 'Heading', array());
        $this->assertEquals($tokens, $this->obj->tokens);
        $this->assertEquals($this->_countRulesTokens, $this->obj->_countRulesTokens);
    }

    public function testSetTokenShouldReplaceRuleWithNewRule()
    {
        $this->markTestSkipped("Uses protected/private vars");

        $this->obj->tokens = $this->tokens;
        $this->obj->_countRulesTokens = $this->_countRulesTokens;
        
        $tokens = array(
            0 => array(0 => 'Heading', 1 => array('type' => 'start', 'level' => 6, 'text' => 'Level 6 heading', 'id' => 'toc0')),
            1 => array(0 => 'Heading', 1 => array('type' => 'end', 'level' => 6)),
            2 => array(0 => 'Heading', 1 => array('type' => 'start', 'level' => 1, 'text' => 'Level 1 heading', 'id' => 'toc1')),
            3 => array(0 => 'Raw', 1 => array('type' => 'end')),
            4 => array(0 => 'Heading', 1 => array('type' => 'start', 'level' => 2, 'text' => 'Level 2 heading', 'id' => 'toc2')),
            5 => array(0 => 'Heading', 1 => array('type' => 'end', 'level' => 2)),
            6 => array(0 => 'Break', 1 => array()),
            7 => array(0 => 'Break', 1 => array())
        );
        $this->_countRulesTokens = array('Heading' => 5, 'Break' => 2, 'Raw' => 1);
        $this->obj->setToken(3, 'Raw', array('type' => 'end'));
        $this->assertEquals($tokens, $this->obj->tokens);
        $this->assertEquals($this->_countRulesTokens, $this->obj->_countRulesTokens);
    }

    public function testSetTokenShouldAddNewRule()
    {
        $this->markTestSkipped("private/protected vars");

        $this->obj->tokens = $this->tokens;
        $this->obj->_countRulesTokens = $this->_countRulesTokens;
        
        $tokens = array(
            0 => array(0 => 'Heading', 1 => array('type' => 'start', 'level' => 6, 'text' => 'Level 6 heading', 'id' => 'toc0')),
            1 => array(0 => 'Heading', 1 => array('type' => 'end', 'level' => 6)),
            2 => array(0 => 'Heading', 1 => array('type' => 'start', 'level' => 1, 'text' => 'Level 1 heading', 'id' => 'toc1')),
            3 => array(0 => 'Heading', 1 => array('type' => 'end', 'level' => 1)),
            4 => array(0 => 'Heading', 1 => array('type' => 'start', 'level' => 2, 'text' => 'Level 2 heading', 'id' => 'toc2')),
            5 => array(0 => 'Heading', 1 => array('type' => 'end', 'level' => 2)),
            6 => array(0 => 'Break', 1 => array()),
            7 => array(0 => 'Break', 1 => array()),
            8 => array(0 => 'Raw', 1 => array('type' => 'end')),
        );
        $this->_countRulesTokens = array('Heading' => 6, 'Break' => 2, 'Raw' => 1);
        $this->obj->setToken(8, 'Raw', array('type' => 'end'));
        $this->assertEquals($tokens, $this->obj->tokens);
        $this->assertEquals($this->_countRulesTokens, $this->obj->_countRulesTokens);
    }
    
    public function testLoadParseObj()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
    
    public function testLoadRenderObj()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    public function testLoadFormatObj()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    public function testAddPathShouldAddDirToExistentType()
    {
        $this->markTestSkipped("private/protected vars");

        $path = array('parse' => array('Text/Wiki2/Parse/Default/'), 'render' => array());
        $this->obj->addPath('parse', 'Text/Wiki2/Parse/Default/');
        $this->assertEquals($path, $this->obj->path);
        
        // dir without trailing trailing slash
        $path = array('parse' => array('Text/Wiki2/Parse/Other/', 'Text/Wiki2/Parse/Default/'), 'render' => array());
        $this->obj->addPath('parse', 'Text/Wiki2/Parse/Other');
        $this->assertEquals($path, $this->obj->path);
    }
    
    public function testAddPathCreateTypeAndThenAddDir()
    {
        $path = array('parse' => array('Text/Wiki2/Parse/Default/'));
        $this->obj->addPath('parse', 'Text/Wiki2/Parse/Default/');
        $this->assertEquals($path, $this->obj->getPath());
    }
    
    public function testGetPathShouldReturnPathArray()
    {
        $path = array('parse' => array('Text/Wiki2/Parse/Default/', 'Text/Wiki2/Parse/Other/'), 'render' => array('Text/Wiki2/Parse/Xhtml/'));
        $this->obj->addPath('parse', $path);
        $this->assertEquals($path, $this->obj->getPath());
    }

    public function testGetPathShouldReturnTypePaths()
    {
        $path = array('parse' => array('Text/Wiki2/Parse/Default/', 'Text/Wiki2/Parse/Other/'), 'render' => array('Text/Wiki2/Parse/Xhtml/'));
        $this->obj->addPath('parse', $path);
        $this->assertEquals($path['parse'], $this->obj->getPath('parse'));
        $this->assertEquals($path['render'], $this->obj->getPath('render'));
    }
    
    public function testGetPathShouldReturnEmptyArray()
    {
        $path = array('parse' => array('Text/Wiki2/Parse/Default/', 'Text/Wiki2/Parse/Other/'), 'render' => array('Text/Wiki2/Parse/Xhtml/'));
        $this->obj->addPath('parse', $path);
        $this->assertEquals(array(), $this->obj->getPath('InexistentType'));
    }

    public function testFindFile()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    public function testFixPath()
    {
        $this->assertEquals('path/', $this->obj->fixPath('path'));
        $this->assertEquals('/longer/path/path/', $this->obj->fixPath('/longer/path/path'));
        $this->assertEquals('/longer/path/path/with/trailing/slash/', $this->obj->fixPath('/longer/path/path/with/trailing/slash/'));
        $this->assertEquals('', $this->obj->fixPath(''));
    }
}
