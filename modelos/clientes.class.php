<?php

ini_set("display_errors",1);
error_reporting(E_ALL);

class Clientes
{
    private $error = false;
    private $errorMensaje = '';
    private $sql = '';

    //CLIENTES
    public $idCliente;
    public $nombreCliente;
    public $razonSocial;
    public $rfcCliente;
    public $personaCliente;
    public $direccionCliente;
    public $dirFiscalCliente;
    public $coloniaCliente;
    public $correoCliente;
    
    //DOCUMENTOS
    public $nombreOriginal;
    public $rutaDoc;
    public $idDocumento;
    public $idCE;
    public $arrTelefonos;
    
    function __construct()
    {
        
    }
    
     /****************************************************************************************************
    FUNCION: dameError()
    OBJETIVO: REGRESA ID CARGO
    PARAMETROS: N/A
    ****************************************************************************************************/
    public function dameError()
    {
       return array("ErrorMensaje"=>$this->errorMensaje,"Error"=>$this->error);
    }
	
    /****************************************************************************************************
    FUNCION: defineError()
    OBJETIVO: DEFINE  ERROR EN LAS PROPIEDADESDE LA CLASE
    PARAMETROS: $a_error["error"],["errorMEnsaje"]
    ****************************************************************************************************/
    public function defineError($a_error)
    {
         $this->error=$a_error["Error"];
         $this->errorMensaje=$a_error["ErrorMensaje"];
    }
    
    /****************************************************************************************************
    FUNCION: dameSQL()
    OBJETIVO: RETORNA LA CADENA DE LA ULTIMA CONSULTA EJECUTADA
    PARAMETROS: OBJETO:PROPIEDAD
    ****************************************************************************************************/
    public function dameSQL(){
            return $this->sql;
    }

    /****************************************************************************************************
    FUNCION: dameClientes()
    OBJETIVO: DEVUELVE EL CATALOGO DE CLIENTES
    PARAMETROS: OBJETO:PROPIEDAD
    ****************************************************************************************************/
    function dameClientes()
    {
        $arrResultado = array();
            
        $this->sql = " SELECT 
                            c.id_cliente AS id,
                            nombre_cliente AS cliente,
                            razonsocial_cliente AS razonSocial,
                            persona_cliente AS tipo,
                            rfc_cliente AS rfc
                        FROM
                            clientes c
                        WHERE
                            c.estatus_cliente = 'ACTIVO'";
        $res = bd_consultaSQL($this->sql);
        
        if(bd_error())
        {
            $this->defineError(array('Error'=>true,'ErrorMensaje'=>bd_mensajeError()));
        }
        else
        {
            if(bd_cuentaRegistros($res)>0)
            {
                while($fila = bd_dameRegistro($res))
                {
                    $this->sql = "  SELECT 
                                        ((suma * 100) / total) AS porcPendientes
                                    FROM
                                        (SELECT 
                                            COUNT(*) AS total, SUM(isReq) AS suma
                                        FROM
                                            (SELECT 
                                            IF(ce.id_clienteexpediente IS NULL, 0, 1) AS isReq
                                        FROM
                                            clientes_expediente_doc ced
                                        LEFT OUTER JOIN clientes_expediente ce ON ce.id_documento = ced.id_documento
                                            AND ce.estatus_clienteexpediente = 'ACTIVO'
                                            AND ce.id_cliente = ".$fila['id']."
                                        WHERE
                                            ced.tipoPersona_documento = '".($fila['tipo'])."'
                                                AND ced.estatus_documento = 'ACTIVO'
                                        GROUP BY ced.id_documento) T1) T2;";

                    //echo($this->sql);
                    
                    $resRequisitos = bd_consultaSQL($this->sql);

                    if(bd_error())
                    {
                        $this->defineError(array('Error'=>true,'ErrorMensaje'=>bd_mensajeError()));
                        break;
                    }
                    else
                    {
                        $filaRequisitos = bd_dameRegistro($resRequisitos);
                        $fila['porcPendientes'] = $filaRequisitos['porcPendientes'];

                        array_push($arrResultado,$fila);

                        bd_liberaResultSet($resRequisitos);
                    }
                }

                //die();
            }
            
            bd_liberaResultSet($res);
        }


        return $arrResultado;

    }
    
