<?php

class Text_Wiki_Render_Tiki_Colortext extends Text_Wiki_Render {
    
    var $colors = array(
        'aqua',
        'black',
        'blue',
        'fuchsia',
        'gray',
        'green',
        'lime',
        'maroon',
        'navy',
        'olive',
        'purple',
        'red',
        'silver',
        'teal',
        'white',
        'yellow'
    );
    
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
        if (!in_array($options['color'], $this->colors)) {
            $options['color'] = '#' . $options['color'];
        }
        
        if ($options['type'] == 'start') {
            return '~~'.$options['color'].':';
        }
        
        if ($options['type'] == 'end') {
            return '~~';
        }
    }
}
?>