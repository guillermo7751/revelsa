<?php

ini_set("display_errors",1);
error_reporting(E_ALL);

require_once('historicoCotizacion_view.php');
    
if(!isset($_POST['accion_ajax']))
{
    $paginaInicio = historicoCotizacion_inicio();
    
    $modulo.=$paginaInicio;
}
else
{
    require_once($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'revelsa'.DIRECTORY_SEPARATOR.'motor'.DIRECTORY_SEPARATOR.'funciones.php');
    require_once($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'revelsa'.DIRECTORY_SEPARATOR.'modelos'.DIRECTORY_SEPARATOR.'catalogo.class.php');
    require_once($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'revelsa'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'funcionesGlobales.php');
    require_once($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'revelsa'.DIRECTORY_SEPARATOR.'modelos'.DIRECTORY_SEPARATOR.'cotizaciones.class.php');
    
    conectarRevelsa();
    
    switch($_POST['accion_ajax'])
    {
        case 'ajax_historicoCotizacion_dameCotizaciones':
            ajax_historicoCotizacion_dameCotizaciones();
        break;
    
        default:
        break;
    }

}


function ajax_historicoCotizacion_dameCotizaciones()
{
    $arrResultado = array('funcionRetorno_sistema' => $_POST['funcionRetorno_sistema'],
                          'modalEspera_sistema' => $_POST['modalEspera_sistema'],
                          'resultado'=>'',
                          'Error'=>false,
                          'ErrorMensaje'=>'');
    
    $oCotizaciones = new Cotizaciones();
    
    $arrCotizaciones = $oCotizaciones->dameCotizaciones();
    $arrError = $oCotizaciones->dameError();
    
    if($arrError['Error'])
    {
        $arrResultado['Error'] = $arrError['Error'];
        $arrResultado['ErrorMensaje'] = $arrError['ErrorMensaje'];
    }
    else
    {
        $tablaCotizaciones = historicoCotizacion_tablaCotizaciones($arrCotizaciones);
        
        $arrResultado['resultado'] = $tablaCotizaciones;
        
    }
   

	echo json_encode($arrResultado);
}

?>