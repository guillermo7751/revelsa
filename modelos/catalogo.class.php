<?php

ini_set("display_errors",1);
error_reporting(E_ALL);

class Catalogo
{
    private $error = false;
    private $errorMensaje = '';
    private $sql = '';
    
    public $idProducto;
    public $tipoCatalogo;
    public $nombreEquipo;
    public $descEquipo;
    public $montoVenta;
    public $idMarca;
    public $arrPreciosRenta = array();
    public $imagenEquipo;
    public $isImgVacio;
    public $isDefaultDesc = 0;
    
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
    FUNCION: dameCatalogoEquipos()
    OBJETIVO: DEVUELVE EL CATALOGO DE EQUIPOS Y ANDAMIOS
    PARAMETROS: OBJETO:PROPIEDAD
    ****************************************************************************************************/
    function dameCatalogo()
    {
        $arrResultado = array();
        
        $this->sql = "CALL sp_catalogo_dameCatalogo(".$this->tipoCatalogo.")";
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
    FUNCION: dameMarcas()
    OBJETIVO: DEVUELVE EL CATALOGO DE MARCAS
    PARAMETROS: OBJETO:PROPIEDAD
    ****************************************************************************************************/
    function dameMarcas()
    {
        $arrResultado = array();
        
        $this->sql = "CALL sp_catalogo_dameMarcas()";
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
    FUNCION: damePeriodosRenta()
    OBJETIVO: DEVUELVE EL CATALOGO DE PERIODOS DE RENTA DE EQUIPOS
    PARAMETROS: OBJETO:PROPIEDAD
    ****************************************************************************************************/
    function damePeriodosRenta()
    {
        $arrResultado = array();
        
        $this->sql = "CALL sp_catalogo_damePeriodosRenta()";
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
    FUNCION: guardaEquipo()
    OBJETIVO: GUARDA LA INFORMACIÓN INGRESADA DEL EQUIPO
    PARAMETROS: OBJETO:PROPIEDAD
    ****************************************************************************************************/
    function guardaEquipo()
    {
        
        $arrResultado = array();
        
        $imagen = $this->imagenEquipo;
        if($this->imagenEquipo == '' || !$this->imagenEquipo)
        {
            $imagen = null;
        }
        
        $this->sql = "CALL sp_catalogo_guardaEquipo(?,?,?,?,?,?,?,?,?,?)";
        $stmt = bd_preparaSQL($this->sql,array('1_s'=>$this->nombreEquipo,
                                         '2_s'=>$this->descEquipo,
                                         '3_i'=>$this->isDefaultDesc,
                                         '4_i'=>$this->tipoCatalogo,
                                         '5_i'=>$this->idMarca,
                                         '6_d'=>$this->montoVenta,
                                         '7_s'=>$imagen,
                                         '8_i'=>$this->isImgVacio,
                                         '9_i'=>$this->idProducto,
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
            
            //INACTIVAMOS LOS PRECIOS DE RENTA PREVIOS EN EL PROCESO DE MODIFICAR PRODUCTOS
            if($this->idProducto <> '')
            {
                $this->sql = "CALL sp_catalogo_inactivaPreciosRenta(?,?)";
                $stmt = bd_preparaSQL($this->sql,array('1_i'=>$this->idProducto,
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
                
            if(sizeof($this->arrPreciosRenta)>0)
            {
                foreach($this->arrPreciosRenta as $precioRentaEnCurso)
                {
                    $arrTempoPrecioRenta = explode('_',$precioRentaEnCurso);
                    
                    
                    $this->sql = "CALL sp_catalogo_guardaPrecioRentaEquipo(?,?,?,?)";
                    $stmt = bd_preparaSQL($this->sql,array('1_i'=>$fila['idProducto'],
                                                     '2_d'=>$arrTempoPrecioRenta[1],
                                                     '3_i'=>$arrTempoPrecioRenta[0],
                                                     '4_i'=>$_SESSION['idUsuario'])
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
        }
    }
    
    /****************************************************************************************************
    FUNCION: eliminaEquipo()
    OBJETIVO: ELIMINA DEL CATÁLOGO EL EQUIPO SELECCIONADO
    PARAMETROS: OBJETO:PROPIEDAD
    ****************************************************************************************************/
    function eliminaEquipo()
    {
        $arrResultado = array();
        
        $this->sql = "CALL sp_catalogo_eliminaEquipo(?,?)";
        $stmt = bd_preparaSQL($this->sql,array('1_i'=>$this->idProducto,
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
    FUNCION: dameInfoProducto()
    OBJETIVO: DEVUELVE LA INFORMACIÓN Y PRECIOS DE UN PRODUCTO 
    PARAMETROS: OBJETO:PROPIEDAD
    ****************************************************************************************************/
    function dameInfoProducto()
    {
        $arrResultado = array();
        
        $this->sql = "CALL sp_catalogo_dameInfoProducto(".$this->idProducto.")";
        $res = bd_consultaSQL($this->sql);
        
        if(bd_error())
        {
            $this->defineError(array('Error'=>true,'ErrorMensaje'=>bd_mensajeError()));
        }
        else
        {
            if(bd_cuentaRegistros($res)>0)
            {
                $arrResultado = bd_dameRegistro($res);
            }
            
            bd_liberaResultSet($res);
        }

        return $arrResultado;

    }
    
     /****************************************************************************************************
    FUNCION: dameTodosProductos()
    OBJETIVO: DEVUELVE UN LISTADO CON TODOS LOS PRODUCTOS ACTIVOS
    PARAMETROS: OBJETO:PROPIEDAD
    ****************************************************************************************************/
    function dameTodosProductos()
    {
        $arrResultado = array();
        
        $this->sql = "  SELECT idProducto,nombreProducto,precioVentaProd,group_concat(precioRentaProd ORDER BY idTiempo) as precioRentaProd,
                        isDefaultDesc,descProducto
                        FROM
                        (
                            SELECT 
                                p.id_producto AS idProducto,
                                nombre_producto nombreProducto,
                                ppv.precio_venta AS precioVentaProd,
                                concat_ws('|',pt.descripcion_producto_tiempo,ppr.precio_renta) as precioRentaProd,
                                ppr.id_producto_tiempo as idTiempo,
                                p.desc_default as isDefaultDesc,
                                p.descripcion_producto as descProducto
                            FROM
                                producto p
                                    INNER JOIN
                                producto_precio_venta ppv ON ppv.id_producto = p.id_producto
                                    AND ppv.estatus_precio_venta = 'ACTIVO'
                                    LEFT OUTER JOIN
                                producto_precio_renta ppr on ppr.id_producto = p.id_producto
                                    AND ppr.estatus_precio_renta = 'ACTIVO'
                                    LEFT OUTER JOIN
                                producto_tiempo pt on pt.id_producto_tiempo = ppr.id_producto_tiempo
                                    AND pt.estatus_producto_tiempo = 'ACTIVO'
                            WHERE
                                p.estatus_producto = 'ACTIVO'
                            ORDER BY nombreProducto
                        ) T1
                        GROUP BY idProducto";
                      
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
    FUNCION: dameCorreosContacto()
    OBJETIVO: DEVUELVE UN LISTADO CON LAS CUENTAS DE CORREO DISPONIBLES EN LA EMPRESA
    PARAMETROS: OBJETO:PROPIEDAD
    ****************************************************************************************************/
    function dameCorreosContacto()
    {
        $arrResultado = array();
        
        $this->sql = "SELECT id_correocontacto as id, correo_correocontacto as correo
                      FROM correos_contacto
                      WHERE estatus_correocontacto = 'ACTIVO'";
                      
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
