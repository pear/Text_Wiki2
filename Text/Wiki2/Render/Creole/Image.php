<?php
class Text_Wiki2_Render_Creole_Image extends Text_Wiki2_Render {

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
        if (!strlen($options['attr']['alt']) || $options['src'] == $options['attr']['alt']) {
            return '{{'.$options['src'].'}}';
        } else {
            return '{{'.$options['src'].'|'.$options['attr']['alt'].'}}';
        }
    }
}
?>