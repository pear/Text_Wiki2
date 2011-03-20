<?php

class Text_Wiki2_Render_Latex_Newline extends Text_Wiki2_Render {
    
    
    function token($options)
    {
        return "\\newline\n";
    }
}

?>