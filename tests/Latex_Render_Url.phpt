--TEST--
Text_Wiki_Latex_Render_Url
--SKIPIF--
<?php
if (!file_exists('config.php')) {
   print "Skip missing configuration file, see config.dist.php";
}
?>
--FILE--
<?php
error_reporting(E_ALL ^ E_NOTICE);
include 'config.php';
require_once 'Text/Wiki/Creole.php';
$w =& new Text_Wiki_Creole(array('Url'));
var_dump($w->transform('
[[http://www.example.com/page|An example page]]
[[http://www.example.com/page]]
http://www.example.com/page
', 'Latex'));
?>
--EXPECT--
string(148) "
An example page\footnote{http://www.example.com/page}
\footnote{http://www.example.com/page}
\footnote{http://www.example.com/page}
\end{document}
"
