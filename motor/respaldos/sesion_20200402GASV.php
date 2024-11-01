<?php


ini_set("display_errors",1);
error_reporting(E_ALL);

include_once("control.php");
include_once("funciones.php");


function revelsa_muestraContenido()
{
	conectarRevelsa();

	
	if(isset($_SESSION['idUsuario']) && ($_SESSION['SERVER']==$_SERVER['SERVER_NAME']))//Usuario logueado con anterioridad.
	{	
		$cuerpo = revelsa_muestraSistema();
	}
	elseif( (isset($_POST['txtUsr']) && trim($_POST['txtUsr'])!="" && trim($_POST['txtPassword'])!=""))//Logueo normal y automático
	{
		//echo "<pre>";
		//print_r($_POST);
		//die();
		
		$arrValidaAcceso = revelsa_validaAccesoUsuario($_POST['txtUsr'],$_POST['txtPassword']);
			
		if($arrValidaAcceso['acceso'])
		{
			//echo "ACCESO CONCEDIDO";
			//die();
			
			$_SESSION['idUsuario'] = $arrValidaAcceso['datos']['idUsuario'];
			$_SESSION['usuario'] = $arrValidaAcceso['datos']['nombre'];
			$_SESSION['correo'] = $arrValidaAcceso['datos']['correo'];
			$_SESSION['idPerfil'] = $arrValidaAcceso['datos']['idPerfil'];
			$_SESSION['SERVER'] = $_SERVER['SERVER_NAME'];
			
			
			//echo 'correos'. $_SESSION['correo'];
			
			if(isset($_POST['idModulo']) && $_POST['idModulo']>0)//Determina si el módulo autmático.
			{
				$_SESSION['modulo'] = $_POST['idModulo'];
			}
			else
			{
				$_SESSION['modulo'] = '';
			}
			
			//echo '<pre>';print_r($_SESSION);die();
			
			$cuerpo = revelsa_muestraSistema();
		}
		else
		{
			$cuerpo = revelsa_muestraLogin(false,$arrValidaAcceso['mensaje']);
		}
	}
	else
	{
	
		$cuerpo = revelsa_muestraLogin(true,'');
	}
	
	echo $cuerpo;
	
}

function revelsa_validaAccesoUsuario($usr,$password)
{
	$arrRetornoValida = array();
	
	$sqlValida = "CALL sp_login_validaLogin(?,?)";
	$filaValida = bd_preparaSQL($sqlValida,array($usr=>'s',$password=>'s'),1);
	
	if($filaValida['Error'])
	{
		$arrRetornoValida['acceso'] = false;
		$arrRetornoValida['mensaje']='Error en el sistema: '. $filaValida['ErrorMensaje'];
		$arrRetornoValida['datos']=array();
	}
	else
	{
		if($filaValida['registros'] > 0)
		{	
			$arrRetornoValida['acceso']=true;
			$arrRetornoValida['mensaje']='Acceso ok.';
			$arrRetornoValida['datos']=$filaValida['resultado'];
		}
		else
		{
			$arrRetornoValida['acceso'] = false;
			$arrRetornoValida['mensaje']='Acceso incorrecto. Favor de validar sus datos.';
			$arrRetornoValida['datos']=array();
		}
	}
	
	return $arrRetornoValida;
	
}

function revelsa_activaAjaxSesion()
{
	session_start();	
}

function revelsa_cerrarSesion()
{
	bd_desconectarRevelsa();
	
	session_unset();
	session_destroy();
    
	
	//ESTABLEZO LA CONFIGURACIÓN INICIAL
	session_start();
}

?>
