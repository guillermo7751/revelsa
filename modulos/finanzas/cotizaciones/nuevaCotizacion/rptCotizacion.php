<?php
	error_reporting(E_ERROR);
	
	require_once($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'revelsa'.DIRECTORY_SEPARATOR.'modelos'.DIRECTORY_SEPARATOR.'cotizaciones.class.php');

	$oCotizacion = new Cotizaciones();
	
	$oCotizacion->generaPDFCotizacion($_POST['cliente'],$_POST['direccion'],$_POST['infoTabla'],$_POST['tipoCotizacion'],
										$_POST['subtotal'],$_POST['iva'],$_POST['retIVA'],$_POST['total'],$_POST['tipoCliente'],
										$_POST['observaciones'],$_POST['vigencia'],$_POST['idUsuario']);
?>
