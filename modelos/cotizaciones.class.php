<?php

ini_set("display_errors",1);
error_reporting(E_ERROR);

class Cotizaciones
{
    private $error = false;
    private $errorMensaje = '';
    private $sql = '';
    private $servidorGral;
    
    public $accionPDF = '';
    public $numSigCotizacion = 0;
    public $nombrePDF;
    
    function __construct()
    {
        $this->servidorGral = $_SERVER['DOCUMENT_ROOT']."\\revelsa\\";
        
        require_once($this->servidorGral."lib".DIRECTORY_SEPARATOR."tcpdf".DIRECTORY_SEPARATOR."tcpdf.php");
        require_once($this->servidorGral."lib".DIRECTORY_SEPARATOR."PHPMailer".DIRECTORY_SEPARATOR."src".DIRECTORY_SEPARATOR."PHPMailer.php");
        require_once($this->servidorGral."lib".DIRECTORY_SEPARATOR."PHPMailer".DIRECTORY_SEPARATOR."src".DIRECTORY_SEPARATOR."SMTP.php");
        require_once($this->servidorGral."lib".DIRECTORY_SEPARATOR."PHPMailer".DIRECTORY_SEPARATOR."src".DIRECTORY_SEPARATOR."Exception.php");

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
    FUNCION: generaPDFCotizacion()
    OBJETIVO: EJECUTA LA ACCIÓN RECIBIDA PARA LA CREACIÓN DEL REPORTE PDF CON LA COTIZACIÓN
    PARAMETROS: OBJETO:PROPIEDAD
    ****************************************************************************************************/
    public function generaPDFCotizacion($cliente,$direccion,$infoTabla,$tipoCotizacion,$subtotal,$iva,$retIVA,$total,$tipoCliente,
                                        $observaciones,$vigencia,$idUsuario,$cuenta='',$correo='',$asunto='',$mensaje='')
    {
        $nombrepdf = time().'.pdf';
        header('Content-Disposition: inline; filename="'.$nombrepdf.'"');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: public'); 
    
        ini_set("memory_limit","150M");
        
        $sqlSigNumCotizacion = "SELECT fn_cotizacion_dameSigNumCotizacion() as sigCotizacion"; //BUSCAMOS EL NUMERO CORRESPONDIENTE A LA SIGUIENTE COTIZACION
        $resSigNumCotizacion = bd_consultaSQL($sqlSigNumCotizacion);
        
        if(bd_error())
        {
            die("Error(0): ".bd_mensajeError());
        }
        else
        {
            $filaSigNumCotizacion = bd_dameRegistro($resSigNumCotizacion);
            $this->numSigCotizacion = $filaSigNumCotizacion['sigCotizacion'];
        }
        
    
        $pdf = new TCPDF('P','mm', 'LETTER', true, 'UTF-8', false);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);	
        $pdf->SetAutoPageBreak(FALSE);
        $pdf->SetAuthor('SIE :: DELASALLE');
        $pdf->SetLineWidth(.2);
        
        $pdf->AddPage();
        
        
        //ENCABEZADO********************************************************************************************************************
        $pdf->Image($this->servidorGral.'img'.DIRECTORY_SEPARATOR.'rpt'.DIRECTORY_SEPARATOR.'logoRpt.png',10,6,30,30);
        
        $inicioX = 120;
        $inicioY = 10;
        
        $posY = $inicioY;
        
        $pdf->setXY($inicioX,$posY);
        $pdf->SetFont('Helvetica','',9);
        $pdf->Cell(75,5,'N° Folio: ',0,0,'R',0);
        $pdf->SetFont('Helvetica','B',9);
        $pdf->Cell(15,5,str_pad($this->numSigCotizacion, 3, "0", STR_PAD_LEFT),0,0,'R',0);
        $posY+=8;
        $pdf->setXY($inicioX,$posY);
        $pdf->SetFont('Helvetica','',9);
        $pdf->Cell(90,5,'Blvd. Hermenegildo Bustos #1905',0,0,'R',0);
        $posY+=4;
        $pdf->setXY($inicioX,$posY);
        $pdf->Cell(90,5,'Col. Unidad Obrera Infonavit',0,0,'R',0);
        $posY+=4;
        $pdf->setXY($inicioX,$posY);
        $pdf->Cell(90,5,'CP. 37179',0,0,'R',0);
        $posY+=4;
        $pdf->setXY($inicioX,$posY);
        $pdf->Cell(90,5,('León, Guanajuato, México'),0,0,'R',0);
        $posY+=4;
        $pdf->setXY($inicioX,$posY);
        $pdf->Cell(90,5,'Tel.(477)119-2121',0,0,'R',0);
        $posY+=4;
        $pdf->setXY($inicioX,$posY);
        $pdf->Cell(90,5,'Cel.(477)125-4968 | (477)269-5599',0,0,'R',0);
        
        //*******************************************************************************************************************
        
        $inicioX = 10;	
        
        $posY+=2;
        $posX = $inicioX;
        
        $pdf->setXY($posX,$posY);
        $pdf->SetFont('Helvetica','',10);
        $pdf->Cell(14,5,'Cliente: ',0,0,'L',0);
        $pdf->SetFont('Helvetica','B',10);
        $pdf->Cell(90,5,$cliente,0,0,'L',0);
        $pdf->Ln(5);
        $pdf->SetFont('Helvetica','',10);
        $pdf->Cell(17,5,('Dirección: '),0,0,'L',0);
        $pdf->SetFont('Helvetica','B',10);
        $pdf->Cell(90,5,$direccion,0,0,'L',0);
        $pdf->Ln(10);
        $pdf->SetFont('Helvetica','BU',10);
        $pdf->Cell(38,5,$this->dameFechaActual(),0,0,'L',0);
        $pdf->SetFont('Helvetica','B',10);
        $pdf->Cell(5,5,':',0,0,'L',0);
        $pdf->Ln(10);
        $pdf->SetFont('Helvetica','',10);
        $pdf->Multicell(202,5,('Por medio de la presente me permito poner a su consideración la cotización solicitada, quedando al pendiente de cualquier duda o comentario al respecto:'),0,'L',0);
        $pdf->Ln(5);
        
        //DESGLOCE*******************************************************************************************************************
        
        $arrTabla = json_decode($infoTabla,1);
        
        $pdf->SetFont('Helvetica','B',8);
        $pdf->Cell(40,5,('Nombre'),1,0,'C',0);
        $pdf->Cell(50,5,('Descripción'),1,0,'C',0);
        $pdf->Cell(50,5,('Imagen'),1,0,'C',0);
        $pdf->Cell(20,5,('Precio'),1,0,'C',0);
        $pdf->Cell(20,5,('Cantidad'),1,0,'C',0);
        $pdf->Cell(20,5,('Total'),1,0,'C',0);
        
        $pdf->Ln(5);
        
        $tamañoCeldas = 30;
        $posY = $pdf->getY();
        $pdf->SetFont('Helvetica','',8);
        
        $contFilas = 0;
        $contPaginas = 1;
        foreach($arrTabla as $filaEnCurso)
        {
            //SI LA PÁGINA DEL ENCABEZADO LLEGA A 6 FILAS, AGREGAMOS UNA NUEVA HOJA
            if($contFilas==6 && $contPaginas==1)
            {
              $pdf->AddPage();
              $posY = 10;
              $contPaginas++;
              $contFilas = 0;
    
            }
            elseif($contPaginas>1 && $contFilas ==8)//A PARTIR DE AHI, LAS PAGINAS PUEDEN TENER UN MÁXIMO DE 8 FILAS
            {
              $pdf->AddPage();
              $posY = 10;
              $contFilas = 0;
            }
         
            $posX = 10;
            $pdf->setXY($posX,$posY);
            
            $pdf->MultiCell(40,$tamañoCeldas,($filaEnCurso['producto']),1,'L',0);
            $posX+=40;
            $pdf->setXY($posX,$posY);
            
            $pdf->MultiCell(50,$tamañoCeldas,($filaEnCurso['notas']),1,'L',0);
            $posX+=50;
            $pdf->setXY($posX,$posY);
            
            $pdf->MultiCell(50,$tamañoCeldas,'',1,'L',0);
            
            $imagen = '';
            
            $sqlImg = "SELECT imagen_producto as imgProducto FROM producto WHERE id_producto = ".$filaEnCurso['img'];
            $resImg = bd_consultaSQL($sqlImg);
            
            if(bd_error())
            {
                die("Error(1): ".bd_mensajeError());
            }
            else
            {
                $filaImg = bd_dameRegistro($resImg);
                
                if($filaImg['imgProducto']<>'')
                {
                  $imagen = $filaImg['imgProducto'];
                  
                  $imagen=str_replace("data:image/jpeg;base64,","",$imagen);
                  $imagen=str_replace("data:image/png;base64,","",$imagen);
                  $imagen = base64_decode($imagen);
                
                  $im = imagecreatefromstring($imagen);
                    
                  $width = imagesx($im);
                  $height = imagesy($im);
                  //echo($width.'-'.$height).'<hr />';
                  $arrNuevasMedidas = $this->imageResize($width,$height);
                  //die($arrNuevasMedidas['width'].'-'.$arrNuevasMedidas['height']);
                  
                  if($arrNuevasMedidas['height']>$arrNuevasMedidas['width'])
                  {
                       $posXImg = $posX+15;
                       $posYImg = $posY+3;
                  }
                  else
                  {
                       $posXImg = $posX+3;
                       $posYImg = $posY+3;
                  }
                  
                  $pdf->Image('@'.$imagen,$posXImg,$posYImg,$arrNuevasMedidas['width'],$arrNuevasMedidas['height']);
                }
                
                $posX+=50;
                $pdf->setXY($posX,$posY);
                
                $pdf->MultiCell(20,$tamañoCeldas,('$ '.number_format((float)$filaEnCurso['precio'], 2, '.', ',')),1,'C',0);
                $posX+=20;
                $pdf->setXY($posX,$posY);
                
                $pdf->MultiCell(20,$tamañoCeldas,($filaEnCurso['cantidad']),1,'C',0);
                $posX+=20;
                $pdf->setXY($posX,$posY);
                
                $pdf->MultiCell(20,$tamañoCeldas,('$ '.number_format((float)$filaEnCurso['total'], 2, '.', ',')),1,'C',0);
                $posX+=20;
                $pdf->setXY($posX,$posY);
                
                $posY+=$tamañoCeldas;
                $contFilas++;
            }
        }
        
        if($posY>180)
        {
         $pdf->AddPage();
         $posY = 10;
        }
        else
        {
         $posY+=2;
        }
        
        if($tipoCotizacion== 'renta')
        {
              $pdf->setXY(10,$posY);
              
              $pdf->SetFont('Helvetica','',7);
              $pdf->Multicell(100,2,("*Pago por anticipado."),0,'L',0);
              $pdf->Ln(1.5);
              $pdf->Multicell(100,2,("*Entregas solo en la zona de León, Gto."),0,'L',0);
        }
        
        $posY+=14;
        
        $pdf->setXY(10,$posY);
        
        if($tipoCotizacion== 'renta')
        {
              $pdf->SetFont('Helvetica','B',7);
              $pdf->Multicell(100,2,("REQUISITOS:"),0,'L',0);
              $pdf->Ln(2);
              $pdf->SetFont('Helvetica','',7);
              $pdf->Multicell(100,2,("• COPIA DE CREDENCIAL DE IFE DEL REPRESENTANTE."),0,'L',0);
              $pdf->Ln(1.5);
              $pdf->Multicell(100,2,("• COPIA DE COMPROBANTE DE DOMICILIO RECIENTE."),0,'L',0);
              $pdf->Ln(1.5);
              $pdf->Multicell(100,2,("• COPIA DEL ALTA EN HACIENDA R.F.C."),0,'L',0);
              $pdf->Ln(1.5);
              $pdf->Multicell(100,2,("• COPIA DEL ACTA CONSTITUTIVA (PERSONAS MORALES)."),0,'L',0);
        }
        
        $pdf->setXY(10,$posY);
        $pdf->SetFont('Helvetica','B',9);
        $pdf->Cell(168,5,('SUBTOTAL:'),0,0,'R',0);
        $pdf->SetFont('Helvetica','',9);
        $pdf->Cell(6,5,'$ ',0,0,'R',0);
        $pdf->Cell(19,5,(number_format((float)$subtotal, 2, '.', ',')),0,0,'R',0);
        $posY+=7;
        $pdf->setXY(10,$posY);
        $pdf->SetFont('Helvetica','B',9);
        $pdf->Cell(168,5,('+ IVA (16%):'),0,0,'R',0);
        $pdf->SetFont('Helvetica','',9);
        $pdf->Cell(6,5,'$ ',0,0,'R',0);
        $pdf->Cell(19,5,(number_format((float)$iva, 2, '.', ',')),0,0,'R',0);
        $posY+=7;
        
        if($tipoCliente == 'moral')
        {
            $pdf->setXY(10,$posY);
            $pdf->SetFont('Helvetica','B',9);
            $pdf->Cell(168,5,('- Ret. IVA (10.67%):'),0,0,'R',0);
            $pdf->SetFont('Helvetica','',9);
            $pdf->Cell(6,5,'$ ',0,0,'R',0);
            $pdf->Cell(19,5,(number_format((float)$retIVA, 2, '.', ',')),0,0,'R',0);
            $posY+=7;
        }
        
        $pdf->setXY(10,$posY);
        $pdf->SetFont('Helvetica','B',9);
        $pdf->Cell(168,5,('TOTAL:'),0,0,'R',0);
        $pdf->SetFont('Helvetica','',9);
        $pdf->Cell(6,5,'$ ',0,0,'R',0);
        $pdf->Cell(19,5,(number_format((float)$total, 2, '.', ',')),0,0,'R',0);
        $posY+=7;
        
        $pdf->setXY(10,$posY);
        $pdf->SetFont('Helvetica','',7);
        $pdf->Cell(193,5,('(Precios sujetos a cambio sin previo aviso.)'),0,0,'R',0);
        $pdf->Ln(3);
        $pdf->Cell(181,5,('Vigencia de la oferta: '),0,0,'R',0);
        $pdf->SetFont('Helvetica','B',7);
        $pdf->Cell(12,5,($vigencia.' día(s).'),0,0,'R',0);
        
        $pdf->Ln(3);
        $pdf->SetFont('Helvetica','B',7);
        $pdf->Cell(20,5,('NOTAS / OBSERVACIONES:'),0,0,'L',0);
        $pdf->Ln(6);
        $pdf->SetFont('Helvetica','',7);
        $pdf->Multicell(195,10,($observaciones),1,'L',0);
        
        $pdf->Ln(5);
        $pdf->SetFont('Helvetica','',10);
        $pdf->Multicell(202,5,("Contamos con una gran variedad de equipo ligero adicional y andamios para atender cualquier requerimiento de su obra.\nSin otro particular por el momento quedo a sus órdenes."),0,'L',0);
        
        $pdf->Ln(6);
        
        $sqlInfoUsuario = "SELECT
                                id_usuario as id,
                                nombre_usuario AS nombre,
                                correo_usuario AS correo,
                                ua.descripcion_usuarioarea AS area
                            FROM
                                _usuario u
                                    INNER JOIN
                                _usuario_area ua ON ua.id_usuarioarea = u.id_usuarioarea
                            WHERE u.id_usuario = ".$idUsuario;
                            
        $resInfoUsuario = bd_consultaSQL($sqlInfoUsuario);
        
        if(bd_error())
        {
            die("Error(2): ".bd_mensajeError());
        }
        else
        {
            $filaInfoUsuario = bd_dameRegistro($resInfoUsuario);
        }
    
        $pdf->SetFont('Helvetica','B',10);
        $pdf->Cell(202,5,('ATENTAMENTE'),0,0,'C',0);
        $pdf->Ln(7);
        $pdf->Cell(202,5,($filaInfoUsuario['nombre']),0,0,'C',0);
        $pdf->Ln(7);
        $pdf->Cell(202,5,($filaInfoUsuario['area']),0,0,'C',0);
        
        //GUARDAMOS EN BD
        if($this->accionPDF == 'enviar' || $this->accionPDF == 'guardar')
        {
            $ruta = 'archivos'.DIRECTORY_SEPARATOR.'cotizaciones'.DIRECTORY_SEPARATOR.$nombrepdf;

            $this->guardaCotizacion($ruta,($cliente),strtoupper($tipoCotizacion),$total,$idUsuario);    
        }
        
        if(!$this->error)
        {
            if($this->accionPDF == 'enviar')
            {
                $pdf->Output($this->servidorGral.$ruta, 'F');
                
                $mail = new PHPMailer\PHPMailer\PHPMailer();
                $mail->IsSMTP();
                //$mail->SMTPDebug = 4;
                    
                //echo "otros";
                $host = "";
                $user = $cuenta;
                $port = 465;
                $secure = 'ssl';
                
                $sender = $filaInfoUsuario['id'];
                
                $mail->SMTPAuth = true;
                $mail->SMTPSecure = $secure; 
                $mail->Host = $host; // A RELLENAR. Aquí pondremos el SMTP a utilizar. Por ej. mail.midominio.com
                $mail->Username = $user; // A RELLENAR. Email de la cuenta de correo. ej.info@midominio.com La cuenta de correo debe ser creada previamente. 
                $mail->Password = ""; // A RELLENAR. Aqui pondremos la contraseña de la cuenta de correo
                $mail->Port = $port; // Puerto de conexión al servidor de envio. 
                $mail->setFrom($user, "Revelsa"); // A RELLENARDesde donde enviamos (Para mostrar). Puede ser el mismo que el email creado previamente.
                $mail->Sender = $cuenta;
                $mail->FromName = "Revelsa <".$cuenta.">"; //A RELLENAR Nombre a mostrar del remitente. 
                $mail->AddAddress($correo); // Esta es la dirección a donde enviamos
                //$mail->AddAddress("test-i0oof@mail-tester.com"); // Esta es la dirección a donde enviamos
                $mail->AddAttachment($this->servidorGral.$ruta);
                $mail->AddReplyTo($cuenta);
                $mail->IsHTML(true); // El correo se envía como HTML 
                $mail->Subject = utf8_decode($asunto); // Este es el titulo del email. 
                //$body = "Prueba de correo electronico para mejorar la puntuacion. Esto genera un problema ya que no se manda al inbox.";
                //die(__DIR__.'/img/'.$sender.'.png');
                $mail->AddEmbeddedImage($this->servidorGral.'img'.DIRECTORY_SEPARATOR.'rpt'.DIRECTORY_SEPARATOR.'firmas'.DIRECTORY_SEPARATOR.$sender.'.png','firmaRev',$sender.'.png');
                //$mail->SMTPDebug = 4;
                
                //echo "<pre>";
                //print_r(error_get_last());
                //die();
                
                $body = utf8_decode($mensaje);
                $body.='<br /><br /><img src="cid:firmaRev">';
                $mail->Body = $body; // Mensaje a enviar.
                $mail->AltBody  =  utf8_decode($mensaje); 
                $exito = $mail->Send(); // Envía el correo.
                //$exito = true;
                $arrRetorno = array('Error'=>false,'ErrorMensaje'=>'');
                
                if(!$exito)
                {                  
                    $arrRetorno = array('Error'=>true,'ErrorMensaje'=>'Hubo un problema al enviar el correo. Contacta a un administrador.');
                    $this->defineError($arrRetorno);
                    
                    //echo $mail->ErrorInfo;
                    //die();
                }
                else
                {
                    $this->nombrePDF = $nombrepdf;
                }
            }
            elseif($this->accionPDF == 'guardar')
            {
                $this->nombrePDF = $nombrepdf;
                $pdf->Output($this->servidorGral.$ruta, 'F');
            }
            else
            {
                //$nombrepdf = 'cot'.strtotime(date('Y-m-d')).rand(1,1000);
                $pdf->Output($nombrepdf, 'I');
            }
        }
        
    }
    
    
	public function imageResize($oWidth,$oHeight)
	{
		//$new_width = round((($newSizePercent/100)*$oWidth));
		//$new_height = round((($newSizePercent/100)*$oHeight));
	 
	    if($oWidth>$oHeight)
	    {
		  $widthCorrecto = 38;
		  $heightCorrecto= 22;
		}
		else
		{
		  $widthCorrecto = 17;
		  $heightCorrecto= 25;
		}
		
		$arrNewSize = array('width'=>$widthCorrecto,'height'=>$heightCorrecto);
		
		return $arrNewSize;
	}
	
