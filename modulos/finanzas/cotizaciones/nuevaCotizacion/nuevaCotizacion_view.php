<?php

ini_set("display_errors",1);
error_reporting(E_ALL);

function nuevaCotizacion_inicio()
{
	//INCLUIMOS EL ARCHIVO JS
	$resultado= '<script src="modulos/finanzas/cotizaciones/nuevaCotizacion/nuevaCotizacion.js?'.time().'"></script>';

	$resultado.= '<div class="card shadow mb-4" id="divView_nuevaCotizacion_inicio">
					<div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
					  <h5 class="m-0 font-weight-bold text-primary">Generar Cotización</h5>
					  
					</div>
					<div class="card-body" id="div_nuevaCotizacion_inicio_form">
						
					</div>
				  </div>
				  <div class="card shadow mb-4" style="display:none;" id="divView_nuevaCotizacion_enviar">
					<div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
					  <h5 class="m-0 font-weight-bold text-primary">Enviar / Guardar Cotización</h5>
					  <button type="button" class="btn btn-default" id="btnRegresarInicio" style="background-color: #de6947; color: white;"><i class="fa fa-arrow-left" aria-hidden="true"></i>  Regresar</button>
					</div>
					<div class="card-body" id="div_nuevaCotizacion_enviar_form">
						
					</div>
				  </div>
				  ';
	
	return $resultado;
	
}

