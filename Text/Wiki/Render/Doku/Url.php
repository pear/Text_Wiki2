<?php


class Text_Wiki_Render_Doku_Url extends Text_Wiki_Render {
    
    /**
    * 
    * Renders a token into text matching the requested format.
    * 
    * @access public
    * 
    * @param array $options The "options" portion of the token (second
    * element).
    * 
    * @return string The text rendered from the token options.
    * 
    */
    
    function token($options)
    {
        if (!strlen($options['text']) || $options['page'] == $options['text']) {
            return $options['href'];
        } else {
            return '[['.$options['href'].'|'.$options['text'].']]';
        }
    }
}
?>