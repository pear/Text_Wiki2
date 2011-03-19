<?php
@include dirname(__FILE__) . '/config.php';

require_once 'PHPUnit/Extensions/PhptTestSuite.php';
require_once 'Text_Wiki_Tests.php';
require_once 'Text_Wiki_Render_Tests.php';
require_once 'Text_Wiki_Render_Tiki_Tests.php';
//require_once 'Text_Wiki_Parse_Tiki_Tests.php';
require_once 'Text_Wiki_Parse_Mediawiki_Tests.php';
require_once 'Text_Wiki_Generic_Transform_Tests.php';

class Framework_AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Text_Wiki_TestSuite');

        /* almost all phpt tests are failling and need to be fixed
           before uncommenting the code below
        $phptTests = new PHPUnit_Extensions_PhptTestSuite('.');
        $suite->addTestSuite($phptTests); */

        $suite->addTestSuite('Text_Wiki_Tests');
        $suite->addTestSuite('Text_Wiki_Render_Tests');
        //TODO: integrate Text_Wiki_Parse_Tiki_AllTests
        //$suite->addTestSuite('Text_Wiki_Parse_Tiki_AllTests');
        $suite->addTestSuite('Text_Wiki_Render_Tiki_AllTests');
        $suite->addTestSuite('Text_Wiki_Parse_Mediawiki_AllTests');
        $suite->addTestSuite('Text_Wiki_Generic_Transform_Tests');

        return $suite;
    }
}