    /****************************************************************************************************
    FUNCION: guardaCliente()
    OBJETIVO: GUARDA LA INFORMACIÓN INGRESADA DEL CLIENTE
    PARAMETROS: OBJETO:PROPIEDAD
    ****************************************************************************************************/
    function guardaCliente()
    {
        
        $arrResultado = array();
           
        $this->sql = "CALL sp_finanzas_guardaCliente(?,?,?,?,?,?,?,?,?,?)";
        $stmt = bd_preparaSQL($this->sql,array('1_s'=>validaValorNulo($this->nombreCliente),
                                         '2_s'=>validaValorNulo($this->razonSocial),
                                         '3_s'=>validaValorNulo($this->rfcCliente),
                                         '4_s'=>validaValorNulo($this->personaCliente),
                                         '5_s'=>validaValorNulo($this->direccionCliente),
                                         '6_s'=>validaValorNulo($this->coloniaCliente),
                                         '7_s'=>validaValorNulo($this->dirFiscalCliente),
                                         '8_s'=>validaValorNulo($this->correoCliente),
                                         '9_i'=>validaValorNulo($this->idCliente),
                                         '10_i'=>$_SESSION['idUsuario'])
                             );
        
        if($stmt['Error'])
        {
            $this->defineError(array('Error'=>true,'ErrorMensaje'=>$stmt['ErrorMensaje']));
        }
        else
        {
            $fila = bd_dameRegistro($stmt['res']);

            bd_stmt_liberaResultSet($stmt['stmt'], $stmt['res']);
        
            $this->sql = "CALL sp_finanzas_eliminaTelefonosCliente(?)";
            
            $stmt = bd_preparaSQL($this->sql,array('1_i'=>$fila['idCliente']));
            
            if($stmt['Error'])
            {
                $this->defineError(array('Error'=>true,'ErrorMensaje'=>$stmt['ErrorMensaje']));
            }
            else
            {
                bd_stmt_liberaResultSet($stmt['stmt'], $stmt['res']);

                // echo "<pre>";
                // print_r($this->arrTelefonos);
                // die();
                foreach($this->arrTelefonos as $telefonoEnCurso)
                {
                    $this->sql = "CALL sp_finanzas_guardaClienteTel(?,?,?,?)";

                    $stmt = bd_preparaSQL($this->sql,array('1_i'=>validaValorNulo($fila['idCliente']),
                                        '2_s'=>validaValorNulo($telefonoEnCurso['numero']),
                                        '3_i'=>validaValorNulo($telefonoEnCurso['tipo']),
                                        '4_i'=>validaValorNulo($_SESSION['idUsuario'])));

                    if($stmt['Error'])
                    {
                        $this->defineError(array('Error'=>true,'ErrorMensaje'=>$stmt['ErrorMensaje']));
                    }
                    else
                    {
                        bd_stmt_liberaResultSet($stmt['stmt'], $stmt['res']);
                    }
                }
            }
        }
    }
    