function nuevaCotizacion_vistaForm($idAsignar)
{
	$cont = '
			<div class="row">
				<div class="col-md-6">
					<div class="form-group col-xs-12 col-sm-12 col-md-12 text-left">
						<label for="txtCliente" class="label-dark">Cliente</label>
						<input autocomplete="off" maxlength="100" class="form-control formCatalogo" id="txtCliente" placeholder="Ingrese el nombre del cliente" value="">
					</div>
					<div class="form-group col-xs-12 col-sm-12 col-md-12 text-left">
						<div class="form-check form-check-inline">
							<input class="form-check-input" type="radio" name="tipoCliente" id="tipoCliente1" value="fisica" checked>
							<label class="form-check-label" for="tipoCliente1">Persona física</label>
						</div>
						<div class="form-check form-check-inline">
							<input class="form-check-input" type="radio" name="tipoCliente" id="tipoCliente2" value="moral">
							<label class="form-check-label" for="tipoCliente2">Persona moral</label>
						</div>
					</div>
					<div class="form-group col-xs-12 col-sm-12 col-md-12 text-left">
						<label for="txtNotas" class="label-dark">Notas / observaciones</label>
						<textarea style="resize: none;height:70px;" autocomplete="off" maxlength="250" class="form-control formCatalogo" id="txtNotas" placeholder="Ingrese notas u observaciones para la cotización"></textarea>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group col-xs-12 col-sm-12 col-md-12 text-left">
						<label for="txtDireccion" class="label-dark">Dirección</label>
						<input autocomplete="off" maxlength="100" class="form-control formCatalogo" id="txtDireccion" placeholder="Ingrese la dirección del cliente" value="">
					</div>
					<div class="form-group col-xs-12 col-sm-12 col-md-12 text-left">
						<label class="label-dark">Tipo de cotización</label>
						<br />
						<div class="form-check form-check-inline">
							<input class="form-check-input" type="radio" name="tipoCotizacion" id="tipoCotizacion1" value="renta" checked>
							<label class="form-check-label" for="tipoCotizacion1">Renta</label>
						</div>
						<div class="form-check form-check-inline">
							<input class="form-check-input" type="radio" name="tipoCotizacion" id="tipoCotizacion2" value="venta">
							<label class="form-check-label" for="tipoCotizacion2">Venta</label>
						</div>
					</div>
					<div class="form-group col-xs-12 col-sm-12 col-md-12 text-left">
						<label class="label-dark">Vigencia</label>
						<br />
						<div class="form-check form-check-inline">
							<input class="form-control formCatalogo col-xs-12 col-sm-4 col-md-4" type="text" value="1" maxlength="3" id="txtVigencia">
							<label class="" for="vigencia">&nbsp;&nbsp;día(s).</label>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-12">
				<div id="table" class="table-editable">
					<span class="accionAgregar float-right mb-3 mr-2"><a href="#!" class="text-success"><i class="fas fa-plus fa-2x" aria-hidden="true"></i></a></span>
					<div class="table-responsive">
						<table id="tblProductosCotizacion" class="table table-bordered text-center" style="width:100%;">
						  <thead>
							<tr>
							  <th class="text-center" style="width:25%;">Producto</th>
							  <th class="text-center" style="width:25%;">Notas</th>
							  <th class="text-center" style="width:20%;">Precio ($)</th>
							  <th class="text-center" style="width:8%;">Cant.</th>
							  <th class="text-center" style="width:12%;">Total ($)</th>
							  <th class="text-center" style="width:10%;">Acción</th>
							</tr>
						  </thead>
						  <tbody>
							'.nuevaCotizacion_filaProducto($idAsignar).'
						  </tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
				</div>
				<div class="col-md-6">
					<div class="form-group col-xs-6 col-sm-6 col-md-6 text-left float-right">
						<div class="text-right">
							<label for="subtotal" class="label-dark">Subtotal</label>
						</div>
						<div class="">
							<div class="input-group mb-3">
								<div class="input-group-prepend">
								  <span class="input-group-text">$</span>
								</div>
								<input type="number" style="font-weight:bold;" min="0" class="form-control formCatalogo" id="subtotal" value="0.00" disabled>
							</div>
						</div>
						<div class="text-right">
							<label for="iva" class="label-dark">(+) IVA</label>
						</div>
						<div class="">
							<div class="input-group mb-3">
								<div class="input-group-prepend">
								  <span class="input-group-text">$</span>
								</div>
								<input type="number" style="font-weight:bold;" min="0" class="form-control formCatalogo" id="iva" value="0.00" disabled>
							</div>
						</div>
						<div class="text-right retIva" style="display:none;">
							<label for="iva" class="label-dark">(-) Ret. IVA</label>
						</div>
						<div class="retIva" style="display:none;">
							<div class="input-group mb-3">
								<div class="input-group-prepend">
								  <span class="input-group-text">$</span>
								</div>
								<input type="number" style="font-weight:bold;" min="0" class="form-control formCatalogo" id="retIVA" value="0.00" disabled>
							</div>
						</div>
						<div class="text-right">
							<label for="iva" class="label-dark">Total</label>
						</div>
						<div class="">
							<div class="input-group mb-3">
								<div class="input-group-prepend">
								  <span class="input-group-text">$</span>
								</div>
								<input type="number" style="font-weight:bold;" min="0" class="form-control formCatalogo" id="total" value="0.00" disabled>
							</div>
						</div>
					</div>
				</div>
			</div>
			<hr class="solid">
			
			<div class="row">
				<div class="col-md-6">
					<div class="col-xs-12 col-sm-12 col-md-4 float-right">
						<button type="button" type="button" class="btn btn-default form-control" id="btnVistaPrevia" data-id="btnVistaPrevia" style="background-color: #A569BD; color: white;"><i class="fa fa-file-pdf-o" aria-hidden="true"></i>&nbsp;&nbsp;Vista previa</button>
					</div>
				</div>
				<div class="col-md-6">
					<div class="col-xs-12 col-sm-12 col-md-4 float-left">
						<button type="button" type="button" class="btn btn-default form-control" id="btnSiguiente" data-id="btnSiguiente" style="background-color: #30a6fc; color: white;"><i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp;&nbsp;Siguiente</button>
					</div>
				</div>
			</div>
			
			
			';
			
	return $cont;
}


