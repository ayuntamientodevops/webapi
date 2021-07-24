 <?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Bienvenidos a Redartsources!</title>
	<link rel="stylesheet" type="text/css" href="<?=base_url();?>css/bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="<?=base_url();?>css/home.css" />
	<link rel="stylesheet" type="text/css" href="<?=base_url();?>css/font-awesome.css" />
	<link rel="stylesheet" type="text/css" href="<?=base_url();?>css/login-style.css" />
	<script type="text/javascript" src="<?=base_url()?>js/jquery-1.8.3.min.js"></script>
</head>
<body> 
<style>
.noscript{
  background-color: #CA2424;
  color: #fff;
}
.noscript a{
	  color: #C9F054;
}
 body{
 background: url('<?=base_url();?>images/3.jpg');
  background-size: cover;
  background-attachment: fixed;
}
</style>
 <div class="header">
   <div class="logoRedart">Redartsources</div>
 </div>
 <div class="container">
   <div class="row">
	  <div class="col-md-8">
         <div class="col-md-12">			
		     <div class="form-horizontal templatemo-login-form-2" role="form">
			    <div class="row">
                <div class="noscript">
            
                Para que este web site funcione correctamente, 
                es necesario habilitar javascript en tu navegador.
            
                <a href="http://www.enable-javascript.com/" target="_blank">
                En este enlace encontrarás instrucciones de 
                cómo habilitar javascript en tu navegador</a> 
            
             </div>
	    </div>
      </div>
     </div>
    </div>
  </div>
 
  </div> 
<div class="footer-redartsources">
   <div class="footer-center">
      <ul style="margin-bottom: 0px;padding-left: 0px;">
<?php

$menu_footer = array(
'Nosotros'  => '#',
'Preguntas' => '#',
'Registro'  => '#',
'Facebook'	=> 'https://www.facebook.com/',
'Twitter'   => '#',
'Blog'   	=> '#',
'Ayuda'   	=> '#',
'Desarrollo' => '#',
'Noticias' 	=> '#',
'Contactanos' => '#',
'Mapa' 		=> '#',

);
 $i = array();
foreach($menu_footer as $key => $val){ 

 $i[] = '<li><a href="'.$val.'">'.$key.'</a></li>'; ?> 
  
<?php
 }
 echo implode(' - ',$i);
?>
        </ul>
       <p class="copyright">Todos los derechos reservados Power By Redartsources © <?=date('Y');?>.</p>
     </div>
    </div>
  </body>
</html>		