<?php
class Catalogo
{
    private $error = false;
    private $errorMensaje = '';
    private $sql = '';
    
    public $tipoCatalogo;
    
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
        
        $sql = "CALL sp_catalogo_dameCatalogo(?)";
        $fila = bd_preparaSQL($sql,array($this->tipoCatalogo=>'i'));
        
        if($fila['Error'])
        {
            $this->defineError(array('Error'=>$fila['Error'],'ErrorMensaje'=>$fila['ErrorMensaje']));
        }
        else
        {            
            $arrResultado = $fila['resultado'];
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
        
        $sql = "CALL sp_catalogo_dameMarcas()";
        $fila = bd_preparaSQL($sql,array());
        
        if($fila['Error'])
        {
            $this->defineError(array('Error'=>$fila['Error'],'ErrorMensaje'=>$fila['ErrorMensaje']));
        }
        else
        {
            $arrResultado = $fila['resultado'];
        }

        return $arrResultado;

    }
    
}
?>
