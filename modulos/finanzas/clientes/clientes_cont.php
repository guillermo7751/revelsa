<?php

ini_set("display_errors",1);
error_reporting(E_ALL);

require_once('clientes_view.php');
    
if(!isset($_POST['accion_ajax']))
{
    $paginaInicio = clientes_inicio();
    
    $modulo.=$paginaInicio;
}
else
{
    require_once($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'revelsa'.DIRECTORY_SEPARATOR.'motor'.DIRECTORY_SEPARATOR.'funciones.php');
    require_once($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'revelsa'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'funcionesGlobales.php');
    require_once($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'revelsa'.DIRECTORY_SEPARATOR.'modelos'.DIRECTORY_SEPARATOR.'clientes.class.php');
    
    conectarRevelsa();
    
    switch($_POST['accion_ajax'])
    {
        case 'ajax_clientes_cargaClientes':
            ajax_clientes_cargaClientes();
        break;
    
        case 'ajax_clientes_cargaVistaEditar':
            ajax_clientes_cargaVistaEditar($_POST['tipoAccion'],$_POST['id']);
        break;
    
        case 'ajax_clientes_guardaCliente':
            ajax_clientes_guardaCliente($_POST['nombre'],$_POST['direccion'],$_POST['colonia'],$_POST['correo'],
                                        $_POST['dirFiscal'],$_POST['razonSocial'],$_POST['rfc'],$_POST['persona'],
                                        $_POST['id'],$_POST['telefonos']);
        break;
    
        case 'ajax_clientes_eliminaCliente':
            ajax_clientes_eliminaCliente($_POST['id']);
        break;
    
        case 'ajax_clientes_cargaVistaDocumentos':
            ajax_clientes_cargaVistaDocumentos($_POST['id'],$_POST['persona']);
        break;
    
        case 'ajax_clientes_guardaAdjuntos':
            ajax_clientes_guardaAdjuntos($_POST['id'],$_POST['arrEliminaAdj']);
        break;

        case 'ajax_clientes_eliminarDocumento':
            ajax_clientes_eliminarDocumento($_POST['idCE']);
        break;

        case 'ajax_clientes_agregaTelefono':
            ajax_clientes_agregaTelefono();
        break;
    
        default:
        break;
    }

}

function ajax_clientes_cargaClientes()
{
    $arrResultado = array('funcionRetorno_sistema' => $_POST['funcionRetorno_sistema'],
                          'modalEspera_sistema' => $_POST['modalEspera_sistema'],
                          'resultado'=>'',
                          'Error'=>false,
                          'ErrorMensaje'=>'');
    
    $oClientes = new Clientes();
    
    $arrClientes = $oClientes->dameClientes();
    $arrError = $oClientes->dameError();
    
    if($arrError['Error'])
    {
        $arrResultado['Error'] = $arrError['Error'];
        $arrResultado['ErrorMensaje'] = $arrError['ErrorMensaje'];
    }
    else
    {
        $tablaClientes = clientes_tablaClientes($arrClientes);
        
        $arrResultado['resultado'] = $tablaClientes;
        
    }
   

	echo json_encode($arrResultado);
}

function ajax_clientes_cargaVistaEditar($tipoAccion,$id)
{
    $arrResultado = array('funcionRetorno_sistema' => $_POST['funcionRetorno_sistema'],
                          'modalEspera_sistema' => $_POST['modalEspera_sistema'],
                          'resultado'=>'',
                          'tipoAccion'=>$tipoAccion,
                          'id'=>$id,
                          'Error'=>false,
                          'ErrorMensaje'=>'');
    
    $oClientes = new Clientes();
    
    $arrInfoCliente = array('infoCliente'=>array(),'telCliente'=>array());
    if($tipoAccion == 'ver' || $tipoAccion == 'editar')
    {
        $oClientes->idCliente = $id;
        $arrInfoCliente = $oClientes->dameInfoCliente();
        
        $arrError = $oClientes->dameError();
        if($arrError['Error'])
        {
            $arrResultado['Error'] = $arrError['Error'];
            $arrResultado['ErrorMensaje'] = $arrError['ErrorMensaje'];
        }   
    }

    $arrTiposTelefono = array();

    if(!$arrResultado['Error'])
    {
        $arrTiposTelefono = $oClientes->dameTiposTelefono();
        $arrError = $oClientes->dameError();
        
        if($arrError['Error'])
        {
            $arrResultado['Error'] = $arrError['Error'];
            $arrResultado['ErrorMensaje'] = $arrError['ErrorMensaje'];
        }
        else
        {
            $arrResultado['resultado'] = clientes_vistaEditar($arrInfoCliente['infoCliente'],$arrInfoCliente['telCliente'],$arrTiposTelefono,$tipoAccion);
        }

    }
        
	echo json_encode($arrResultado);
}

