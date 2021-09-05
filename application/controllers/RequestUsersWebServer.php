<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
use chriskacerguis\RestServer\RestController;

class RequestUsersWebServer extends RestController
{
    public function __construct() { 
        parent::__construct();
        
        // Load the user model
        $this->load->model('RequestUserModel', 'user');
       // $this->load->library('email','emailLib');
    }


    /*
        login
        Typo de metodo: Post
        Ejemplo de llamada: http://localhost/sci/RequestUsersWebServer/login
        Parametros requeridos:

        {
            "IdentificationCard": "22500719137",
            "password": "login_pass"
        }

        Autenticacion del API:

        User: sciadmin
        Password: sciadmin@

        X-API-KEY: sciadmin@sciadmin
    */

    public function login_post() {
        // Get the post data
        $identificationCard = $this->post('IdentificationCard');
        $password = $this->post('password');
        
        // Validate the post data
        if(!empty($identificationCard) && !empty($password)){
            
            // Check if any user exists with the given credentials
            $con['returnType'] = 'single';
            $con['conditions'] = array(
                'IdentificationCard' => $identificationCard,
                'password' => md5($password),
                'status' => 1
            );
            $user = $this->user->getRows($con);
            
            if($user){
                // Set the response and exit
                $this->response([
                    'status' => true,
                    'message' => 'Inicio de sesión satisfactorio.',
                    'data' => $user
                ], 200);
            }else{
                  $this->response([
                    'status' => false,
                    'message' => 'Número de identificación o Password Incorrecto.'
                ], 200);
            }
        }else{
            // Set the response and exit
            $this->response([
                    'status' => false,
                    'message' => 'Favor Ingresar un Número de identificación y su contraseña.'
                ], 200);
        }
    }


        /*
        registration
        Typo de metodo: Post
        Ejemplo de llamada: http://localhost/sci/RequestUsersWebServer/registration
        Parametros requeridos:

        {
            "first_name": "John",
            "last_name": "Doe",
            "email": "john@example.com",
            "password": "login_pass",
            "phone": "123-456-7890",
            "IdentificationCard": "22500719137"
            "DocumentType": "1"
        }

        Autenticacion del API:

        User: sciadmin
        Password: sciadmin@

        X-API-KEY: sciadmin@sciadmin
    */
    
    public function registration_post() {
        // Get the post data
        $first_name = strip_tags($this->post('first_name'));
        $last_name = strip_tags($this->post('last_name'));
        $email = strip_tags($this->post('email'));
        $password = $this->post('password');
        $phone = strip_tags($this->post('phone'));
        $Identification = strip_tags($this->post('IdentificationCard'));
        $DocumentTypeId = strip_tags($this->post('DocumentType'));

        // Validate the post data
        if(!empty($first_name) && !empty($last_name) && !empty($email) && !empty($password) && !empty($Identification)){
            
            // Check if the given email already exists
            $con['returnType'] = 'count';
            $con['conditions'] = array(
                'IdentificationCard' => $Identification,
            );
            $userCount = $this->user->getRows($con);

            if($userCount > 0){
                // Set the response and exit
                $this->response([
                    'status' => false,
                    'message' => 'El Número de identificación ingresado ya esta asociado a una cuenta.'
                ], 200);
            }else{

                $conE['conditions'] = array(
                    'email' => $email,
                );
                $userCountE = $this->user->getRows($conE);

                if($userCountE > 0){
                    // Set the response and exit
                    $this->response([
                        'status' => false,
                        'message' => 'El Email ingresado ya esta asociado a una cuenta.'], 200);
                }else{
                    // Insert user data
                    $userData = array(
                        'first_name' => $first_name,
                        'last_name' => $last_name,
                        'email' => $email,
                        'password' => md5($password),
                        'phone' => $phone,
                        'IdentificationCard' => $Identification,
                        'DocumentType' => $DocumentTypeId,
                        'NeedResetPass' => 0
                    );
                    $insert = $this->user->insert($userData);
                    
                    // Check if the user data is inserted
                    if($insert){
                        // Set the response and exit
                        $this->response([
                            'status' => true,
                            'message' => 'Usuario Creado satisfactoriamente.',
                            'data' => $insert
                        ], 200);
                    }else{
                        // Set the response and exit
                        $this->response([
                        'status' => false,
                        'message' => 'Ha ocurrido un error, Por favor inténtelo de nuevo, Si el problema persiste favor contactar al Ayuntamiento'], 200);
                    }

                }
            }
        }else{
            // Set the response and exit
            $this->response(['status' => false,'message' => 'Información incompleta'], 200);
        }
    }

