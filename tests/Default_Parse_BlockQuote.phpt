--TEST--
Text_Wiki2_Default_Blockquote
--SKIPIF--
<?php require_once dirname(__FILE__).'/skipif.php'; ?>
--FILE--
<?php
include 'config.php';
require_once 'Text/Wiki.php';
$t = Text_Wiki2::factory('Default', array('Blockquote'));
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
