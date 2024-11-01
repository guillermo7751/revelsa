<?php

	//COMPRESIÓN DE DATOS
  if(substr_count($_SERVER["HTTP_ACCEPT_ENCODING"],"gzip"))
  {
      header('Content-Encoding: gzip');
      ob_start('ob_gzhandler');
  }
  else
  {
       ob_start();
  }

	$vEstilos = time();//Cambia la versión de los estilos
	$vScripts = time();//Cambia la versión de los scripts

  ini_set("display_errors",true);
  error_reporting(E_ALL);

  if(!isset($_SESSION))
  {
      session_start();
  }
  
  header("Cache-Control: no-cache, must-revalidate");
	require_once("motor/sesion.php");
	
?>
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?php echo 'Revelsa' ?></title>

    <!-- Bootstrap -->
    <link href="componentes/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <!-- Font Awesome -->
    <link href="componentes/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="componentes/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    
    <!-- DataTables -->
    <link href="componentes/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    
    <!-- Chosen -->
    <link href="componentes/chosen/chosen.min.css" rel="stylesheet">
    
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    
    <!-- Autocomplete -->
    <link href="componentes/autocomplete/jquery.autocomplete.css" rel="stylesheet">
    
    <!--===============================================================================================-->
	<?php echo '<link rel="stylesheet" type="text/css" href="css/login/util.css">
              <link rel="stylesheet" type="text/css" href="css/login/main.css">
              <link rel="stylesheet" type="text/css" href="css/contenido/sb-admin-2.css">
              <link rel="stylesheet" type="text/css" href="css/contenido/revelsa.css">
              '?>
	<!-- jQuery -->
    <script src="componentes/jquery/dist/jquery.min.js"></script>
  <!-- Popper -->
   <script src="componentes/popper/popper.min.js"></script>
   
   <!-- Bootstrap core JavaScript-->
  <script src="componentes/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Core plugin JavaScript-->
  <script src="componentes/jquery-easing/jquery.easing.min.js"></script>

  <!-- Page level plugins -->
  <script src="componentes/chart.js/Chart.min.js"></script>
    
  <!-- DataTables -->
  <script src="componentes/datatables/jquery.dataTables.min.js"></script>
  <script src="componentes/datatables/dataTables.bootstrap4.min.js"></script>
  
  <!-- Chosen -->
  <script src="componentes/chosen/chosen.jquery.min.js"></script>
  
  <!-- Autocomplete -->
  <script src="componentes/autocomplete/jquery.autocomplete.min.js"></script>
  
  
  </head>
	<?php revelsa_muestraContenido();?>

	<?php echo '<script src="js/encripta.js?v='.$vScripts.'"></script>' ?>
	<?php echo '<script src="js/sistema.js?v='.$vScripts.'"></script>' ?>
  <?php echo '<script src="js/sb-admin-2.min.js"></script>' ?>
  <?php echo '<script src="js/funcionesGlobales.js"></script>' ?>
  <?php echo '<script src="js/tools.js"></script>' ?>
  <?php echo '<script src="js/uploader.js"></script>' ?>
</html>
