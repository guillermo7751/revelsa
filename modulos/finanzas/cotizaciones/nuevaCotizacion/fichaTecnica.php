<?php
	ini_set("display_errors",1);
	error_reporting(E_ERROR);

	$servidorGral = $_SERVER['DOCUMENT_ROOT']."\\revelsa\\";

	require_once($servidorGral."lib".DIRECTORY_SEPARATOR."tcpdf".DIRECTORY_SEPARATOR."tcpdf.php");

	$nombrepdf = time().'.pdf';
	header('Content-Disposition: inline; filename="'.$nombrepdf.'"');
	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Pragma: public'); 

	ini_set("memory_limit","150M");

	$pdf = new TCPDF('P','mm', 'LETTER', true, 'UTF-8', false);
	$pdf->setPrintHeader(false);
	$pdf->setPrintFooter(false);	
	$pdf->SetAutoPageBreak(FALSE);
	$pdf->SetAuthor('SIE :: DELASALLE');
	$pdf->SetLineWidth(.2);
	
	$pdf->AddPage();

	$pdf->Output($nombrepdf, 'I');
?>