    /****************************************************************************************************
    FUNCION: dameInfoCliente()
    OBJETIVO: DEVUELVE LA INFORMACIÓN Y PRECIOS DE UN PRODUCTO 
    PARAMETROS: OBJETO:PROPIEDAD
    ****************************************************************************************************/
    function dameInfoCliente()
    {
        $arrResultado = array('infoCliente'=>array(),'telCliente'=>array());
        
        $this->sql = "CALL sp_finanzas_dameInfoCliente(".$this->idCliente.")";
        $res = bd_consultaSQL($this->sql);
        
        if(bd_error())
        {
            $this->defineError(array('Error'=>true,'ErrorMensaje'=>bd_mensajeError()));
        }
        else
        {
            if(bd_cuentaRegistros($res)>0)
            {
                $arrResultado['infoCliente'] = bd_dameRegistro($res);

                bd_liberaResultSet($res);

                //OBTENEMOS LOS TELEFONOS DEL CLIENTE
                $this->sql = "  SELECT 
                                    id_telefono, id_cliente, telefono_numero, id_telefonotipo
                                FROM
                                    clientes_telefonos
                                WHERE
                                    id_cliente = ".$this->idCliente."
                                        AND estatus_telefono = 'ACTIVO';";
                $res = bd_consultaSQL($this->sql);

                if(bd_error())
                {
                    $this->defineError(array('Error'=>true,'ErrorMensaje'=>bd_mensajeError()));
                }
                else
                {
                    while($fila = bd_dameRegistro($res))
                    {
                        array_push($arrResultado['telCliente'],$fila);
                    }

                    bd_liberaResultSet($res);
                }

            } 
            
        }

        return $arrResultado;

    }
    
    /****************************************************************************************************
    FUNCION: eliminaCliente()
    OBJETIVO: ELIMINA EL CLIENTE DEL CATALOGO
    PARAMETROS: OBJETO:PROPIEDAD
    ****************************************************************************************************/
    function eliminaCliente()
    {
        $arrResultado = array();
        
        $this->sql = "CALL sp_finanzas_eliminaCliente(?,?)";
        $stmt = bd_preparaSQL($this->sql,array('1_i'=>$this->idCliente,
                                         '2_i'=>$_SESSION['idUsuario'])
                             );
        
        if($stmt['Error'])
        {
            $this->defineError(array('Error'=>true,'ErrorMensaje'=>$stmt['ErrorMensaje']));
        }
        else
        {
            bd_stmt_liberaResultSet($stmt['stmt'], $stmt['res']);
        }
    }
    
    /****************************************************************************************************
    FUNCION: dameListadoDocumentos()
    OBJETIVO: REGRESA EL LISTADO DE DOCUMENTOS
    PARAMETROS: OBJETO:PROPIEDAD
    ****************************************************************************************************/
    function dameListadoDocumentos()
    {
        $arrResultado = array();
        
        $this->sql = "  SELECT 
                            ced.id_documento as idDoc, ced.descripcion_documento as descDoc
                        FROM
                            clientes_expediente_doc ced
                        WHERE
                            ced.estatus_documento = 'ACTIVO'
                                AND ced.tipoPersona_documento = '".($this->personaCliente)."'
                        ORDER BY idDoc;";
        
        $res = bd_consultaSQL($this->sql);
        
        if(bd_error())
        {
            $this->defineError(array('Error'=>true,'ErrorMensaje'=>bd_mensajeError()));
        }
        else
        {
            if(bd_cuentaRegistros($res)>0)
            {
                while($fila = bd_dameRegistro($res))
                {
                    $arrResultado[$fila['idDoc']] = $fila;
                }
            }
            
            bd_liberaResultSet($res);
        }

        return $arrResultado;
    }
    
    /****************************************************************************************************
    FUNCION: dameExpedienteClienteDocs()
    OBJETIVO: REGRESA EL EXPEDIENTE DE DOCUMENTOS DEL CLIENTE
    PARAMETROS: OBJETO:PROPIEDAD
    ****************************************************************************************************/
    function dameExpedienteClienteDocs()
    {
        $arrResultado = array();
        
        $this->sql = "  SELECT 
                            ce.ruta_clienteexpediente as ruta, ce.nombreoriginal_clienteexpediente as nombre,
                            ce.id_clienteexpediente as idCE
                        FROM
                            clientes_expediente ce
                        WHERE
                            ce.id_cliente = ".$this->idCliente."
                                AND ce.id_documento = ".$this->idDocumento."
                                AND ce.estatus_clienteexpediente = 'ACTIVO'";
        
        $res = bd_consultaSQL($this->sql);
        
        if(bd_error())
        {
            $this->defineError(array('Error'=>true,'ErrorMensaje'=>bd_mensajeError()));
        }
        else
        {
            if(bd_cuentaRegistros($res)>0)
            {
                while($fila = bd_dameRegistro($res))
                {
                    array_push($arrResultado,$fila);
                }
            }
            
            bd_liberaResultSet($res);
        }

        return $arrResultado;
    }
    
