<?php

ini_set("display_errors",1);
error_reporting(E_ALL);

require_once('catalogoAndamios_view.php');
    
if(!isset($_POST['accion_ajax']))
{
    $paginaInicio = catalogoAndamios_inicio();
    
    $modulo.=$paginaInicio;
}
else
{
    require_once($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'revelsa'.DIRECTORY_SEPARATOR.'motor'.DIRECTORY_SEPARATOR.'funciones.php');
    require_once($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'revelsa'.DIRECTORY_SEPARATOR.'modelos'.DIRECTORY_SEPARATOR.'catalogo.class.php');
    require_once($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'revelsa'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'funcionesGlobales.php');
   
    conectarRevelsa();
    
    switch($_POST['accion_ajax'])
    {
        case 'ajax_catalogoAndamios_cargaCatalogo':
            ajax_catalogoAndamios_cargaCatalogo();
        break;
    
        case 'ajax_catalogoAndamios_cargaVistaEditar':
            ajax_catalogoAndamios_cargaVistaEditar($_POST['tipoAccion'],$_POST['id']);
        break;
    
        case 'ajax_catalogoAndamios_guardaEquipo':
            ajax_catalogoAndamios_guardaEquipo($_POST['nombre'],$_POST['montoVenta'],$_POST['desc'],$_POST['isDefaultDesc'],$_POST['strPreciosRenta'],$_POST['isImgVacio'],$_POST['id']);
        break;
    
        case 'ajax_catalogoAndamios_eliminaEquipo':
            ajax_catalogoAndamios_eliminaEquipo($_POST['id']);
        break;
    }

}

function ajax_catalogoAndamios_cargaCatalogo()
{
    $arrResultado = array('funcionRetorno_sistema' => $_POST['funcionRetorno_sistema'],
                          'modalEspera_sistema' => $_POST['modalEspera_sistema'],
                          'resultado'=>'',
                          'Error'=>false,
                          'ErrorMensaje'=>'');
    
    $oCatalogo = new Catalogo();
    $oCatalogo->tipoCatalogo = 2;
    
    $arrCatalogo = $oCatalogo->dameCatalogo();
    $arrError = $oCatalogo->dameError();
    
    if($arrError['Error'])
    {
        $arrResultado['Error'] = $arrError['Error'];
        $arrResultado['ErrorMensaje'] = $arrError['ErrorMensaje'];
    }
    else
    {
        $tablaCatalogo = catalogoAndamios_tablaCatalogo($arrCatalogo);
        
        $arrResultado['resultado'] = $tablaCatalogo;
        
    }
   

	echo json_encode($arrResultado);
}


function ajax_catalogoAndamios_cargaVistaEditar($tipoAccion,$id)
{
    $arrResultado = array('funcionRetorno_sistema' => $_POST['funcionRetorno_sistema'],
                          'modalEspera_sistema' => $_POST['modalEspera_sistema'],
                          'resultado'=>'',
                          'tipoAccion'=>$tipoAccion,
                          'id'=>$id,
                          'Error'=>false,
                          'ErrorMensaje'=>'');
    
    $oCatalogo = new Catalogo();
    
    $arrPeriodosRenta = $oCatalogo->damePeriodosRenta();
    $arrError = $oCatalogo->dameError();
    if($arrError['Error'])
    {
        $arrResultado['Error'] = $arrError['Error'];
        $arrResultado['ErrorMensaje'] = $arrError['ErrorMensaje'];
    }
    else
    {
        $arrInfoProducto = array();
        if($tipoAccion == 'ver' || $tipoAccion == 'editar')
        {
            $oCatalogo->idProducto = $id;
            $arrInfoProducto = $oCatalogo->dameInfoProducto();
            
            $arrError = $oCatalogo->dameError();
            if($arrError['Error'])
            {
                $arrResultado['Error'] = $arrError['Error'];
                $arrResultado['ErrorMensaje'] = $arrError['ErrorMensaje'];
            }               
        }

        $arrResultado['resultado'] = catalogoAndamios_vistaEditar($arrPeriodosRenta,$arrInfoProducto,$tipoAccion);
        
        
    }
    

	echo json_encode($arrResultado);
}


function ajax_catalogoAndamios_guardaEquipo($nombre,$montoVenta,$desc,$isDefaultDesc,$strPreciosRenta,$isImgVacio,$id)
{
    $arrResultado = array('funcionRetorno_sistema' => $_POST['funcionRetorno_sistema'],
                          'modalEspera_sistema' => $_POST['modalEspera_sistema'],
                          'resultado'=>'',
                          'Error'=>false,
                          'ErrorMensaje'=>'');
    
    //REDUCIMOS EL TAMAÑO DE LA IMG.
    $imgReducida = '';
    
    if(sizeof($_FILES) > 0)
    {
        $imgOriginal = file_get_contents($_FILES['imagen']['tmp_name']);
        $size = getimagesizefromstring($imgOriginal);
        
        $imgReducida = global_reducirImagen($imgOriginal,($size[0]/4),($size[1]/4),$_FILES['imagen']['type']);
        $imgReducida = 'data:'.$_FILES['imagen']['type'].';base64,'.$imgReducida;
    }
    
    $oCatalogo = new Catalogo();
    
    $oCatalogo->nombreEquipo = $nombre;
    $oCatalogo->idMarca = 12;
    $oCatalogo->montoVenta = $montoVenta;
    $oCatalogo->descEquipo = $desc;
    $oCatalogo->isDefaultDesc = $isDefaultDesc;
    $oCatalogo->tipoCatalogo = 2;
    $oCatalogo->imagenEquipo = $imgReducida;
    $oCatalogo->isImgVacio = $isImgVacio;
    $oCatalogo->idProducto = $id;
    
    if($strPreciosRenta<>'')
    {
        $arrPreciosRenta = explode(',',$strPreciosRenta);
        $oCatalogo->arrPreciosRenta = $arrPreciosRenta;
    }
    
    bd_inicia_transaccion();
    
    $oCatalogo->guardaEquipo();
    $arrError = $oCatalogo->dameError();
    
    if($arrError['Error'])
    {
        bd_rollback();
        
        $arrResultado['Error'] = $arrError['Error'];
        $arrResultado['ErrorMensaje'] = $arrError['ErrorMensaje'];
    }
    else
    {
        bd_commit();
    }

	echo json_encode($arrResultado);
}


function ajax_catalogoAndamios_eliminaEquipo($id)
{
    $arrResultado = array('funcionRetorno_sistema' => $_POST['funcionRetorno_sistema'],
                          'modalEspera_sistema' => $_POST['modalEspera_sistema'],
                          'resultado'=>'',
                          'Error'=>false,
                          'ErrorMensaje'=>'');
    
    $oCatalogo = new Catalogo();
    
    $oCatalogo->idProducto = $id;
    
    $oCatalogo->eliminaEquipo();
    $arrError = $oCatalogo->dameError();
    
    if($arrError['Error'])
    {   
        $arrResultado['Error'] = $arrError['Error'];
        $arrResultado['ErrorMensaje'] = $arrError['ErrorMensaje'];
    }

	echo json_encode($arrResultado);
}

?>