    /*
        user_get
        Typo de metodo: Post
        Ejemplo de llamada: http://localhost/sci/RequestUsersWebServer/user/$IdUsuario
        Parametros requeridos:

        Autenticacion del API:

        User: sciadmin
        Password: sciadmin@

        X-API-KEY: sciadmin@sciadmin
    */
    
    
    public function user_get($id = 0) {
        // Returns all the users data if the id not specified,
        // Otherwise, a single user will be returned.
        $con = $id?array('id' => $id):'';
        $users = $this->user->getRows($con);
        
        // Check if the user data exists
        if(!empty($users)){
            // Set the response and exit
            //OK (200) being the HTTP response code
            $this->response(['status' => true,'data' => $users], 200);
        }else{
            // Set the response and exit
            //NOT_FOUND (404) being the HTTP response code
            $this->response([
                'status' => false,
                'message' => 'El perfil de usuario no existe.'
            ], 200);
        }
    }

    
        /*
        user_put
        Typo de metodo: Put
        Ejemplo de llamada: http://localhost/sci/RequestUsersWebServer/user
        Parametros requeridos:

        {
            "id": 00, --Id usuario a modificar
            "first_name": "Doe",
            "last_name": "JOHAM",
            "email": "john2@example.com",
            "password": "123-456-7890",
            "phone": "123-456-7890"
        }

        Autenticacion del API:

        User: sciadmin
        Password: sciadmin@

        X-API-KEY: sciadmin@sciadmin
    */


    public function user_put() {
        $id = $this->put('id');
        
        // Get the post data
        $first_name = strip_tags($this->put('first_name'));
        $last_name = strip_tags($this->put('last_name'));
        $email = strip_tags($this->put('email'));
        $password = $this->put('password');
        $phone = strip_tags($this->put('phone'));
        
        // Validate the post data
        if(!empty($id) && (!empty($first_name) || !empty($last_name) || !empty($email) || !empty($password) || !empty($phone))){
            // Update user's account data
            $userData = array();
            if(!empty($first_name)){
                $userData['first_name'] = $first_name;
            }
            if(!empty($last_name)){
                $userData['last_name'] = $last_name;
            }
            if(!empty($email)){
                $userData['email'] = $email;
            }
            if(!empty($password)){
                $userData['password'] = md5($password);
            }
            if(!empty($phone)){
                $userData['phone'] = $phone;
            }
            $update = $this->user->update($userData, $id);
            
            // Check if the user data is updated
            if($update){
                // Set the response and exit
                $this->response([
                    'status' => TRUE,
                    'message' => 'La Información de usuario fue actualizada satisfactoriamente.'
                ],200);
            }else{
                // Set the response and exit
                $this->response([
                    'status' => false,
                    'message' => 'Ha ocurrido un error, Por favor inténtelo de nuevo, Si el problema persiste favor contactar al Ayuntamiento'
                ],200);
            }
        }else{
            // Set the response and exit
             $this->response([
                    'status' => false,
                    'message' => 'Proporcione al menos una información de usuario para actualizar.'
                ],200);
        }
    }


    /*
        documenttype_get
        Typo de metodo: Get
        Ejemplo de llamada: http://localhost/webapi/RequestUsersWebServer/documenttype
        Parametros requeridos:

        Autenticacion del API:

        User: sciadmin
        Password: sciadmin@

        X-API-KEY: sciadmin@sciadmin
    */

    public function documenttype_get() {

        $documents = $this->user->getDocumentType();
        
        // Check if the documents data exists
        if(!empty($documents)){
            // Set the response and exit
            //OK (200) being the HTTP response code
            $this->response(['status' => true,'data' => $documents], 200);
        }else{
            // Set the response and exit
            //NOT_FOUND (404) being the HTTP response code
            $this->response([
                'status' => false,
                'message' => 'No se encontro ningun tipo de documentos.'
            ], 200);
        }
    }



       /*
        Reset Password
        Typo de metodo: Post
        Ejemplo de llamada: http://localhost/sci/RequestUsersWebServer/passreset
        Parametros requeridos:

        {
            "email": "john@example.com",
            "IdentificationCard": "22500719137"
        }

        Autenticacion del API:

        User: sciadmin
        Password: sciadmin@

        X-API-KEY: sciadmin@sciadmin
    */
    
