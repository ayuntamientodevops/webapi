<?php
class RequestModel extends CI_Model
{

	public function RequestByRequestDate($RequestDate)
	{
		$this->db->select("Sol_Reclamaciones.ReclamacionId, Sol_Reclamaciones.FechaSolicitud,Sol_TipoReclamaciones.Descripcion as TipoReclamacion, 
		Sol_EstatusReclamaciones.Descripcion as EstatusReclamacion, Sol_Reclamaciones.Sector ");
		$this->db->from('Sol_Reclamaciones');
		$this->db->join('Sol_TipoReclamaciones', 'Sol_Reclamaciones.TipoReclamacionId = Sol_TipoReclamaciones.TipoReclamacionId');
		$this->db->join('Sol_EstatusReclamaciones', 'Sol_Reclamaciones.EstatusReclamacionId = Sol_EstatusReclamaciones.EstatusReclamacionId');
		
		if (!empty($fecha1) && !empty($fecha2))
			$this->db->where('CONVERT(Sol_Reclamaciones.FechaSolicitud, DATE)' . $RequestDate);

		$this->db->order_by('Sol_Reclamaciones.FechaSolicitud', 'DESC');
		$query = $this->db->get();

		return $query->result();
	}


	public function GetAllRequest()
	{
		$this->db->select("Sol_Reclamaciones.ReclamacionId, Sol_Reclamaciones.FechaSolicitud,Sol_TipoReclamaciones.Descripcion as TipoReclamacion, 
		Sol_EstatusReclamaciones.Descripcion as EstatusReclamacion, Sol_Reclamaciones.Sector ");
		$this->db->from('Sol_Reclamaciones');
		$this->db->join('Sol_TipoReclamaciones', 'Sol_Reclamaciones.TipoReclamacionId = Sol_TipoReclamaciones.TipoReclamacionId');
		$this->db->join('Sol_EstatusReclamaciones', 'Sol_Reclamaciones.EstatusReclamacionId = Sol_EstatusReclamaciones.EstatusReclamacionId');
		$this->db->order_by('Sol_Reclamaciones.FechaSolicitud', 'DESC');

		$query = $this->db->get();

		return $query->result();
	}


	public function RequestById($id)
	{

		$this->db->select("Sol_Reclamaciones.ReclamacionId, Sol_Reclamaciones.FechaSolicitud,Sol_TipoReclamaciones.Descripcion as TipoReclamacion, 
		Sol_EstatusReclamaciones.Descripcion as EstatusReclamacion, Sol_Reclamaciones.Sector ");
		$this->db->from('Sol_Reclamaciones');
		$this->db->join('Sol_TipoReclamaciones', 'Sol_Reclamaciones.TipoReclamacionId = Sol_TipoReclamaciones.TipoReclamacionId');
		$this->db->join('Sol_EstatusReclamaciones', 'Sol_Reclamaciones.EstatusReclamacionId = Sol_EstatusReclamaciones.EstatusReclamacionId');
		$this->db->where('Sol_Reclamaciones.ReclamacionId', $id);
		$this->db->order_by('fecha_inscripcion', 'DESC');

		$query = $this->db->get();

		return $query->row();
	}


	public function GetImageByRequest($RequestId)
	{
			$this->db->select("Sol_ImagenesReclamacion.ReclamacionId, Sol_ImagenesReclamacion.Imagen, Sol_ImagenesReclamacion.RutaImagen ");
			$this->db->from('Sol_ImagenesReclamacion');
			$this->db->where('Sol_ImagenesReclamacion.ReclamacionId', $RequestId);

			$query = $this->db->get();

			$Images = json_decode(json_encode($query->result()), true);

			return $Images;
	}



	public function RequestByUser($UserId)
	{
			$this->db->select("Sol_Reclamaciones.ReclamacionId, Sol_Reclamaciones.Descripcion, Sol_Reclamaciones.FechaSolicitud,Sol_TipoReclamaciones.Descripcion as TipoReclamacion, 
			Sol_EstatusReclamaciones.Descripcion as EstatusReclamacion, Sol_Reclamaciones.Sector, Sol_Reclamaciones.Latitud, Sol_Reclamaciones.Longitud, Sol_Reclamaciones.ReferenciaDireccion,
			Sol_Reclamaciones.FechaAperturaIncidente, Sol_Reclamaciones.FechaResolucion  ");
			$this->db->from('Sol_Reclamaciones');
			$this->db->join('Sol_TipoReclamaciones', 'Sol_Reclamaciones.TipoReclamacionId = Sol_TipoReclamaciones.TipoReclamacionId');
			$this->db->join('Sol_EstatusReclamaciones', 'Sol_Reclamaciones.EstatusReclamacionId = Sol_EstatusReclamaciones.EstatusReclamacionId');
			$this->db->where('Sol_Reclamaciones.UsuarioSolicita', $UserId);
			$this->db->order_by('Sol_Reclamaciones.FechaSolicitud', 'DESC');

			$query = $this->db->get();

			$Request = json_decode(json_encode($query->result()), true);

			$result = array();

			foreach ($Request as $R) {
				$requestwithImage = array(
					'ReclamacionId' => $R['ReclamacionId'],
					'Descripcion' => $R['Descripcion'],
					'FechaSolicitud' => $R['FechaSolicitud'],
					'TipoReclamacion' => $R['TipoReclamacion'],
					'EstatusReclamacion' => $R['EstatusReclamacion'],
					'Latitud' => $R['Latitud'],
					'Longitud' => $R['Longitud'],
					'Sector' => $R['Sector'],
					'ReferenciaDireccion' => $R['ReferenciaDireccion'],
					'FechaAsignacion' => $R['FechaAperturaIncidente'],
					'FechaResolucion' => $R['FechaResolucion']
					// 'Pictures' =>$this->GetImageByRequest($R['ReclamacionId'])
				);

				array_push($result,$requestwithImage);
			}

			return $result;

	}


	public function InsertRequest($data = array())
	{
		$request = array(
			'Descripcion' => $data['Description'],
			'UsuarioSolicita' => $data['UserRequested'],
			'FechaSolicitud' => date('Y-m-d H:i:s'),
			'Latitud' => $data['Latitude'],
			'Longitud' => $data['Longitude'],
			'TipoReclamacionId' => $data['RequestType'],
			'EstatusReclamacionId' => 1,
			'Sector' => $data['Sector'],
			'ReferenciaDireccion' => $data['ReferenceAddress'],
			'FechaAperturaIncidente' =>null,
			'FechaResolucion' => null,
			'UsuarioSolucionaId' => null
		);

		$imagenes = $data['RequestImages'];

		if(count($imagenes) > 0)
		{

			$this->db->trans_start(); # Starting Transaction
			$this->db->trans_strict(FALSE);

			$result = $this->db->insert('Sol_Reclamaciones', $request);
			$requestId = $this->db->insert_id();


			if ($result) {
				foreach ($imagenes as $images) {
					$img = array(
						'ReclamacionId' => $requestId,
						'TipoImagenReclamacionID' => 1,
						'Imagen' => $images,
						'RutaImagen' => '',
						'FechaCreacion' => date('Y-m-d H:i:s'),
						'Activo' => 1
					);

					$this->db->insert('Sol_ImagenesReclamacion', $img);
				}
			}

			$this->db->trans_complete();

			if ($this->db->trans_status() === FALSE) {
				# Something went wrong.
				$this->db->trans_rollback();
				return FALSE;
			} 
			else {
				# Everything is Perfect. 
				# Committing data to the database.
				$this->db->trans_commit();
				return TRUE;
			}
			
		}else{
			return false;
		}

	}
}
