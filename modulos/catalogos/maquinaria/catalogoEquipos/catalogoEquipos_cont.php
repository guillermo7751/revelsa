<?php

ini_set("display_errors",1);
error_reporting(E_ALL);

require_once('catalogoEquipos_view.php');
    
if(!isset($_POST['accion_ajax']))
{
    $paginaInicio = catalogoEquipos_inicio();
    
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
        case 'ajax_catalogoEquipos_cargaCatalogo':
            ajax_catalogoEquipos_cargaCatalogo();
        break;
    
        case 'ajax_catalogoEquipos_cargaVistaEditar':
            ajax_catalogoEquipos_cargaVistaEditar($_POST['tipoAccion'],$_POST['id']);
        break;
    
        case 'ajax_catalogoEquipos_guardaEquipo':
            ajax_catalogoEquipos_guardaEquipo($_POST['nombre'],$_POST['idMarca'],$_POST['montoVenta'],$_POST['desc'],$_POST['strPreciosRenta'],$_POST['isImgVacio'],$_POST['id']);
        break;
    
        case 'ajax_catalogoEquipos_eliminaEquipo':
            ajax_catalogoEquipos_eliminaEquipo($_POST['id']);
        break;
    }

}

function ajax_catalogoEquipos_cargaCatalogo()
{
    $arrResultado = array('funcionRetorno_sistema' => $_POST['funcionRetorno_sistema'],
                          'modalEspera_sistema' => $_POST['modalEspera_sistema'],
                          'resultado'=>'',
                          'Error'=>false,
                          'ErrorMensaje'=>'');
    
    $oCatalogo = new Catalogo();
    $oCatalogo->tipoCatalogo = 1;
    
    $arrCatalogo = $oCatalogo->dameCatalogo();
    $arrError = $oCatalogo->dameError();
    
    if($arrError['Error'])
    {
        $arrResultado['Error'] = $arrError['Error'];
        $arrResultado['ErrorMensaje'] = $arrError['ErrorMensaje'];
    }
    else
    {
        $tablaCatalogo = catalogoEquipos_tablaCatalogo($arrCatalogo);
        
        $arrResultado['resultado'] = $tablaCatalogo;
        
    }
   

	echo json_encode($arrResultado);
}


function ajax_catalogoEquipos_cargaVistaEditar($tipoAccion,$id)
{
    $arrResultado = array('funcionRetorno_sistema' => $_POST['funcionRetorno_sistema'],
                          'modalEspera_sistema' => $_POST['modalEspera_sistema'],
                          'resultado'=>'',
                          'tipoAccion'=>$tipoAccion,
                          'id'=>$id,
                          'Error'=>false,
                          'ErrorMensaje'=>'');
    
    $oCatalogo = new Catalogo();
    
    $arrMarcas = $oCatalogo->dameMarcas();
    $arrError = $oCatalogo->dameError();
    if($arrError['Error'])
    {
        $arrResultado['Error'] = $arrError['Error'];
        $arrResultado['ErrorMensaje'] = $arrError['ErrorMensaje'];
    }
    else
    {
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

            $arrResultado['resultado'] = catalogoEquipos_vistaEditar($arrMarcas,$arrPeriodosRenta,$arrInfoProducto,$tipoAccion);
            
            
        }
    }

	echo json_encode($arrResultado);
}


function ajax_catalogoEquipos_guardaEquipo($nombre,$idMarca,$montoVenta,$desc,$strPreciosRenta,$isImgVacio,$id)
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
    $oCatalogo->idMarca = $idMarca;
    $oCatalogo->montoVenta = $montoVenta;
    $oCatalogo->descEquipo = $desc;
    $oCatalogo->tipoCatalogo = 1;
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


function ajax_catalogoEquipos_eliminaEquipo($id)
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