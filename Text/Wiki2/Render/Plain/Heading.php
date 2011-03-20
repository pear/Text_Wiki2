<?php

class Text_Wiki2_Render_Plain_Heading extends Text_Wiki2_Render {
    
    function token($options)
    {
        if ($options['type'] == 'end') {
            return "\n\n";
        } else {
            return "\n";
        }
    }
}
?>