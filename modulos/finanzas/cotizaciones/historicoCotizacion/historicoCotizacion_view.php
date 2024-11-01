<?php

ini_set("display_errors",1);
error_reporting(E_ALL);

function historicoCotizacion_inicio()
{
	//INCLUIMOS EL ARCHIVO JS
	$resultado= '<script src="modulos/finanzas/cotizaciones/historicoCotizacion/historicoCotizacion.js?'.time().'"></script>';

	$resultado.= '<div class="card shadow mb-4" id="divView_historicoCotizacion_inicio">
					<div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
					  <h5 class="m-0 font-weight-bold text-primary">Histórico de Cotizaciones</h5>
					</div>
					<div class="card-body" id="div_historicoCotizacion_inicio_tablaCotizaciones">
						
					</div>
				  </div>
				  
				  ';
	
	return $resultado;
	
}


function historicoCotizacion_tablaCotizaciones($arrCotizaciones)
{
	$resultado = '<div class="table-responsive bordeada">
					<table class="table table-bordered table-responsive-md table-striped text-center" id="tblCotizaciones" style="width=100%" cellspacing="0">
						<thead>
							<tr>
							  <th>N° Folio</th>
							  <th>Cliente</th>
							  <th>Tipo</th>
							  <th>Total ($)</th>
							  <th>Fecha</th>
							  <th>Acciones</th>
							</tr>
						</thead>
						<tbody>';

	if(sizeof($arrCotizaciones)>0)
	{
		foreach($arrCotizaciones as $cotEnCurso)
		{
			$btnVer ='<a href="#" title="Ver" style="margin-left:5px;" class="btn btn-circle btn-primary btn-sm accionVer" data-id="'.$cotEnCurso['id'].'" data-ruta="'.$cotEnCurso['ruta'].'"><i class="far fa-eye"></i></a>';
			
			$resultado.='<tr>
							<td>'.$cotEnCurso['folio'].'</td>
							<td>'.$cotEnCurso['cliente'].'</td>
							<td>'.$cotEnCurso['tipo'].'</td>
							<td>$ '.$cotEnCurso['total'].'</td>
							<td>'.$cotEnCurso['fecha'].'</td>
							<td>'.$btnVer.'</td>
						</tr>';
		}
	}
	else
	{
		$resultado.='<tr>
						<td></td>
						<td>No se encontraron cotizaciones.</td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>';
	}
	
	$resultado.='</tbody>
			</table>
		</div>';
	
		
	return $resultado;
}

?>