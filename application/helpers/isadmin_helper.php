<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*To redirect is not logged as admin*/
if ( ! function_exists('isAdmin'))
{
	function isAdmin(){
		$CI = &get_instance();
		$loginPage = 'login';
		if($loginPage != $CI->uri->segment(2)){
			if(!$CI->session->userdata('logged_in'))
				redirect('admin/login');
		}
	}
	
}
/*To know if user admin is logged*/
if ( ! function_exists('isLoggedAdmin'))
{
	function isLoggedAdmin(){
		$CI = &get_instance();
		if(!$CI->session->userdata('logged_in')) return false;
		
		return true;
	}
	
}

?>
