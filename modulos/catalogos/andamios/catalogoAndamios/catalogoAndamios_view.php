<?php

ini_set("display_errors",1);
error_reporting(E_ALL);

function catalogoAndamios_inicio()
{
	//INCLUIMOS EL ARCHIVO JS
	$resultado= '<script src="modulos/catalogos/andamios/catalogoAndamios/catalogoAndamios.js?'.time().'"></script>';

	$resultado.= '<div class="card shadow mb-4" id="divView_catalogoAndamios_inicio">
					<div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
					  <h5 class="m-0 font-weight-bold text-primary">Relación de Andamios y Apuntalamiento</h5>
					  <button type="button" class="btn btn-default" id="btnNuevoEquipo" style="background-color: #30a6fc; color: white;"><i class="fa fa-plus" aria-hidden="true"></i>  Nuevo</button>
						
					</div>
					<div class="card-body" id="div_catalogoAndamios_inicio_tablaCatalogo">
						
					</div>
				  </div>
				  <div class="card shadow mb-5" style="display:none;" id="divView_catalogoAndamios_editar">
					<div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
					  <h5 class="m-0 font-weight-bold text-primary" id="viewFormulario">Agregar Producto</h5>
					  <button type="button" class="btn btn-default" id="btnRegresarInicio" style="background-color: #de6947; color: white;"><i class="fa fa-arrow-left" aria-hidden="true"></i>  Regresar</button>
					</div>
					<div class="card-body" id="div_catalogoAndamios_editar_form">
						
					</div>
				  </div>';
	
	return $resultado;
	
}