function nuevaCotizacion_filaProducto($idAsignar)
{
	$cont = '
			<tr>
				<td class="pt-3-half">
					<input class="autocomplete-input" type="text" name="txtEquipo_'.$idAsignar.'" id="txtEquipo_'.$idAsignar.'"/>
				</td>
				<td class="pt-3-half" id="txtObservaciones_'.$idAsignar.'" contenteditable="true" style="word-wrap:break-word;overflow-wrap: break-word;"></td>
				<td class="pt-3-half" id="tdPrecio_'.$idAsignar.'">
					<input autocomplete="off" onkeydown="javascript: return event.keyCode == 69 ? false : true" class="moneyInput inputPrecio" type="number" min="0" name="txtPrecio_'.$idAsignar.'" id="txtPrecio_'.$idAsignar.'" data-idp="'.$idAsignar.'"/>
				</td>
				<td class="pt-3-half">
					<input autocomplete="off" onkeydown="javascript: return event.keyCode == 69 ? false : true" class="moneyInput inputCant" type="number" min="0" name="txtCant_'.$idAsignar.'" id="txtCant_'.$idAsignar.'" data-idp="'.$idAsignar.'" value="1"/>
				</td>
				<td class="pt-3-half">
					<input class="moneyInput inputSub" style="background-color:transparent;" type="number" min="0" name="txtTotal_'.$idAsignar.'" id="txtTotal_'.$idAsignar.'" disabled/>
				</td>
				<td>
				  <div style="margin-top:7px;">
					<span class="table-up"><a href="#!" class="indigo-text"><i class="fas fa-long-arrow-alt-up"
						  aria-hidden="true"></i></a></span>
					<span class="table-down"><a href="#!" class="indigo-text"><i class="fas fa-long-arrow-alt-down"
						  aria-hidden="true"></i></a></span>
					&nbsp;
				    <a href="#" title="Eliminar" class="btn btn-danger btn-circle btn-sm accionEliminar"><i class="fas fa-trash"></i></a>
					<input id="img_'.$idAsignar.'" type="hidden" value="0"/>
				  </div>
				  
				</td>
			</tr>';
			
	return $cont;
}


function nuevaCotizacion_vistaFormEnviar($arrCorreosContacto)
{
	//COMBO DE CORREOS
	$cmbCorreos = '<select id="cmbCorreos" style="" class="form-control">';

	foreach($arrCorreosContacto as $correoEnCurso)
	{
		$cmbCorreos.='<option value="'.$correoEnCurso['correo'].'">'.$correoEnCurso['correo'].'</option>';
	}
	
	$cmbCorreos.='</select>';
	
	
	
	$cont = '
			<div class="row">
				<div class="col-md-3">
					
				</div>
				<div class="col-md-8">
					<div class="form-group col-xs-12 col-sm-12 col-md-7 text-left">
						<div class="">
							<label for="cmbCorreos" class="label-dark">Cuenta</label>
						</div>
						<div class="">
							'.$cmbCorreos.'
						</div>
					</div>
					<div class="form-group col-xs-12 col-sm-12 col-md-7 text-left">
						<div class="">
							<label for="txtDestino" class="label-dark">Correo destino</label>
						</div>
						<div class="">
							<input maxlength="45" class="form-control" id="txtDestino" placeholder="Ingrese el correo del destinatario" value="">
						</div>
					</div>
					<div class="form-group col-xs-12 col-sm-12 col-md-7 text-left">
						<div class="">
							<label for="txtAsunto" class="label-dark">Asunto</label>
						</div>
						<div class="">
							<input maxlength="100" class="form-control" id="txtAsunto" placeholder="Ingrese el asunto del correo" value="">
						</div>
					</div>
					<div class="form-group col-xs-12 col-sm-12 col-md-9 text-left">
						<div class="">
							<label for="txtAreaMensaje" class="label-dark">Mensaje</label>
						</div>
						<div class="">
							<textarea maxlength="500" style="resize:none;height:200px;" class="form-control" id="txtAreaMensaje" placeholder="Ingrese el cuerpo del correo"></textarea>
						</div>
					</div>
				</div>
			</div>
			<hr class="solid">
			
			<div class="row">
				<div class="col-md-6">
					<div class="col-xs-12 col-sm-12 col-md-5 float-right">
						<button type="button" type="button" class="btn btn-default form-control" id="btnEnviarCot" style="background-color: #1cc88a; color: white;"><i class="fa fa-envelope" aria-hidden="true"></i>&nbsp;&nbsp;Enviar Cotización</button>
					</div>
				</div>
				<div class="col-md-6">
					<div class="col-xs-12 col-sm-12 col-md-5 float-left">
						<button type="button" type="button" class="btn btn-default form-control" id="btnGuardarCot" style="background-color: #30a6fc; color: white;"><i class="far fa-save" aria-hidden="true"></i>&nbsp;&nbsp;Guardar Cotización</button>
					</div>
				</div>
			</div>
			
			
			
			';
			
	return $cont;
}

?>