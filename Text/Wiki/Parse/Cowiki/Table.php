<?php

/**
* 
* Parses for table markup.
* 
* @category Text
* 
* @package Text_Wiki
* 
* @author Paul M. Jones <pmjones@php.net>
* 
* @license LGPL
* 
* @version $Id$
* 
*/

/**
* 
* Parses for table markup.
* 
* This class implements a Text_Wiki_Parse to find source text marked as a
* set of table rows, where a line start and ends with double-pipes (||)
* and uses double-pipes to separate table cells.  The rows must be on
* sequential lines (no blank lines between them) -- a blank line
* indicates the beginning of a new table.
*
* @category Text
* 
* @package Text_Wiki
* 
* @author Paul M. Jones <pmjones@php.net>
* 
*/

class Text_Wiki_Parse_Table extends Text_Wiki_Parse {
    
    
    /**
    * 
    * The regular expression used to parse the source text and find
    * matches conforming to this rule.  Used by the parse() method.
    * 
    * @access public
    * 
    * @var string
    * 
    * @see parse()
    * 
    */
    
    var $regex = '/\n<table( [^>]*)?>(.*?)<\/table>\n/Us';
    
    
    /**
    * 
    * Generates a replacement for the matched text.
    * 
    * Token options are:
    * 
    * 'type' =>
    *     'table_start' : the start of a bullet list
    *     'table_end'   : the end of a bullet list
    *     'row_start' : the start of a number list
    *     'row_end'   : the end of a number list
    *     'cell_start'   : the start of item text (bullet or number)
    *     'cell_end'     : the end of item text (bullet or number)
    * 
    * 'cols' => the number of columns in the table (for 'table_start')
    * 
    * 'rows' => the number of rows in the table (for 'table_start')
    * 
    * 'span' => column span (for 'cell_start')
    * 
    * 'attr' => column attribute flag (for 'cell_start')
    * 
    * @access public
    *
    * @param array &$matches The array of matches from parse().
    *
    * @return A series of text and delimited tokens marking the different
    * table elements and cell text.
    *
    */
    
    function process(&$matches)
    {
        if (strlen(trim($matches[1]))) {
            $attr = $this->getAttrs(trim($matches[1]));
        } else {
            $attr = array();
        }

        // our eventual return value
        $return = '';
        
        // the number of columns in the table
        $num_cols = 0;
        
        // the number of rows in the table
        $num_rows = 0;
        
        // rows are separated by newlines in the matched text
        $rows = explode("\n", $matches[2]);
        
        // loop through each row
        foreach ($rows as $row) {
            if (!strlen($row)) {
                continue;
            }
            
            // increase the row count
            ++$num_rows;
            
            // start a new row
            $return .= $this->wiki->addToken(
                $this->rule,
                array('type' => 'row_start')
            );
            
            // cells are separated by pipes
            $cells = explode('|', $row);
            
            // get the number of cells (columns) in this row
            $last = count($cells);
            
            // is this more than the current column count?
            if ($last - 1 > $num_cols) {
                // increase the column count
                $num_cols = $last - 1;
            }
            
            // by default, cells span only one column (their own)
            $span = 1;
            
            for ($i = 1; $i < $last; ++$i) {
                
                // if there is no content at all, then it's an instance
                // of two sets of || next to each other, indicating a
                // span.
                if ($cells[$i] == '') {
                    
                    // add to the span and loop to the next cell
                    $span += 1;
                    continue;
                    
                } else {
                    
                    // this cell has content.

                    $attr = null;                    
                    
                    // start a new cell...
                    $return .= $this->wiki->addToken(
                        $this->rule, 
                        array (
                            'type' => 'cell_start',
                            'attr' => $attr,
                            'span' => $span
                        )
                    );
                    
                    // ...add the content...
                    $return .= trim($cells[$i]);
                    
                    // ...and end the cell.
                    $return .= $this->wiki->addToken(
                        $this->rule, 
                        array (
                            'type' => 'cell_end',
                            'attr' => $attr,
                            'span' => $span
                        )
                    );
                    
                    // reset the span.
                    $span = 1;
                }
                    
            }
            
            // end the row
            $return .= $this->wiki->addToken(
                $this->rule,
                array('type' => 'row_end')
            );
            
        }
        
        // wrap the return value in start and end tokens 
        $return =
            $this->wiki->addToken(
                $this->rule,
                array(
                    'type' => 'table_start',
                    'rows' => $num_rows,
                    'cols' => $num_cols,
                    'attr' => $attr
                )
            )
            . $return .
            $this->wiki->addToken(
                $this->rule,
                array(
                    'type' => 'table_end'
                )
            );
        
        // we're done!
        return "\n$return\n\n";
    }
}
?>