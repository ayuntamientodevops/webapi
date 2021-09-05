<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class EmailController extends CI_Controller {

    public function __construct() {
        parent:: __construct();

        $this->load->helper('url');
    }

    
    function send($fromEmail, $toEmail) {
        $config = Array(        
            'protocol' => 'smtp',
            'smtp_host' => 'mail.asdn.gob.do',
            'smtp_port' => 465,
            'smtp_user' => 'helpdesk@asdn.gob.do',
            'smtp_pass' => 'ITsoporte2021',
            'smtp_timeout' => '4',
            'mailtype'  => 'html', 
            'charset'   => 'iso-8859-1'
        );
        $this->load->library('email', $config);
        $this->email->set_newline("\r\n"); 
        $from_email = "helpdesk@asnd.gob.do"; 
        $this->email->from($from_email, 'Name'); 
        $this->email->to($toEmail);
        $this->email->subject('email subject');
        $message = 'email body';                 
        if ($this->email->send()) {
            echo 'Your Email has successfully been sent.';
        } else {
            show_error($this->email->print_debugger());
        }
    }
}