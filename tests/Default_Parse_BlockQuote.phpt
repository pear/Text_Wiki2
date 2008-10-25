--TEST--
Text_Wiki_Default_Blockquote
--SKIPIF--
<?php
if (!file_exists('config.php')) {
   print "Skip missing configuration file, see config.dist.php";
}
?>
--FILE--
<?php
include 'config.php';
require_once 'Text/Wiki.php';
$t = Text_Wiki::factory('Default', array('Blockquote'));
$t->parse('
> test 1
> test 2
>> test 11
>> test 22
', 'Xhtml');
var_dump($t->source);
var_dump($t->tokens);
?>
--EXPECT--
string(43) "
�0�test 1
test 2
�1�test 11
test 22�2��3�
"
array(4) {
  [0]=>
  array(2) {
    [0]=>
    string(10) "Blockquote"
    [1]=>
    array(2) {
      ["type"]=>
      string(5) "start"
      ["level"]=>
      int(1)
    }
  }
  [1]=>
  array(2) {
    [0]=>
    string(10) "Blockquote"
    [1]=>
    array(2) {
      ["type"]=>
      string(5) "start"
      ["level"]=>
      int(2)
    }
  }
  [2]=>
  array(2) {
    [0]=>
    string(10) "Blockquote"
    [1]=>
    array(2) {
      ["type"]=>
      string(3) "end"
      ["level"]=>
      int(2)
    }
  }
  [3]=>
  array(2) {
    [0]=>
    string(10) "Blockquote"
    [1]=>
    array(2) {
      ["type"]=>
      string(3) "end"
      ["level"]=>
      int(1)
    }
  }
}
