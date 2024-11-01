<?php
	error_reporting(E_ERROR);
	//echo "<pre>";
	//print_r($_POST);
	//die();
	
	//echo '<img src="data:image/jpeg;base64,'.( $_FILES['imagen']['name'] ).'"/>';
	
	//die();
	//echo "<pre>";
	//print_r($_FILES);
	//die();
	
	$nombrepdf = time().'.pdf';
	header('Content-Disposition: inline; filename="'.$nombrepdf.'"');
	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Pragma: public'); 

	ini_set("memory_limit","150M");
	
	require_once("../lib/tcpdf/tcpdf.php");
	require_once("../lib/PHPMailer/src/PHPMailer.php");
    require_once("../lib/PHPMailer/src/SMTP.php");
	require_once("../lib/PHPMailer/src/Exception.php");

	stream_wrapper_register('var', 'VariableStream');	

	
	$pdf = new TCPDF('P','mm', 'LETTER', true, 'UTF-8', false);
	$pdf->setPrintHeader(false);
	$pdf->setPrintFooter(false);	
	$pdf->SetAutoPageBreak(FALSE);
	$pdf->SetAuthor('SIE :: DELASALLE');
	$pdf->SetLineWidth(.2);
	
	$pdf->AddPage();
	
	
	//ENCABEZADO********************************************************************************************************************
	$pdf->Image('../img/rpt/logoRpt.png',10,10,60,24);
	
	$inicioX = 120;
	$inicioY = 10;
	
	$posY = $inicioY;
	
	$pdf->setXY($inicioX,$posY);
	$pdf->SetFont('Helvetica','',9);
	$pdf->Cell(75,5,'N° Folio: ',0,0,'R',0);
	$pdf->SetFont('Helvetica','B',9);
	$pdf->Cell(15,5,$_POST[''],0,0,'R',0);
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
	$pdf->Cell(90,5,$_POST['cliente'],0,0,'L',0);
	$pdf->Ln(5);
	$pdf->SetFont('Helvetica','',10);
	$pdf->Cell(17,5,('Dirección: '),0,0,'L',0);
	$pdf->SetFont('Helvetica','B',10);
	$pdf->Cell(90,5,$_POST['direccion'],0,0,'L',0);
	$pdf->Ln(10);
	$pdf->SetFont('Helvetica','BU',10);
	$pdf->Cell(38,5,dameFechaActual(),0,0,'L',0);
	$pdf->SetFont('Helvetica','B',10);
	$pdf->Cell(5,5,':',0,0,'L',0);
	$pdf->Ln(10);
	$pdf->SetFont('Helvetica','',10);
	$pdf->Multicell(202,5,('Por medio de la presente me permito poner a su consideración la cotización solicitada, quedando al pendiente de cualquier duda o comentario al respecto:'),0,'L',0);
	$pdf->Ln(5);
	
	//DESGLOCE*******************************************************************************************************************
	
	$arrTabla = json_decode($_POST['infoTabla'],1);
	
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
			  $arrNuevasMedidas = imageResize($width,$height);
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
	
	if($_POST['tipoCotizacion']== 'renta')
	{
		  $pdf->setXY(10,$posY);
		  
		  $pdf->SetFont('Helvetica','',7);
		  $pdf->Multicell(100,2,("*Pago por anticipado."),0,'L',0);
		  $pdf->Ln(1.5);
		  $pdf->Multicell(100,2,("*Entregas solo en la zona de León, Gto."),0,'L',0);
	}
	
	$posY+=14;
	
    $pdf->setXY(10,$posY);
	
	if($_POST['tipoCotizacion']== 'renta')
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
	$pdf->Cell(19,5,(number_format((float)$_POST['subtotal'], 2, '.', ',')),0,0,'R',0);
	$posY+=7;
	$pdf->setXY(10,$posY);
	$pdf->SetFont('Helvetica','B',9);
	$pdf->Cell(168,5,('+ IVA (16%):'),0,0,'R',0);
	$pdf->SetFont('Helvetica','',9);
	$pdf->Cell(6,5,'$ ',0,0,'R',0);
	$pdf->Cell(19,5,(number_format((float)$_POST['iva'], 2, '.', ',')),0,0,'R',0);
	$posY+=7;
	
	if($_POST['tipoCliente'] == 'moral')
	{
		$pdf->setXY(10,$posY);
		$pdf->SetFont('Helvetica','B',9);
		$pdf->Cell(168,5,('- Ret. IVA (10.67%):'),0,0,'R',0);
		$pdf->SetFont('Helvetica','',9);
		$pdf->Cell(6,5,'$ ',0,0,'R',0);
		$pdf->Cell(19,5,(number_format((float)$_POST['retIVA'], 2, '.', ',')),0,0,'R',0);
		$posY+=7;
	}
	
	$pdf->setXY(10,$posY);
	$pdf->SetFont('Helvetica','B',9);
	$pdf->Cell(168,5,('TOTAL:'),0,0,'R',0);
	$pdf->SetFont('Helvetica','',9);
	$pdf->Cell(6,5,'$ ',0,0,'R',0);
	$pdf->Cell(19,5,(number_format((float)$_POST['total'], 2, '.', ',')),0,0,'R',0);
	$posY+=7;
	
	$pdf->setXY(10,$posY);
	$pdf->SetFont('Helvetica','',7);
	$pdf->Cell(193,5,('(Precios sujetos a cambio sin previo aviso.)'),0,0,'R',0);
	
	$pdf->Ln(6);
	$pdf->SetFont('Helvetica','B',7);
	$pdf->Cell(20,5,('NOTAS / OBSERVACIONES:'),0,0,'L',0);
	$pdf->Ln(6);
	$pdf->SetFont('Helvetica','',7);
	$pdf->Multicell(195,10,($_POST['observaciones']),1,'L',0);
	
	$pdf->Ln(5);
	$pdf->SetFont('Helvetica','',10);
	$pdf->Multicell(202,5,("Contamos con una gran variedad de equipo ligero adicional y andamios para atender cualquier requerimiento de su obra.\nSin otro particular por el momento quedo a sus órdenes."),0,'L',0);
	
	$pdf->Ln(6);

	$pdf->SetFont('Helvetica','B',10);
	$pdf->Cell(202,5,('ATENTAMENTE'),0,0,'C',0);
	$pdf->Ln(7);
	$pdf->Cell(202,5,($_POST['']),0,0,'C',0);
	$pdf->Ln(7);
	$pdf->Cell(202,5,($_POST['']),0,0,'C',0);
	
	/*
	
	$imagen = file_get_contents($_FILES['BAILARINA']['tmp_name']);
	
	//echo base64_encode($imagen);
	
	
	$pdf->MemImage($imagen,10,10,10,10);
	*/
	
	if($_POST['']== 'enviar')
	{
		$pdf->Output(__DIR__.'/archivos/'.$nombrepdf, 'F');
		
		$mail = new PHPMailer\PHPMailer\PHPMailer();
		$mail->IsSMTP();
		//$mail->SMTPDebug = 4;
		
		switch($_POST[''])
		{
			case 'BERE':
				$sender = 'BCONTRERAS';
			break;
		
			case 'MARISOL':
				$sender = 'MMUNOZ';
			break;
		
			case 'ING':
				$sender = 'GSANCHEZ';
			break;
		}
		
		$arrCorreo = explode('@',$_POST['']);
			
		//echo "otros";
		$host = "mail.revelsa.com.mx";
		$user = $_POST[''];
		$port = 465;
		$secure = 'ssl';
		
		$mail->SMTPAuth = true;
		$mail->SMTPSecure = $secure; 
		$mail->Host = $host; // A RELLENAR. Aquí pondremos el SMTP a utilizar. Por ej. mail.midominio.com
		$mail->Username = $user; // A RELLENAR. Email de la cuenta de correo. ej.info@midominio.com La cuenta de correo debe ser creada previamente. 
		$mail->Password = "YagoLucas8820"; // A RELLENAR. Aqui pondremos la contraseña de la cuenta de correo
		$mail->Port = $port; // Puerto de conexión al servidor de envio. 
		$mail->setFrom($user, "Revelsa"); // A RELLENARDesde donde enviamos (Para mostrar). Puede ser el mismo que el email creado previamente.
		$mail->Sender = $_POST[''];
		$mail->FromName = "Revelsa <".$_POST[''].">"; //A RELLENAR Nombre a mostrar del remitente. 
		$mail->AddAddress($_POST['']); // Esta es la dirección a donde enviamos
		//$mail->AddAddress("test-i0oof@mail-tester.com"); // Esta es la dirección a donde enviamos
		$mail->AddAttachment(__DIR__.'/archivos/'.$nombrepdf);
		$mail->AddReplyTo($_POST['']);
		$mail->IsHTML(true); // El correo se envía como HTML 
		$mail->Subject = utf8_decode($_POST['']); // Este es el titulo del email. 
		//$body = "Prueba de correo electronico para mejorar la puntuacion. Esto genera un problema ya que no se manda al inbox.";
		//die(__DIR__.'/img/'.$sender.'.png');
		$mail->AddEmbeddedImage(__DIR__.'/img/'.$sender.'.PNG','firmaRev',$sender.'.PNG');
		//$mail->SMTPDebug = 4;
		
		//echo "<pre>";
		//print_r(error_get_last());
		//die();
		
		$body = utf8_decode($_POST['']);
		$body.='<br /><br /><img src="cid:firmaRev">';
		$mail->Body = $body; // Mensaje a enviar.
		$mail->AltBody  =  utf8_decode($_POST['']); 
		$exito = $mail->Send(); // Envía el correo.
		if($exito)
		{
			  echo "El correo fue enviado correctamente.";
		}
		else
		{
			  echo "Hubo un problema. Contacta a un administrador.";
			  echo $mail->ErrorInfo;
		}
	}
	else
	{
		$pdf->Output($nombrepdf, 'I');
	}
	
	
	
	function imageResize($oWidth,$oHeight)
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
	
	function dameFechaActual()
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
?>