	public function dameFechaActual()
	{
		$mes = '';
		switch(date('m'))
		{
			case '01':
				$mes = 'Enero';
			break;
		
			case '02':
				$mes = 'Febrero';
			break;
		
			case '03':
				$mes = 'Marzo';
			break;
		
			case '04':
				$mes = 'Abril';
			break;
		
			case '05':
				$mes = 'Mayo';
			break;
		
			case '06':
				$mes = 'Junio';
			break;
		
			case '07':
				$mes = 'Julio';
			break;
		
			case '08':
				$mes = 'Agosto';
			break;
		
			case '09':
				$mes = 'Septiembre';
			break;
		
			case '10':
				$mes = 'Octubre';
			break;
		
			case '11':
				$mes = 'Noviembre';
			break;
		
			case '12':
				$mes = 'Diciembre';
			break;
		
			
		}
		
		$fecha = 'A '.date('d').' de '.$mes.' del '.date('Y');
		
		return $fecha;
	}
    
    /****************************************************************************************************
    FUNCION: guardaCotizacion()
    OBJETIVO: GUARDA LA COTIZACIÓN EN LA BD
    PARAMETROS: OBJETO:PROPIEDAD
    ****************************************************************************************************/
    function guardaCotizacion($ruta,$cliente,$tipo,$total,$idUsuario)
    {
        $this->sql = "CALL sp_finanzas_guardaCotizacion(?,?,?,?,?)";
        $stmt = bd_preparaSQL($this->sql,array('1_s'=>$ruta,
                                               '2_s'=>$cliente,
                                               '3_s'=>$tipo,
                                               '4_d'=>$total,
                                               '5_i'=>$idUsuario
                                               )
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
    FUNCION: dameCotizaciones()
    OBJETIVO: DEVUELVE LAS COTIZACIONES REALIZADAS DE UN AÑO A LA FECHA
    PARAMETROS: OBJETO:PROPIEDAD
    ****************************************************************************************************/
    function dameCotizaciones()
    {
        $arrResultado = array();
        
        $this->sql = "CALL sp_finanzas_dameCotizaciones()";
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
