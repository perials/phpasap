<?php
/**
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

class Mail_Handler {
    
    private static $last_error = "";
    
    /**
     * get last sent mail's error if any
     */
    public function get_last_error() {
        return self::$last_error;
    }
    
    protected function get_phpmailer_instance() {
        $phpmailer_obj = new Php_Mailer();
        
        if(Config::get('mail.use_smtp')) {
            $phpmailer_obj->isSMTP(); // Set mailer to use SMTP
        
            $phpmailer_obj->Host = Config::get('mail.smtp_host');
            $phpmailer_obj->SMTPAuth = true;
            $phpmailer_obj->Username = Config::get('mail.smtp_username');
            $phpmailer_obj->Password = Config::get('mail.smtp_password');
            $phpmailer_obj->SMTPSecure = Config::get('mail.smtp_encryption');
            $phpmailer_obj->Port = Config::get('mail.smtp_port');
        }
        
        return $phpmailer_obj;
    }
    
    /**
     * $to = 'info@perials.com'
     * $to = ['info@perials.com', 'Info']
     * $to = [ ['info@perials.com', 'Info'], ['support@gmail.com', 'Support'] ]
     */
    public function send($to=false, $subject=false, $message=false, $options=[]) {
        if( empty($to) ||
            empty($subject) ||
            empty($message)
           ) {
            self::$last_error = "Required fields are missing";
            return false;
        }
        
        $defaults = [
                        'is_html' => Config::get('mail.is_html'),
                        'from' => Config::get('mail.from')
                    ];
        
        $options = array_merge($defaults, $options);
        
        if(empty($options['from'])) {
            self::$last_error = "From field are missing";
            return false;    
        }
        
        $from_mail = is_array($options['from']) ? $options['from'][0] : $options['from'];
        $from_name = is_array($options['from']) ? $options['from'][1] : '';
        
        //validate from mail
        if(filter_var($from_mail, FILTER_VALIDATE_EMAIL) === FALSE) {
            self::$last_error = "From mail is invalid";
            return false;
        }
        
        //validate to mail
        //also set to mail array
        $to_mail_array = [];
        
        if( !($to_mail_array = $this->validate_and_get_mail_name_array($to)) )
        return false;
    
        //validate and set cc mail array
        $cc_mail_array = [];
        if( !empty($options['cc']) ) {
            if( !($cc_mail_array = $this->validate_and_get_mail_name_array($options['cc'])) )
            return false;
        }
        
        //validate and set bcc mail array
        $bcc_mail_array = [];
        if( !empty($options['bcc']) ) {
            if( !($bcc_mail_array = $this->validate_and_get_mail_name_array($options['bcc'])) )
            return false;
        }
        
        $mail = $this->get_phpmailer_instance();
        
        // set from
        $mail->setFrom($from_mail, $from_name);
        
        // set to
        foreach($to_mail_array as $mail_name_array) {
            $mail->addAddress( $mail_name_array[0], $mail_name_array[1] );
        }
        
        // set cc
        foreach($cc_mail_array as $mail_name_array) {
            $mail->addCC( $mail_name_array[0], $mail_name_array[1] );
        }
        
        // set bcc
        foreach($bcc_mail_array as $mail_name_array) {
            $mail->addBCC( $mail_name_array[0], $mail_name_array[1] );
        }
        
        if( !empty($options['is_html']) )
        $mail->isHTML(true);
        
        // set subject
        $mail->Subject = $subject;
        
        // set body
        $mail->Body = ( $message instanceof View_Handler ) ? $message->get_markup() : $message;
        
        if(!$mail->send()) {
            self::$last_error = $mail->ErrorInfo;
            return false;
        }
        else {
            self::$last_error = "";
            return true;
        }
        
    }
    
    protected function validate_and_get_mail_name_array($field) {
        $mail_name_array = [];
        
        if(is_array($field)) {
            //if each element in $to is also an array then it is multi recipients
            if(is_array($field[0])) {
                foreach($field as $email_name_array) {
                    if(filter_var($email_name_array[0], FILTER_VALIDATE_EMAIL) === FALSE) {
                        self::$last_error = "To mail is invalid";
                        return false;
                    }
                    $mail_name_array[] = [$email_name_array[0], isset($email_name_array[1]) ? $email_name_array[1] : ''];
                }
            }
            else {
                if(filter_var($field[0], FILTER_VALIDATE_EMAIL) === FALSE) {
                    self::$last_error = "To mail is invalid";
                    return false;
                }
                $mail_name_array[] = [$field[0], isset($field[1]) ? $field[1] : ''];
            }
        }
        else {
            if(filter_var($field, FILTER_VALIDATE_EMAIL) === FALSE) {
                self::$last_error = "To mail is invalid";
                return false;
            }
            $mail_name_array[] = [$field, ''];
        }
        
        return $mail_name_array;
    }
    
}