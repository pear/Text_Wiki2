<?php

class Text_Wiki2_Render_CoWiki_Heading extends Text_Wiki2_Render {
    function token($options)
    {
        if ($options['type'] == 'start') {
            return str_pad('', $options['level'], '+').' ';
        } else {
            return '';
        }
    }
}
?>