    /****************************************************************************************************
    FUNCION: guardaDocumento()
    OBJETIVO: GUARDA EL DOCUMENTO ASOCIADO AL CLIENTE
    PARAMETROS: OBJETO:PROPIEDAD
    ****************************************************************************************************/
    function guardaDocumento()
    {
        
        $arrResultado = array();
        
        //$this->eliminaDocumento();
        
        if(!$this->error)
        {
            
            $this->sql = "CALL sp_finanzas_guardaDocumentoCliente(?,?,?,?,?)";
            $stmt = bd_preparaSQL($this->sql,array('1_i'=>validaValorNulo($this->idDocumento),
                                             '2_i'=>validaValorNulo($this->idCliente),
                                             '3_s'=>validaValorNulo($this->rutaDoc),
                                             '4_s'=>validaValorNulo($this->nombreOriginal),
                                             '5_i'=>$_SESSION['idUsuario'])
                                 );
            
            if($stmt['Error'])
            {
                $this->defineError(array('Error'=>true,'ErrorMensaje'=>$stmt['ErrorMensaje']));
            }
            else
            {
                bd_stmt_liberaResultSet($stmt['stmt'], $stmt['res']);
            }
        }
    }
    
    /****************************************************************************************************
    FUNCION: eliminaDocumento()
    OBJETIVO: ELIMINA EL DOCUMENTO SELECCIONADO
    PARAMETROS: OBJETO:PROPIEDAD
    ****************************************************************************************************/
    function eliminaDocumento()
    {
        $arrResultado = array();
        
        $this->sql = "  SELECT 
                            c.id_cliente as idCliente, c.persona_cliente as personaCliente
                        FROM
                            clientes_expediente ce
                        INNER JOIN clientes c ON ce.id_cliente = c.id_cliente
                            AND ce.id_clienteexpediente = ".$this->idCE;

        $res = bd_consultaSQL($this->sql);

        if(bd_error())
        {
            $this->defineError(array('Error'=>true,'ErrorMensaje'=>bd_mensajeError()));
        }
        else
        {
            $fila = bd_dameRegistro($res);

            bd_liberaResultSet($res);

            $arrResultado['idCliente'] = $fila['idCliente'];
            $arrResultado['personaCliente'] = $fila['personaCliente'];

            $this->sql = "CALL sp_finanzas_eliminaDocumentoCliente(?,?)";
            $stmt1 = bd_preparaSQL($this->sql,array('1_i'=>$this->idCE,
                                            '2_i'=>$_SESSION['idUsuario'])
                                );
            
            if($stmt1['Error'])
            {
                $this->defineError(array('Error'=>true,'ErrorMensaje'=>$stmt1['ErrorMensaje']));
            }
            else
            {
                bd_stmt_liberaResultSet($stmt1['stmt'], $stmt1['res']);
            }
        }

        return $arrResultado;
    }
    
    /****************************************************************************************************
    FUNCION: dameTiposTelefono()
    OBJETIVO: DEVUELVE LOS TIPOS DE TELEFONO
    PARAMETROS: OBJETO:PROPIEDAD
    ****************************************************************************************************/
    function dameTiposTelefono()
    {
        $arrResultado = array();
        
        $this->sql = "SELECT id_telefonotipo as id, nombre_telefonotipo as tipo FROM telefono_tipo WHERE estatus_telefonotipo = 'ACTIVO';";
        $res = bd_consultaSQL($this->sql);
        
        if(bd_error())
        {
            $this->defineError(array('Error'=>true,'ErrorMensaje'=>bd_mensajeError()));
        }
        else
        {
            if(bd_cuentaRegistros($res)>0)
            {
                while($fila = bd_dameRegistro($res))
                {
                    array_push($arrResultado,$fila);
                }
                
            }
            
            bd_liberaResultSet($res);
        }

        return $arrResultado;

    }
}
?>
