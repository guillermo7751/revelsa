<?php

if(!isset($_SESSION))
{
	session_start();
}
			
ini_set("display_errors",1);
error_reporting(E_ALL);

function conectarRevelsa()
{
	//CEPM 20161006 (19323)
	//bd_desconectarRevelsa();
	
	//$usuario = "revelsa_user";
	//$pass = "DC!!rJn2r=Yt3PL9KQh@@";
	$usuario = "root";
	$pass = '';
	$bd = "revelsa";
	$servidor = "127.0.0.1";
	
	$conexion = mysqli_connect($servidor, $usuario, $pass, $bd); 

	if(mysqli_connect_errno())
	{
		echo "La conexión falló: " . mysqli_connect_error();
		return;
	}
	else
	{
		/**
		* Se asigna la conexión en una variable de sesion
		*/

		$_SESSION['REVELSA'] = $conexion;

	}

}

function bd_desconectarRevelsa()
{
	mysqli_close($_SESSION['REVELSA']);
}


function bd_consultaSQL($sql)
{
	$res = mysqli_query($_SESSION['REVELSA'],$sql);
	libera_consulta($_SESSION['REVELSA']);
	return $res;
}

function bd_preparaSQL($sql,$arrParametros,$procesaRS=2)
{
	$arrRetorno = array('Error'=>false,'ErrorMensaje'=>'','registros'=>0,'resultado'=>array());
	
	$stmt = mysqli_prepare($_SESSION['REVELSA'],$sql);

	if(sizeof($arrParametros)>0)
	{
		$cadenaTipos = '';
		$arrTempParam = array();
		foreach($arrParametros as $parametro=>$tipoEnCurso)
		{
			$cadenaTipos.=$tipoEnCurso;
			array_push($arrTempParam,$parametro);
		}
		
		mysqli_stmt_bind_param($stmt,$cadenaTipos,...$arrTempParam);
	}
	
	mysqli_stmt_execute($stmt);
	
	if(mysqli_stmt_errno($stmt))
	{
		 $arrRetorno['Error']=true;
		 $arrRetorno['ErrorMensaje']=mysqli_stmt_error($stmt);
	}
	else
	{
		
		$res = mysqli_stmt_get_result($stmt);
		
		$arrAux = array();
		
		if($procesaRS==2)//PROCESO EL RESULTSET DEVOLVIENDO UN ARREGLO DE UN NUMERO DETERMINADO DE FILAS
		{
			$arrRetorno['registros'] = bd_cuentaRegistros($res);
			
			if($arrRetorno['registros'] > 0)
			{
				while($fila = bd_dameRegistro($res))
				{
					array_push($arrAux,$fila);
				}
			}
		}
		elseif($procesaRS==1)//PROCESO EL RESULTSET DEVOLVIENDO UN ARREGLO CON UNA SOLA FILA
		{
			$arrRetorno['registros'] = bd_cuentaRegistros($res);
			
			if($arrRetorno['registros']>1)
			{
				$arrRetorno['Error']=true;
				$arrRetorno['ErrorMensaje']='La consulta devuelve más de un registro.';
			}
			elseif($arrRetorno['registros'] == 1)
			{
				$arrAux = bd_dameRegistro($res);
			}
		}
		
		$arrRetorno['resultado'] = $arrAux;
	}
	
	mysqli_stmt_close($stmt);
	
	return $arrRetorno;
}

function bd_cuentaRegistros($resulset)
{
	return mysqli_num_rows($resulset);
}

function bd_dameRegistro($resulset)
{
	$arrAux = mysqli_fetch_assoc($resulset);
	
	$arrAux = procesaArrayLineal($arrAux);
	
	return $arrAux;
}

function bd_insert($sql)
{
	$res = mysqli_query($_SESSION['REVELSA'],$sql);
	
	$id = mysqli_insert_id($_SESSION['REVELSA']);
	
	return $id;
}

function bd_error(){
	return mysqli_errno($_SESSION['REVELSA']);	
}

function bd_mensajeError(){
	return mysqli_error($_SESSION['REVELSA']);	
}

function bd_inicia_transaccion(){
	mysqli_autocommit($_SESSION['REVELSA'],FALSE);
}

function bd_commit(){
	mysqli_commit($_SESSION['REVELSA']);
	bd_termina_transaccion();
}

function bd_rollback(){
	mysqli_rollback($_SESSION['REVELSA']);
	bd_termina_transaccion();	
}

function bd_termina_transaccion(){
	mysqli_autocommit($_SESSION['REVELSA'],TRUE);	
}

function bd_posicionaCursor($resultset, $indice){
	mysqli_data_seek($resultset,$indice);	
}

/****
	FUNCIÓN QUE LIBERA EL RESULTSET PARA PODER EJECUTAR MÁS DE UN PROCEDIMIENTO ALMACENADO
**/
function libera_consulta($conexion)
{
	while(mysqli_more_results($conexion))
	{
		if(mysqli_next_result($conexion))
		{
			$resultado = mysqli_use_result($conexion);
			
			if($resultado)
			{
				mysqli_free_result($resultado);
			}
		}
	}
} 

//funcion que sustituye la de conexion maestro 
function bd_liberaMemoria($conexion)
{
	while(mysqli_more_results($conexion))
	{
		if(mysqli_next_result($conexion))
		{
			$resultado = mysqli_use_result($conexion);
			mysqli_free_result($resultado);
		}
	}
}

//Función para escapar los caracteres especiales de un parámetro.
function bd_escapaParametro($valor)
{
	if(is_array($valor))
	return array_map(__METHOD__, $valor);
	
	if(!empty($valor) && is_string($valor))
	{
	    return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $valor);
	}
	return $valor; 
}

function procesaArrayLineal($arrDato)
{    
	if (is_array($arrDato))
	{
		foreach($arrDato as $key => $value)
		{
			if(mb_detect_encoding($value, 'UTF-8', true)===false)
			{
				$arrDato[$key] = utf8_encode($value);
			}
			else
			{
				$arrDato[$key] = ($value);
			}
		}
	}
	
	return $arrDato;
}


?>
