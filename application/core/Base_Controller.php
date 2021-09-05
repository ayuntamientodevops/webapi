<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'libraries/mPdf/autoload.php';

use \Mpdf\Mpdf;

class Base_Controller extends CI_Controller
{

    public function __construct()
    {

        parent::__construct();

        date_default_timezone_set('America/Santo_Domingo');
        setlocale(LC_ALL, "es_MX");

        $CI = &get_instance();
        $session = $CI->session->userdata('logged_in');
        if ($session != null) {

            $url_name = $CI->router->fetch_class() . "/" . $CI->router->method;

            $CI->db->select('id_menu');
            $CI->db->where('url', $url_name);

            $id_menu = $CI->db->get('menu')->row_array();

            $CI->db->select('*');
            $CI->db->where('id_usuario', $session["id_usuario"]);
            $CI->db->where_in('id_menu', $id_menu);

            $query = $CI->db->get('menu_permisos');

            if ($session['IntPerfil'] != 1) {
                if ($query->num_rows() == 0)
                    header("Location: " . base_url() . "home/error_view");
            }
        } else {

            redirect('login');
        }
    }

    public function PrintPdfDocument($format, $orien, $html, $fileName, $output)
    {
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => $format,
            'orientation' => $orien
        ]);
        $mpdf->WriteHTML($html);
        $mpdf->Output($fileName, $output);
    }

    public function SendEmail(
        $fromEmail,
        $nametFrom,
        $toEmail,
        $ccEmail,
        $subject,
        $template,
        $isCc
    ) {

        //Load email library
        $this->load->library('email');

        $config['mailtype'] = 'sendmail';
        $config['wordwrap'] = TRUE;
        $config['charset']    = 'utf-8';
        $config['newline']    = "\r\n";

        $config['mailtype'] = 'html';
        $config['protocol'] = 'smtp';
        $config['smtp_host'] = 'mail.asdn.gob.do';
        $config['smtp_crypto'] = 'ssl';
        $config['smtp_port'] = '465';
        $config['smtp_user'] = 'helpdesk@asdn.gob.do';
        $config['smtp_pass'] = 'jE*gql1H{NY6';

        $this->email->initialize($config);
        $this->email->from($fromEmail, $nametFrom);
        $this->email->to($toEmail);
 
        if ($isCc){
            $this->email->cc($ccEmail);
        }
        $this->email->subject($subject);
        $this->email->message($template);

        $this->email->send();
    }
    public function UploadFiles($files, $path, $allowedFiles = array(), $isNameModify, $IdUser)
    {
        $arryName = array();
        for ($i = 0; $i < count($files); $i++) {
            if ($isNameModify) {
                $name = $IdUser;
            } else {
                $name =  md5($files[$i]['name']);
            }

            $typeFile = substr($files[$i]['type'], strpos($files[$i]['type'], "/") + 1, strlen($files[$i]['type']));
            $fullNameFile = $name . "." . $typeFile;
            $fullPath = $path . $fullNameFile;

            if (!in_array($typeFile, $allowedFiles)) {
                $result = "El archivo que esta tratando de cargar no esta permitido.";
            } else {

                if (move_uploaded_file($files[$i]['tmp_name'], $fullPath)) {
                    $result = 'Archivo cargado correctamente.';
                } else {
                    $result =  'Hubo un error al intentar cargar el archivo.';
                }

                array_push($arryName, array('fileName' => $fullNameFile, 'result' => $result));
            }
        }

        return $arryName;
    }
}
