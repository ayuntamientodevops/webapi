<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
use chriskacerguis\RestServer\RestController;

class RequestWebServer extends RestController
{
	function __construct()
    {
        // Construct the parent class
		// For references please use: https://github.com/chriskacerguis/codeigniter-restserver

        parent::__construct();
		$this->load->model('RequestTypeModel', '', TRUE);
        $this->load->model('RequestModel', '', TRUE);
    }

	public function RequestTypeList()
	{
		$data = array();
		foreach ($this->RequestTypeModel->GetAllRequestType() as $item) {
			$arrayList = array(
				'TipoReclamacionId' => $item->TipoReclamacionId,
				'Descripcion' => $item->Descripcion,
				'Activo' => $item->Activo
			);

			array_push($data, $arrayList);
		}
		return $data;
	}

    function insertrequest_post()
    {
        /*
            {
                "Description": "Recoger Basura Desde el Web Api",
                "UserRequested": "10",
                "Latitude": "18.1563145",
                "Longitude": "18.1563145",
                "RequestType": "1",
                "Sector": "Sabana Perdida",
                "ReferenceAddress": "Entrando por la Bomba Total",
                "RequestImages": {
                    "1" : "Image1",
                    "2" : "Image2"
                }
            }
        */
        
        
        $result = $this->RequestModel->InsertRequest($this->post());

        if($result === FALSE)
        {
            $this->response( [
                'status' => false,
                'message' => 'Se ha producido un error al insertar la solicitud, inténtelo de nuevo.'
            ], 200 );
        }
        
        else
        {
            $this->response( [
                'status' => true,
                'message' => 'Solicitud Enviada.'
            ], 200 );

        }

    }



    public function requesttype_get()
    {
        // Request Type From Database
		/*
			http://localhost/sci/RequestWebServer/requesttype/ will return the list of all Request Type
			http://localhost/sci/sci/RequestWebServer/requesttype/id/1 will only return information about the Request Type with id = 1		
		*/
        $RTypeList= $this->RequestTypeList();


        $id = $this->get( 'id' );

        if ( $id === null )
        {
            // Check if the request list data store contains Request Type
            if ( $RTypeList )
            {
                // Set the response and exit
                $this->response( [
                    'status' => true,
                    'data' =>  $RTypeList
                ], 200 );
            }
            else
            {
                // Set the response and exit
                $this->response( [
                    'status' => false,
                    'message' => 'No se ha encontrado ningún tipo de solicitud.'
                ], 200 );
            }
        }
        else
        {
            if ( array_key_exists( $id, $RTypeList ) )
            {
                $this->response( [
                    'status' => true,
                    'data' =>  $RTypeList[$id]
                ], 200 );
            }
            else
            {
                $this->response( [
                    'status' => false,
                    'message' => 'No se ha encontrado ningún tipo de solicitud.'
                ], 200 );
            }
        }
    }


    public function getrequestuser_get()
    {
        // Request Type From Database
		/*
			http://localhost/sci/RequestWebServer/getrequestuser/ will return the list of all Request Type
			http://localhost/sci/sci/RequestWebServer/getrequestuser/user/10 will only return information about the Request Type with id = 1		
		*/
        $id = $this->get( 'user' );

        $RequestByUser=$this->RequestModel->RequestByUser($id);

        // Check if the request list data store contains Request Type
        if ( $RequestByUser )
        {
            // Set the response and exit
             $this->response( [
                'status' => true,
                'data' => $RequestByUser
            ], 200 );
        }
        else
        {
            // Set the response and exit
            $this->response( [
                'status' => false,
                'message' => 'No se encontró ninguna solicitud.'
            ], 200 );
        }

    }

    public function getimagebyrequest_get()
    {
        // Request Type From Database
        /*
            http://localhost/sci/RequestWebServer/getimagebyrequest/ will return the list of all Request Type
            http://localhost/sci/sci/RequestWebServer/getimagebyrequest/id/10 will only return information about the Request Type with id = 1        
        */
        $id = $this->get( 'id' );

        $RequestImage=$this->RequestModel->GetImageByRequest($id);

        // Check if the request list data store contains Request Type
        if ( $RequestImage )
        {
            // Set the response and exit
             $this->response( [
                'status' => true,
                'data' => $RequestImage
            ], 200 );
        }
        else
        {
            // Set the response and exit
            $this->response( [
                'status' => false,
                'message' => 'No se encontró ninguna Imagen asociada a esta solicitud.'
            ], 200 );
        }

    }
}