    public function passreset_post() {
        // Get the post data
        $Identification = strip_tags($this->post('IdentificationCard'));
        $email = strip_tags($this->post('email'));


        // Validate the post data
        if(!empty($Identification) && !empty($email)){
            // Validate if the user and de email exists

            $con2['conditions'] = array(
                'email' => $email,
                'status' => 1
            );
            $userEmailExist = $this->user->getRows($con2);

            if( $userEmailExist){
                $con['conditions'] = array(
                    'IdentificationCard' =>  $Identification,
                    'email' => $email,
                    'status' => 1
                );
                $user = $this->user->getRows($con);
    
                // Check if the user exist
                if($user){
    
                    $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
                    $userId = $user[0]['id'];
                    $first_name = $user[0]['first_name'];
                    $last_name = $user[0]['last_name'];
                    $identificationCard = $user[0]['IdentificationCard'];
                    $email = $user[0]['email'];
    
                    $newpassword = substr(str_shuffle($permitted_chars), 0, 10);
    
                    $userData = array();
    
                    $userData['password'] = md5($newpassword);
                    $userData['NeedResetPass'] = 1;
    
    
                    $update = $this->user->update($userData, $userId);
                    $template = $this->TemplateEmail($first_name,$last_name,$identificationCard,$newpassword);
    
    

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
                    $this->email->from('helpdesk@asnd.gob.do', 'Cambio de Password');
                    $this->email->to($email);
                                    
                        
                    $this->email->subject('Solicitud de Nueva Contraseña');
                    $this->email->message($template);
                        
                    $this->email->send();
                        

                   /* $config = Array(        
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
                    $this->email->to($email);
                    $this->email->subject('email subject');
                    $message = 'email body';                 
                    $this->email->message($message);
                   // $this->email->send();

                    if ($this->email->send()) {
                        echo 'Your Email has successfully been sent.';
                    } else {
                        show_error($this->email->print_debugger());
                    }*/


    
    
                    // Set the response and exit
                    $this->response([
                        'status' => true,
                        'message' => 'Se envio una nueva Contraseña, Por favor verifique su email.',
                        'data' => $user,
                        'NewPassword' =>  $newpassword,
                        'id' => $userId,
                        'email'=>$email
                    ], 200);
                }else{
                // Set the response and exit
                    $this->response([
                    'status' => false,
                    'message' => 'Ha ocurrido un error, Por favor inténtelo de nuevo, Si el problema persiste favor contactar al Ayuntamiento'], 200);
                }

            }else{
            // Set the response and exit
                $this->response([
                'status' => false,
                'message' => 'El email ingresado no existe.'], 200);
            }

        }else{
            // Set the response and exit
            $this->response(['status' => false,'message' => 'Información incompleta'], 200);
        }
    }


    /*
        Reset changePass
        Typo de metodo: put
        Ejemplo de llamada: http://localhost/sci/RequestUsersWebServer/changePass
        Parametros requeridos:

        {
            "id": 1,
            "password": "22500719137"
        }

        Autenticacion del API:

        User: sciadmin
        Password: sciadmin@

        X-API-KEY: sciadmin@sciadmin
    */

    public function changepass_put() {
        $id = $this->put('id');

        $password = $this->put('password');
        
        // Validate the post data
        if( !empty($password)){
            // Update user's account data
            $userData = array();
            if(!empty($password)){
                $userData['password'] = md5($password);
            }
            $update = $this->user->update($userData, $id);
            
            // Check if the user data is updated
            if($update){
                // Set the response and exit
                $this->response([
                    'status' => TRUE,
                    'message' => 'La Información de usuario fue actualizada satisfactoriamente.'
                ],200);
            }else{
                // Set the response and exit
                $this->response([
                    'status' => false,
                    'message' => 'Ha ocurrido un error, Por favor inténtelo de nuevo, Si el problema persiste favor contactar al Ayuntamiento'
                ],200);
            }
        }else{
            // Set the response and exit
             $this->response([
                    'status' => false,
                    'message' => 'Proporcione al menos una información de usuario para actualizar.'
                ],200);
        }
    }



    public function TemplateEmail($first_name,$last_name,$identificationCard,$newpassword)
    {
        $template = '<html>
        <header>
            <style>
            .table{
                border: 1px solid #000; 
            }
            .table tr td{
            border: 1px solid #000;
            padding: 5px;
            }
            </style>
        </header>
        <body>
            <table class="table">
                <tr>
                    <td><strong>Asunto</strong></td> 
                    <td>Solicitud de Nueva Contraseña</td>  
                    
                    <td><strong>Cedula</strong></td>
                    <td>' . $identificationCard . '</td> 
                </tr>
                <tr>
                    <td><strong>Nombre de usuario</strong></td>
                    <td>' . $first_name.' '.$last_name . '</td>  
                    
                    <td><strong>Nueva Contraseña</strong></td> 
                    <td>' . $newpassword . '</td> 
                    
                </tr>
            </table>
        </body>
        </html> ';

        return $template;
    }



}