function ajax_clientes_guardaCliente($nombre,$direccion,$colonia,$correo,$dirFiscal,$razonSocial,$rfc,$persona,$id,$telefonos)
{
    $arrResultado = array('funcionRetorno_sistema' => $_POST['funcionRetorno_sistema'],
                          'modalEspera_sistema' => $_POST['modalEspera_sistema'],
                          'resultado'=>'',
                          'Error'=>false,
                          'ErrorMensaje'=>'');
    
    $oClientes = new Clientes();
    
    $oClientes->nombreCliente = ($nombre);
    $oClientes->razonSocial = ($razonSocial);
    $oClientes->rfcCliente = $rfc;
    $oClientes->personaCliente = ($persona);
    $oClientes->direccionCliente = ($direccion);
    $oClientes->coloniaCliente = ($colonia);
    $oClientes->dirFiscalCliente = ($dirFiscal);
    $oClientes->correoCliente = $correo;
    $oClientes->idCliente = $id;
    
    $arrTelefonos = json_decode($telefonos,true);

    $oClientes->arrTelefonos = $arrTelefonos;
    
    bd_inicia_transaccion();

    $oClientes->guardaCliente();
    
    $arrError = $oClientes->dameError();
    
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


function ajax_clientes_eliminaCliente($id)
{
    $arrResultado = array('funcionRetorno_sistema' => $_POST['funcionRetorno_sistema'],
                          'modalEspera_sistema' => $_POST['modalEspera_sistema'],
                          'resultado'=>'',
                          'Error'=>false,
                          'ErrorMensaje'=>'');
    
    $oClientes = new Clientes();
    
    $oClientes->idCliente = $id;
    
    $oClientes->eliminaCliente();
    $arrError = $oClientes->dameError();
    
    if($arrError['Error'])
    {   
        $arrResultado['Error'] = $arrError['Error'];
        $arrResultado['ErrorMensaje'] = $arrError['ErrorMensaje'];
    }

	echo json_encode($arrResultado);
}

function ajax_clientes_cargaVistaDocumentos($id,$persona)
{
    $arrResultado = array('funcionRetorno_sistema' => $_POST['funcionRetorno_sistema'],
                          'modalEspera_sistema' => $_POST['modalEspera_sistema'],
                          'resultado'=>'',
                          'Error'=>false,
                          'ErrorMensaje'=>'');
    
    $oClientes = new Clientes();
    
    $oClientes->idCliente = $id;
    $oClientes->personaCliente = utf8_decode($persona);
    
    $arrListDocumentos = $oClientes->dameListadoDocumentos();
    
    $arrError = $oClientes->dameError();
    
    if($arrError['Error'])
    {   
        $arrResultado['Error'] = $arrError['Error'];
        $arrResultado['ErrorMensaje'] = $arrError['ErrorMensaje'];
    }
    else
    {
        foreach($arrListDocumentos as $docEnCurso)
        {
            $oClientes->idDocumento = $docEnCurso['idDoc'];
            $arrDocCliente = $oClientes->dameExpedienteClienteDocs();

            $arrListDocumentos[$docEnCurso['idDoc']]['docsCliente'] = $arrDocCliente;
        }

        $arrResultado['resultado'] = clientes_vistaEstatus($arrListDocumentos,$id);
    }

	echo json_encode($arrResultado);
}

function ajax_clientes_guardaAdjuntos($id,$strEliminaAdj)
{
    $arrResultado = array('funcionRetorno_sistema' => $_POST['funcionRetorno_sistema'],
                          'modalEspera_sistema' => $_POST['modalEspera_sistema'],
                          'resultado'=>'',
                          'Error'=>false,
                          'ErrorMensaje'=>'');
    
    //PRIMERO ELIMINAMOS LOS ADJUNTOS DEFINIDOS POR EL USUARIO
    $oClientes = new Clientes();
    
    /*
    $arrEliminaAdj = explode(',',$strEliminaAdj);
    foreach($arrEliminaAdj as $adjuntoEliminadoEnCurso)
    {
        $oClientes->idCliente = $id;
        $oClientes->idDocumento = $adjuntoEliminadoEnCurso;
        
        $oClientes->eliminaDocumento();
        
        $arrError = $oClientes->dameError();
    
        if($arrError['Error'])
        {   
            $arrResultado['Error'] = $arrError['Error'];
            $arrResultado['ErrorMensaje'] = $arrError['ErrorMensaje'];
            
            break;
        }
    }
    */
    //if(!$arrResultado['Error'])
    //{
    $directorioCliente = "../../../archivos/clientes/".$id;
    $directorioClienteBD = "archivos/clientes/".$id;
    
    if(!file_exists($directorioCliente))
    {
        mkdir($directorioCliente, 0777);
    }
    
    //echo "<pre>";
    //print_r($_FILES);
    //die();
    
    $oClientes = new Clientes();
    
    foreach($_FILES as $nombre => $fileEnCurso)
    {
        $arrKeyArchivo = explode('_',$nombre);//PARA OBTENER EL ID DOCUMENTO
        
        $tmp_name = $fileEnCurso['tmp_name'];
        
        $name = basename($fileEnCurso["name"]);
        $arrName = explode('.',$name);//Obtenemos la extensión del nombre original
            
        $nombreFinal = basename($nombre).'.'.$arrName[1];
        $rutaFinal = $directorioCliente.'/'.$nombreFinal;
        
        move_uploaded_file($tmp_name,$rutaFinal);
        
        $oClientes->idCliente = $id;
        $oClientes->rutaDoc = $directorioClienteBD.'/'.$nombreFinal;
        $oClientes->nombreOriginal = $name;
        $oClientes->idDocumento = $arrKeyArchivo[2];
        
        $oClientes->guardaDocumento();
        
        $arrError = $oClientes->dameError();
    
        if($arrError['Error'])
        {   
            $arrResultado['Error'] = $arrError['Error'];
            $arrResultado['ErrorMensaje'] = $arrError['ErrorMensaje'];
            
            break;
        }
        //}
    }
    
    echo json_encode($arrResultado);
}

function ajax_clientes_eliminarDocumento($idCE)
{
    $arrResultado = array('funcionRetorno_sistema' => $_POST['funcionRetorno_sistema'],
                          'modalEspera_sistema' => $_POST['modalEspera_sistema'],
                          'resultado'=>'',
                          'Error'=>false,
                          'ErrorMensaje'=>'');
    
    $oClientes = new Clientes();
    $oClientes->idCE = $idCE;

    $arrCliente = $oClientes->eliminaDocumento();
    
    $arrError = $oClientes->dameError();
    
    if($arrError['Error'])
    {   
        $arrResultado['Error'] = $arrError['Error'];
        $arrResultado['ErrorMensaje'] = $arrError['ErrorMensaje'];
    }
    else
    {
        $arrResultado['IdCliente'] = $arrCliente['idCliente'];
        $arrResultado['PersonaCliente'] = $arrCliente['personaCliente']; 
    }

	echo json_encode($arrResultado);
}

function ajax_clientes_agregaTelefono()
{
    
    $arrResultado = array('funcionRetorno_sistema' => $_POST['funcionRetorno_sistema'],
                          'modalEspera_sistema' => $_POST['modalEspera_sistema'],
                          'resultado'=>'',
                          'arrProductos'=>array(),
                          'Error'=>false,
                          'ErrorMensaje'=>'');

    $oClientes = new Clientes();
    
    $arrTiposTelefono = $oClientes->dameTiposTelefono();
    $arrError = $oClientes->dameError();
    
    if($arrError['Error'])
    {
        $arrResultado['Error'] = $arrError['Error'];
        $arrResultado['ErrorMensaje'] = $arrError['ErrorMensaje'];
    }
    else
    {
        $filaTelefono = cliente_filaTelefono($arrTiposTelefono,'editar');

        $arrResultado['resultado'] = $filaTelefono;
    }

	echo json_encode($arrResultado);
}

?>