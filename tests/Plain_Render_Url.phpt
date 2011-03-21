--TEST--
Text_Wiki2_Plain_Render_Url
--SKIPIF--
<?php require_once dirname(__FILE__).'/skipif.php'; ?>
--FILE--
<?php
error_reporting(E_ALL ^ E_NOTICE);
include 'config.php';
require_once 'Text/Wiki2/Creole.php';
$w =() new Text_Wiki2_Creole(array('Url'));
var_dump($w->transform('
[[http://www.example.com/page|An example page]]
[[http://www.example.com/page]]
http://www.example.com/page
', 'Plain'));
?>
--EXPECT--
string(19) "
An example page


"
