--TEST--
Text_Wiki2_Xhtml_Render_Url
--SKIPIF--
<?php require_once dirname(__FILE__).'/skipif.php'; ?>
--FILE--
<?php
include 'config.php';
require_once 'Text/Wiki2/Creole.php';
$w =& new Text_Wiki2_Creole(array('Url'));
var_dump($w->transform('
[[http://www.example.com/page|An example page]]
[[http://www.example.com/page]]
http://www.example.com/page
', 'Xhtml'));
?>
--EXPECT--
string(319) "
<a href="http://www.example.com/page" onclick="window.open(this.href, '_blank'); return false;">An example page</a>
<a href="http://www.example.com/page" onclick="window.open(this.href, '_blank'); return false;"></a>
<a href="http://www.example.com/page" onclick="window.open(this.href, '_blank'); return false;"></a>
"
