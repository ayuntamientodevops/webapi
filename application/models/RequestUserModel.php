<?php
class RequestUserModel extends CI_Model
{
	public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->userTbl = 'Sol_users';
    }

	/*
     * Get rows from the users table
     */
    function getRows($params = array()){
        $this->db->select('*');
        $this->db->from($this->userTbl);
        
        //fetch data by conditions
        if(array_key_exists("conditions",$params)){
            foreach($params['conditions'] as $key => $value){
                $this->db->where($key,$value);
            }
        }
        
        if(array_key_exists("id",$params)){
            $this->db->where('id',$params['id']);
            $query = $this->db->get();
            $result = $query->row_array();
        }else{
            //set start and limit
            if(array_key_exists("start",$params) && array_key_exists("limit",$params)){
                $this->db->limit($params['limit'],$params['start']);
            }elseif(!array_key_exists("start",$params) && array_key_exists("limit",$params)){
                $this->db->limit($params['limit']);
            }
            
            if(array_key_exists("returnType",$params) && $params['returnType'] == 'count'){
                $result = $this->db->count_all_results();    
            }elseif(array_key_exists("returnType",$params) && $params['returnType'] == 'single'){
                $query = $this->db->get();
                $result = ($query->num_rows() > 0)?$query->row_array():false;
            }else{
                $query = $this->db->get();
                $result = ($query->num_rows() > 0)?$query->result_array():false;
            }
        }

        //return fetched data
        return $result;
    }

    
    /*
     * Insert user data
     */

    public function insert($data){
        //add created and modified date if not exists
        if(!array_key_exists("created", $data)){
            $data['created'] = date("Y-m-d H:i:s");
        }
        if(!array_key_exists("modified", $data)){
            $data['modified'] = date("Y-m-d H:i:s");
        }
        
        //insert user data to users table
        $insert = $this->db->insert($this->userTbl, $data);
        
        //return the status
        return $insert?$this->db->insert_id():false;
    }
    
    /*
     * Update user data
     */

    public function update($data, $id){
        //add modified date if not exists
        if(!array_key_exists('modified', $data)){
            $data['modified'] = date("Y-m-d H:i:s");
        }
        
        //update user data in users table
        $update = $this->db->update($this->userTbl, $data, array('id'=>$id));
        
        //return the status
        return $update?true:false;
    }
    
    /*
     * Delete user data
     */
	
    public function delete($id){
        //update user from users table
        $delete = $this->db->delete('users',array('id'=>$id));
        //return the status
        return $delete?true:false;
    }


    /* get document type */


    public function getDocumentType()
	{
        //SELECT id ,descripcion, appDocument  FROM jefplamy_sci_db_dev.categoria_documento;

			$this->db->select("categoria_documento.id as IdTipoDocumento, categoria_documento.descripcion as DescripcionDocumento, categoria_documento.appDocument as IsMovilDocument");
			$this->db->from('categoria_documento');
			$this->db->where('categoria_documento.appDocument', 1);

			$query = $this->db->get();

			$Images = json_decode(json_encode($query->result()), true);

			return $Images;
	}

    public function SentEmail($email2)
	{
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
        $this->email->to($email2);
        $this->email->subject('email subject');
        $message = 'email body';                 
        $this->email->message($message);

        var_dump( $this->email->send());
        
       // $this->email->send();
	}






}
