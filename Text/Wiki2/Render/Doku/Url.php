<?php


class Text_Wiki2_Render_Doku_Url extends Text_Wiki2_Render {

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
        if ($options['type'] == 'start') {
            if (! strlen($options['text']) || $options['page'] == $options['text']) {
                return $options['href'];
            } else {
                return '[['.$options['href'].'|';
            }
        }
        else if ($options['type'] == 'end') {
            if (! strlen($options['text']) || $options['page'] == $options['text']) {
                return '';
            } else {
                return ']]';
            }
        }
        else {
            if (! strlen($options['text']) || $options['page'] == $options['text']) {
                return $options['href'];
            } else {
                return '[['.$options['href'].'|'.$options['text'].']]';
            }
        }
    }
}
?>