<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('generateUser')) {
	function generateUser($nombre, $apellido, $isCreate = TRUE)
	{
		$CI = &get_instance();

		$nombre =  (properText($nombre));
		$apellido =  (properText($apellido));

		$result1 = explode(" ", cleanSpecialCharacters($nombre), 2);
		$result2 = explode(" ", cleanSpecialCharacters($apellido), 2);
		$usuario = rtrim(ltrim($result1[0] . "." . $result2[0]));

		$CI->db->select('id_usuario');
		$CI->db->where('usuario', $usuario);
		$query = $CI->db->get('usuarios');
		if (!$isCreate) {
			if ($query->num_rows() != 0) {
				$CI->db->select_max('id_usuario');
				$max = $CI->db->get('usuarios')->result()[0];

				$usuario = $usuario . $max->id_usuario;
			}
		}
		return strtolower($usuario);
	}
}
if (!function_exists('cleanSpecialCharacters')) {
	function cleanSpecialCharacters($string)
	{
		$string = str_replace(' ', '', $string); // Replaces all spaces with hyphens.
		$string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.

		return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
	}
}

if (!function_exists('properText')) {
	function properText($string)
	{
		$string = trim($string);

		$string = str_replace(
			array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
			array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
			$string
		);

		$string = str_replace(
			array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
			array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
			$string
		);

		$string = str_replace(
			array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
			array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
			$string
		);

		$string = str_replace(
			array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
			array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
			$string
		);

		$string = str_replace(
			array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
			array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
			$string
		);

		$string = str_replace(
			array('ñ', 'Ñ', 'ç', 'Ç'),
			array('n', 'N', 'c', 'C',),
			$string
		);


		//Esta parte se encarga de eliminar cualquier caracter extraño
		$string = str_replace(
			array(
				"\\", "¨", "º", "-", "~",
				"#", "@", "|", "!", "\"",
				"·", "$", "%", "&", "/",
				"(", ")", "?", "'", "¡",
				"¿", "[", "^", "`", "]",
				"+", "}", "{", "¨", "´",
				">", "< ", ";", ",", ":",
				"."
			),
			'',
			$string
		);

		return $string;
	}
}
if (!function_exists('validateFieldForm')) {
	function validateFieldForm($data = array(), $listPermitidos = array())
	{
		$result = array();
		foreach ($data as $key => $val) {
			if (in_array($key, $listPermitidos)) {  
				if (empty($val) || count(array($val)) == 0) {
				 	array_push($result, $key);
				}
			}
		}
		return $result;
	}
}
if (!function_exists('validateFieldFormArray')) {
	function ValidateFieldFormArray($data = array())
	{ 
		$result = array();
		foreach ($data as $key) {
			foreach ($key as $k => $val ) { 
				if (empty($val) || count(array($val)) == 0) {
					array_push($result, $k);
			   }
			} 
		}
		return $result;
	}
}
if (!function_exists('formatDateTimeAll')) {
	function formatDateTimeAll($timestamp, $type)
	{
		$hora = strftime("%I:%M", strtotime($timestamp)) . date(' a', strtotime($timestamp));
		if ($type === 1) {
			$fecha = utf8_encode(strftime("%A %d de %B del %Y", strtotime($timestamp)));

			return $fecha . " " . $hora;
		} else
			if ($type === 2) {
			$fecha = utf8_encode(strftime("%d/%b/%Y", strtotime($timestamp)));

			return $fecha . " " . $hora;
		} else
			if ($type === 3) {
			$fecha = utf8_encode(strftime("%d/%b/%Y", strtotime($timestamp)));

			return $fecha;
		}
	}
}


if (!function_exists('formatArrayWithComma')) {
	function formatArrayWithComma($data = array())
	{
		$string = rtrim(implode(', ', $data), ', ');

		return $string;
	}
}

if (!function_exists('validatePassword')) {
	function validatePassword($pass1, $pass2)
	{
		$hash = password_hash($pass1, PASSWORD_DEFAULT);

		if (password_verify($pass2, $hash)) {
			return true;
		}

		return false;
	}
}
if (!function_exists('SeeMore')) {
	function SeeMore($text, $limit)
	{
		$countText = strlen($text);
		if ($countText <= $limit)
			return $text;
		else
			return substr($text, 0, $limit) . '...';
	}
}
 
if (!function_exists('AlertTicketDate')) {
	function AlertTicketDate($date1, $date2)
	{
		$start = new DateTime($date1);
		$end = new DateTime($date2);
		// otherwise the  end date is excluded (bug?)
		$end->modify('+1 day');

		$interval = $end->diff($start);

		// total days
		$days = $interval->days;

		// create an iterateable period of date (P1D equates to 1 day)
		$period = new DatePeriod($start, new DateInterval('P1D'), $end);

		// best stored as array, so you can add more than one
		$holidays = array('2021-02-02', '2021-06-03', '2021-08-16', '2021-09-24');

		foreach ($period as $dt) {
			$curr = $dt->format('D');

			// substract if Saturday or Sunday
			if ($curr == 'Sat' || $curr == 'Sun') {
				$days--;
			}

			// (optional) for the updated question
			elseif (in_array($dt->format('Y-m-d'), $holidays)) {
				$days--;
			}
		}
		return $days;
	}
}
