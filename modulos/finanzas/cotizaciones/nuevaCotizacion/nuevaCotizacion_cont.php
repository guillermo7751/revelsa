<?php

ini_set("display_errors",1);
error_reporting(E_ALL);

require_once('nuevaCotizacion_view.php');
    
if(!isset($_POST['accion_ajax']))
{
    $paginaInicio = nuevaCotizacion_inicio();
    
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
        case 'ajax_nuevaCotizacion_cargaForm':
            ajax_nuevaCotizacion_cargaForm($_POST['idEnCurso']);
        break;
    
        case 'ajax_nuevaCotizacion_agregaProducto':
            ajax_nuevaCotizacion_agregaProducto($_POST['idEnCurso']);
        break;
    
        case 'ajax_nuevaCotizacion_cargaFormEnvio':
            ajax_nuevaCotizacion_cargaFormEnvio();
        break;
    
        case 'ajax_nuevaCotizacion_accionCotizacion':
            ajax_nuevaCotizacion_accionCotizacion($_POST['cliente'],$_POST['direccion'],$_POST['infoTabla'],$_POST['tipoCotizacion'],
                                                    $_POST['subtotal'],$_POST['iva'],$_POST['retIVA'],$_POST['total'],$_POST['tipoCliente'],
                                                    $_POST['observaciones'],$_POST['vigencia'],$_POST['idUsuario'],$_POST['cuenta'],$_POST['correo'],
                                                    $_POST['asunto'],$_POST['mensaje'],$_POST['accion']);
        break;
    
        default:
        break;
    }

}

function ajax_nuevaCotizacion_cargaForm($idAsignar)
{
    $arrResultado = array('funcionRetorno_sistema' => $_POST['funcionRetorno_sistema'],
                          'modalEspera_sistema' => $_POST['modalEspera_sistema'],
                          'resultado'=>'',
                          'arrProductos'=>array(),
                          'Error'=>false,
                          'ErrorMensaje'=>'');
    
    $vistaForm = nuevaCotizacion_vistaForm($idAsignar);
        
    $arrResultado['resultado'] = $vistaForm;
    
    $oCatalogo = new Catalogo();
    
    $arrTemp = $oCatalogo->dameTodosProductos();
    $arrError = $oCatalogo->dameError();
    
    if($arrError['Error'])
    {
        $arrResultado['Error'] = $arrError['Error'];
        $arrResultado['ErrorMensaje'] = $arrError['ErrorMensaje'];
    }
    else
    {
        $arrProductos = array();
        
        foreach($arrTemp as $filaEnCurso)
        {
            $arrEnCurso = array('value'=>$filaEnCurso['nombreProducto'],'data'=>$filaEnCurso);
            
            array_push($arrProductos,$arrEnCurso);
        }
        
        $arrResultado['arrProductos'] = $arrProductos;
    }
    
	echo json_encode($arrResultado);
}


function ajax_nuevaCotizacion_agregaProducto($idAsignar)
{
    $arrResultado = array('funcionRetorno_sistema' => $_POST['funcionRetorno_sistema'],
                          'modalEspera_sistema' => $_POST['modalEspera_sistema'],
                          'resultado'=>'',
                          'arrProductos'=>array(),
                          'Error'=>false,
                          'ErrorMensaje'=>'');
    
    $filaProducto = nuevaCotizacion_filaProducto($idAsignar);
        
    $arrResultado['resultado'] = $filaProducto;
    
    $oCatalogo = new Catalogo();
    
    $arrTemp = $oCatalogo->dameTodosProductos();
    $arrError = $oCatalogo->dameError();
    
    if($arrError['Error'])
    {
        $arrResultado['Error'] = $arrError['Error'];
        $arrResultado['ErrorMensaje'] = $arrError['ErrorMensaje'];
    }
    else
    {
        $arrProductos = array();
        
        foreach($arrTemp as $filaEnCurso)
        {
            $arrEnCurso = array('value'=>$filaEnCurso['nombreProducto'],'data'=>$filaEnCurso);
            
            array_push($arrProductos,$arrEnCurso);
        }
        
        $arrResultado['arrProductos'] = $arrProductos;
    }
    
	echo json_encode($arrResultado);
}

function ajax_nuevaCotizacion_cargaFormEnvio()
{
    $arrResultado = array('funcionRetorno_sistema' => $_POST['funcionRetorno_sistema'],
                          'modalEspera_sistema' => $_POST['modalEspera_sistema'],
                          'resultado'=>'',
                          'Error'=>false,
                          'ErrorMensaje'=>'');
     
    $oCatalogo = new Catalogo();
     
    $arrCorreosContacto = $oCatalogo->dameCorreosContacto();
    $arrError = $oCatalogo->dameError();
    if($arrError['Error'])
    {
        $arrResultado['Error'] = $arrError['Error'];
        $arrResultado['ErrorMensaje'] = $arrError['ErrorMensaje'];
    }
    else
    {
        $vistaFormEnviar = nuevaCotizacion_vistaFormEnviar($arrCorreosContacto);
        $arrResultado['resultado'] = $vistaFormEnviar;
    }
     
    echo json_encode($arrResultado);
}

function ajax_nuevaCotizacion_accionCotizacion($cliente,$direccion,$infoTabla,$tipoCotizacion,$subtotal,$iva,
                                               $retIVA,$total,$tipoCliente,$observaciones,$vigencia,$idUsuario,
                                               $cuenta,$correo,$asunto,$mensaje,$accion
                                               )
{
    $arrResultado = array('funcionRetorno_sistema' => $_POST['funcionRetorno_sistema'],
                          'modalEspera_sistema' => $_POST['modalEspera_sistema'],
                          'resultado'=>'',
                          'Error'=>false,
                          'ErrorMensaje'=>'',
                          'PDF'=>'',
                          'Accion'=>$accion);
     
    $oCotizacion = new Cotizaciones();
    
    $oCotizacion->accionPDF = $accion;
    $oCotizacion->generaPDFCotizacion($cliente,$direccion,$infoTabla,$tipoCotizacion,$subtotal,$iva,
                                      $retIVA,$total,$tipoCliente,$observaciones,$vigencia,$idUsuario,
                                      $cuenta,$correo,$asunto,$mensaje);
    
    $arrError = $oCotizacion->dameError();
    
    if($arrError['Error'])
    {
        $arrResultado['Error'] = true;
        $arrResultado['ErrorMensaje'] = $arrError['ErrorMensaje'];  
    }
    else
    {
        $arrResultado['PDF'] = $oCotizacion->nombrePDF;
    }
    
    echo json_encode($arrResultado);
}

?>