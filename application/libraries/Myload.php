<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class MyLoad
{
	var $siteName = "SCI";

	var $spliterTitle = " .::. ";


	public function view($view, $data = array())
	{

		$CI = &get_instance();
		$session = $CI->session->userdata('logged_in');

		//For send data to views inner ours views
		$data['data'] = $data;
		//Title page
		$data['titlepage'] = (isset($data['titlepage'])) ? $this->siteName . $this->spliterTitle . $data['titlepage'] : $this->siteName;
		//Name to load the view - (Must be unique)
		$data['viewToLoad'] = $view;

		//to highlight the current page
		$data['page_name'] = (@$data['page_name']) ? $data['page_name'] : $view;
		$CI->load->model('ConfigModel', '', TRUE);

		/*		//idetificar que browser se esta usando
		$CI->load->library('user_agent');
		$data["msg_browser"] = 0;
		if ($CI->agent->browser() == "Internet Explorer" )
		{
			if ($CI->agent->version() < "7.0") //si el browser es internet explorer 6 o menor
			{
				$data["msg_browser"] = 1;
			}
		}
		// -------------------------------------------------------- //*/
		$dataBirthday = $CI->ConfigModel->GetBirthdayUser($session['id_usuario']);
		$data['menu'] = $CI->ConfigModel->getMenuByUserId($session['id_usuario']);
 
		$CI->load->view("template/header", $data);
		$CI->load->view($view, $data);
		
		$data['birthday'] = array(
			'data' => $dataBirthday 
		);
		$CI->load->view("template/footer", $data);
		 
	}
 
}
