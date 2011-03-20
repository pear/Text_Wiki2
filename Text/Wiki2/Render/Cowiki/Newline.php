<?php

class Text_Wiki2_Render_CoWiki_Newline extends Text_Wiki2_Render {
    
    
    function token($options)
    {
        return "\n";
        //return "\\\\\n";
    }
}

?>