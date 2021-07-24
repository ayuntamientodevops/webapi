<?php
class RequestTypeModel extends CI_Model
{


	public function GetAllRequestType()
	{
		$this->db->select("TipoReclamacionId, Descripcion,Activo");
		$this->db->from('Sol_TipoReclamaciones');
		$this->db->order_by('TipoReclamacionId', 'DESC');

		$query = $this->db->get();

		return $query->result();
	}

	public function CreateRequestType($data)
    {
        $this->db->insert('Sol_TipoReclamaciones',$data);                  
        $insert_id = $this->db->insert_id();
        return $insert_id;
	}

	function updateRequestType($id, $nombre)
	{
		$this->db->where('id', $id);
		$this->db->set('nombre', $nombre);
		return $this->db->update('editoriales');
	}


	public function GetAllRequestTypeById($id)
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

}
