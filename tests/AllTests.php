<?php
@include dirname(__FILE__) . '/config.php';

require_once 'PHPUnit/Extensions/PhptTestSuite.php';

require_once 'Text_Wiki2_Tests.php';
require_once 'Text_Wiki2_Render_Tests.php';
require_once 'Text_Wiki2_Render_Tiki_Tests.php';
//require_once 'Text_Wiki2_Parse_Tiki_Tests.php';
require_once 'Text/Wiki2/Parse/Mediawiki/Tests.php';
require_once 'Text_Wiki2_Generic_Transform_Tests.php';
require_once 'Text_Wiki2_BugTests.php';

class Text_Wiki2_AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Text_Wiki2_TestSuite');

        /* almost all phpt tests are failling and need to be fixed
           before uncommenting the code below
        $phptTests = new PHPUnit_Extensions_PhptTestSuite('.');
        $suite->addTestSuite($phptTests); */

        $suite->addTestSuite('Text_Wiki2_Tests');
        $suite->addTestSuite('Text_Wiki2_Render_Tests');
        //TODO: integrate Text_Wiki2_Parse_Tiki_AllTests
        //$suite->addTestSuite('Text_Wiki2_Parse_Tiki_AllTests');
        $suite->addTestSuite('Text_Wiki2_Render_Tiki_AllTests');
        $suite->addTestSuite('Text_Wiki2_Parse_Mediawiki_AllTests');
        $suite->addTestSuite('Text_Wiki2_Generic_Transform_Tests');

        /**
         * @desc This suite currently 'fails' when run through here.
         *       Standalone works.
         */
        $suite->addTestSuite('Text_Wiki2_BugTests');

        return $suite;
    }

    /**
     * Autoloader for the test suite.
     */
    public static function autoload($className)
    {
        if (substr($className, 0, 10) != 'Text_Wiki2') {
            return false;
        }
        $file = str_replace('_', '/', $className) . '.php';
        return include $file;
    }
}

spl_autoload_register(array('Text_Wiki2_AllTests', 'autoload'));
