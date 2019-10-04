<?php
/**
 * PA Exception Class
 *
 * The App class instance is created by bootstrap file. This instance sets up the
 * debug mode conditionally based on user configuration
 * It also calls the Route_Handler class instance which then decides which
 * Controller method to be called depending upon the current HTTP request and
 * user set routing rules
 * 
 * This file is part of the PHPasap, a MVC framework
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2016, Perials Technologies
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	    PHPasap
 * @author	    Perials
 * @copyright	Copyright (c) 2016, Perials Technologies (https://perials.com/)
 * @license	    http://opensource.org/licenses/MIT	MIT License
 * @link	    https://phpasap.com
 */

namespace phpasap\classes;

class Pa_Exception extends \Exception {
    
    public function errorMessage() {
        
        //echo "<pre>";print_r($this->getTrace());echo "</pre>";
        //echo "<pre>";print_r($this->getTrace());echo "</pre>";die;
        //error message
        $errorMsg = '<strong>'.$this->getMessage().'</strong>';
        if( $this->getCode() != 8888 ) {
            $errorMsg .= '<br/>
            File: '.$this->getFile().'<br/>
            Line: '.$this->getLine().'<br/>
            Exception Code: '.$this->getCode().'<br/>
            <table style="border-collapse: collapse; font-size:14px;" border="1" cellpadding="1" cellspacing="0">';
            $errorMsg .= "<tr><th colspan='5'>Traceback<th></tr>
            <tr>
                <td>File</td>
                <td>Line</td>
                <td>Class</td>
                <td>Function</td>
                <td>Args</td>
                </tr>";
            foreach( $this->getTrace() as $trace_array ) {
                $errorMsg .= "<tr>
                <td>". (isset($trace_array['file']) ? $trace_array['file'] : "") ."</td>
                <td>". (isset($trace_array['line']) ? $trace_array['line'] : "") ."</td>
                <td>". (isset($trace_array['class']) ? $trace_array['class'] : "") ."</td>
                <td>".$trace_array['function']."</td>
                <td>".print_r($trace_array['args'], true)."</td>
                </tr>";
            }
            $errorMsg .= "</table>";
        }
        return show_error($errorMsg);
    }
    
}