function catalogoAndamios_tablaCatalogo($arrCatalogo)
{
	$resultado = '<div class="table-responsive bordeada">
					<table class="table table-bordered table-responsive-md table-striped text-center" id="tblEquipos" style="width=100%" cellspacing="0">
						<thead>
							<tr>
							  <th style="width:15%">Producto</th>
							  <th style="width:40%">Descripción</th>
							  <th style="width:10%">Marca</th>
							  <th style="width:15%">Precio ($)</th>
							  <th style="width:20%">Acciones</th>
							</tr>
						</thead>
						<tbody>';

	if(sizeof($arrCatalogo)>0)
	{
		foreach($arrCatalogo as $equipoEnCurso)
		{
			$btnVer ='<a href="#" title="Ver" style="margin-left:5px;" class="btn btn-circle btn-primary btn-sm accionVer" data-id="'.$equipoEnCurso['id'].'"><i class="far fa-eye"></i></a>';
			$btnEditar ='<a href="#" title="Editar" style="margin-left:5px;" class="btn btn-circle btn-info btn-sm accionEditar" data-id="'.$equipoEnCurso['id'].'"><i class="fas fa-edit"></i></a>';
			$btnEliminar ='<a href="#" title="Eliminar" style="margin-left:5px;" class="btn btn-danger btn-circle btn-sm accionEliminar" data-nombre="'.$equipoEnCurso['nombre'].'" data-id="'.$equipoEnCurso['id'].'"><i class="fas fa-trash"></i></a>';
			
			$resultado.='<tr>
							<td style="width:15%">'.$equipoEnCurso['nombre'].'</td>
							<td class="text-left" style="width:40%">'.$equipoEnCurso['descripcion'].'</td>
							<td style="width:10%">'.$equipoEnCurso['marca'].'</td>
							<td class="text-right" style="width:15%">$ '.$equipoEnCurso['precioVenta'].'</td>
							<td style="width:20%">'.$btnVer.$btnEditar.$btnEliminar.'</td>
						</tr>';
		}
	}
	else
	{
		$resultado.='<tr>
						<td></td>
						<td>No se encontraron productos.</td>
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

function catalogoAndamios_vistaEditar($arrPeriodosRenta,$arrInfoProducto,$tipoAccion)
{
	
	//TABLA DE PRECIOS A LA RENTA
	
	$tblPreciosRenta = '<div class="table-responsive">
							<table class="table table-fixed preciosRenta tablaCheckbox" id="tblPeriodosRenta" cellspacing="0">
								<thead>
									<tr>
										<th class="text-right" style="width:50%;">PERIODO</th>
										<th class="text-left" style="width:50%;">PRECIO ($)</th>
									</tr>
								</thead>
								<tbody>';
								
	foreach($arrPeriodosRenta as $periodoEnCurso)
	{
		//ASIGNAMOS LOS PRECIOS DE RENTA DE LOS PRODUCTOS QUE CORRESPONDA
		$checked = '';
		$classChecked = '';
		$disabled = 'disabled';
		$precioAsig = '';
		
		if(isset($arrInfoProducto['preciosRenta']))
		{
			$strPreciosRenta = $arrInfoProducto['preciosRenta'];
			
			$arrPreciosRenta = explode(',',$strPreciosRenta);
			
			foreach($arrPreciosRenta as $precioRentaEnCurso)
			{
				$precioRenta = explode('-',$precioRentaEnCurso);
				
				if($periodoEnCurso['id'] == $precioRenta[0])
				{
					$checked = 'checked';
					$classChecked = 'clsTiempoRenta';
					$disabled='';
					$precioAsig = $precioRenta[1];
					break;
				}
			}
		}
		
		$tblPreciosRenta.='<tr>
							   <td class="text-right" style="width:50%;font-weight:bold;">
									<input type="checkbox" '.$checked.' class="chkInTable chkRenta formCatalogo" data-id="'.$periodoEnCurso['id'].'" id="customCheck_'.$periodoEnCurso['id'].'">
									'.$periodoEnCurso['tiempo'].'
							   </td>
							   <td class="text-left" style="width:50%;">$ <input '.$disabled.' id="'.$periodoEnCurso['id'].'" class="'.$classChecked.' formCatalogo" style="width:70%;padding-left:5px;" type="number" value="'.$precioAsig.'" /></td>
						   </tr>';
	}
	
	$tblPreciosRenta.= '</tbody>
					</table>
				</div>';
				
	//LLENAMOS LOS VALORES QUE VENGAN DE LA INFO DEL PRODUCTO PARA LA EDICION O LA VISTA
	
	$nombreProducto = '';
	if(isset($arrInfoProducto['nombre']))
	{
		$nombreProducto = $arrInfoProducto['nombre'];
	}
	
	$descripcionProducto = '';
	if(isset($arrInfoProducto['descripcion']))
	{
		$descripcionProducto = $arrInfoProducto['descripcion'];
	}
	
	$montoVenta = '';
	if(isset($arrInfoProducto['precioVenta']))
	{
		$montoVenta = $arrInfoProducto['precioVenta'];
	}
	
	$imagen = '#';
	if(isset($arrInfoProducto['imagen']))
	{
		$imagen = $arrInfoProducto['imagen'];
	}
	
	$descDefaultChecked = '';
	if(isset($arrInfoProducto['isDefaultDesc']) && $arrInfoProducto['isDefaultDesc'] == 1)
	{
		$descDefaultChecked = 'checked';
	}
	
	$cont = '
			<div class="row">
				<div class="col-md-6">
					<div class="form-group col-xs-12 col-sm-12 col-md-12 text-left">
						<label for="txtNombreEquipo" class="label-dark">Nombre del producto</label>
						<input maxlength="100" class="form-control formCatalogo" id="txtNombreEquipo" placeholder="Ingrese el nombre del producto" value="'.$nombreProducto.'">
					</div>
					<div class="form-group col-xs-12 col-sm-12 col-md-12 text-left">
						<label for="txtDescEquipo" class="label-dark">Descripción</label>
						<textarea class="form-control formCatalogo" maxlength="500" id="txtDescEquipo" style="resize:none;" placeholder="Ingrese una descripción del producto">'.$descripcionProducto.'</textarea>
						
						<div class="mt-2 mb-2"></div>
						<div class="input-group mb-3">
							<div class="input-group-prepend">
							  <input type="checkbox" id="descDefault" class="chkForm formCatalogo form-control" '.$descDefaultChecked.'>
							</div>
							&nbsp;&nbsp;Descripción default
						</div>
					</div>
					<div class="form-group col-xs-12 col-sm-12 col-md-12 text-left">
						<div class="">
							<label for="txtMontoVenta" class="label-dark formCatalogo">Imagen del producto</label>
						</div>
						<div class="">';
					
					$clsBefore = '';
					if($imagen<>'#' || ($imagen=='#' && $tipoAccion == 'ver'))
					{
						$clsBefore = 'image-no-before';		
					}
					
					$divImg = '<div class="image-area mt-4 clsEquipoImg '.$clsBefore.'"><img id="resultadoEquipo" src="'.$imagen.'" alt="" data-vacio="0" class="img-fluid rounded shadow-sm mx-auto d-block"></div>';
					if($tipoAccion <> 'ver')
					{
						
						$cont.='
								<div class="input-group mb-3 px-2 py-2 rounded-pill bg-white shadow-sm">
									
									<input id="uploadEquipo" type="file" accept="image/png, image/jpeg" onchange="js_catalogoAndamios_cargarImagen(this,\'resultadoEquipo\',\'clsEquipoImg\');" class="form-control border-0 upload">
									
									<label id="" for="uploadEquipo" class="font-weight-light text-muted upload-label">Elegir archivo</label>
									<div class="input-group-append">
										<label for="uploadEquipo" class="btn btn-light m-0 rounded-pill px-4"> <i class="fa fa-cloud-upload mr-2 text-muted"></i><small class="text-uppercase font-weight-bold text-muted formCatalogo">Elegir archivo</small></label>
									</div>
								</div>
								'.$divImg.'
								<br />
								<div class="d-flex justify-content-center">
									<button type="button" type="button" class="btn btn-default btn-danger formCatalogo" id="btnEliminarImagen"><i class="fas fa-trash" aria-hidden="true"></i>  Quitar imagen</button>
								</div>';
					}
					else
					{
						$cont.=$divImg;
					}
	
	$cont.='						
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group col-xs-12 col-sm-8 col-md-8 text-left">
						<div class="">
							<label for="txtMontoVenta" class="label-dark">Monto a la venta</label>
						</div>
						<div class="">
							<div class="input-group mb-3">
								<div class="input-group-prepend">
								  <span class="input-group-text">$</span>
								</div>
								<input type="number" min="0" class="form-control formCatalogo" placeholder="Ingrese un monto" id="txtMontoVenta" value="'.$montoVenta.'">
							</div>
						</div>
					</div>
					<div class="form-group col-xs-12 col-sm-12 col-md-12 text-left">
						<div class="">
							<label for="divTblPeriodosRenta" class="label-dark">Precios a la renta</label>
						</div>
						'.$tblPreciosRenta.'
					</div>
				</div>
			</div>';
			
	if($tipoAccion <> 'ver')
	{
		$cont.='	
				<div class="col-md-12">
					<hr class="solid">
					<div class="d-flex justify-content-center">
						<button type="button" type="button" class="btn btn-default" id="btnGuardarEquipo" data-id="" data-accion="" style="background-color: #1cc88a; color: white;"><i class="far fa-save" aria-hidden="true"></i>  Guardar producto</button>
					</div
				</div>
				';
	}
	
	return $cont;
}

?>