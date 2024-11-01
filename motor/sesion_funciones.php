<?php
	
	session_start();

	$_POST['accion_ajax_sesion'] = isset($_POST['accion_ajax_sesion']) ? $_POST['accion_ajax_sesion']:'';
	
	if(!empty($_POST['accion_ajax_sesion'])) //SE VERIFICA SI LA ACCION SOLICITADA FUE VÍA AJAX
	{
		$ajax = validaDatosSesion($_POST['i'],$_POST['i2']);
	   
	    if($ajax!='')
		{
			header("Content-Type: application/json");
			echo json_encode(($ajax));
		}
		
	}
	
	function validaDatosSesion($a_hash,$a_hash64)
	{
		$arrRetorno = array('Error'=>false,'ErrorMensaje'=>'');
		$mensajeError = '';
		
		$usr = isset($_SESSION['idUsuario']) ? $_SESSION['idUsuario']:'';
		$prf = isset($_SESSION['idPerfil']) ? $_SESSION['idPerfil']:'';
		
		$hash = md5($usr.'valida');//Armo el hash
		
		//echo $a_hash64.'-->'.$a_hash;
		
		if(base64_decode($a_hash64)==$a_hash)//Verifico mi control, que no se han modificado
		{
			if($a_hash!=$hash)//Los datos no corresponden, cierro la sesión.
			{
				if(trim($prf)=='')
				{
					$mensajeError.='Error, la sesión se ha perdido.';
				}
			}
		}
		else
		{
			$mensajeError.='Error, sesión no válida.';
		}
		
		if($mensajeError <> '')
		{
			$arrRetorno['Error'] = true;
			$arrRetorno['ErrorMensaje'] = $mensajeError;
		}
		
		return $arrRetorno;
	}

?>