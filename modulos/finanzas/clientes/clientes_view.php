<?php

ini_set("display_errors",1);
error_reporting(E_ALL);

function clientes_inicio()
{
	//INCLUIMOS EL ARCHIVO JS
	$resultado= '<script src="modulos/finanzas/clientes/clientes.js?'.time().'"></script>';
	
	$resultado .= '<style>';
	$resultado .= '.leyendas_informativas {
		float: left;
		margin-top: 16px;
	}

	.leyendas_informativas ul {
		padding-left: 0px;
	}

	.leyendas_informativas ul li {
		display: block;
	}

	@media only screen and (min-width: 600px) {
		.leyendas_informativas ul li {
			float: left;
			display: block;
		}
	}

	.leyendas_informativas ul span.leyBox {
		width: 10px;
		height: 10px;
		display: block;
		float: left;
		margin-top: 4px;
	}

	.leyendas_informativas ul span.redBox {
		background-color: #ff0000;
	}
	
	.trRed {
		background-color: #ff0000 !important;
		color:white;
		font-weight:bold;
	}

	.leyendas_informativas ul span.greenBox {
		background-color: #008000;
	}
	
	.trGreen {
		background-color: #008000 !important;
		color:white;
		font-weight:bold;
	}

	.leyendas_informativas ul span.yellowBox {
		background-color: #ffc001;
	}
	
	.trYellow {
		background-color: #ffc001 !important;
		color:white;
		font-weight:bold;
	}

	.leyendas_informativas .mleft {
		margin-left: 5px;
	}

	.leyendas_informativas ul span.leyenda {
		color: #000000;
	}
	
	.noFloat{
		float:none !important;
	}
	';
	$resultado .= '</style>';

	$resultado.= '<div class="card shadow mb-4" id="divView_clientes_inicio">
					<div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
					  <h5 class="m-0 font-weight-bold text-primary">Concentrado de Clientes</h5>
					  <button type="button" class="btn btn-default" id="btnNuevoCliente" style="background-color: #30a6fc; color: white;"><i class="fa fa-plus" aria-hidden="true"></i>  Nuevo</button>
						
					</div>
					<div class="card-body" id="div_clientes_inicio_tablaClientes">
						
					</div>
				  </div>
				  <div class="card shadow mb-5" style="display:none;" id="divView_clientes_editar">
					<div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
					  <h5 class="m-0 font-weight-bold text-primary" id="viewFormulario">Nuevo Cliente</h5>
					  <button type="button" class="btn btn-default" id="btnRegresarNuevo" style="background-color: #de6947; color: white;"><i class="fa fa-arrow-left" aria-hidden="true"></i>  Regresar</button>
					</div>
					<div class="card-body" id="div_clientes_editar_form">
						
					</div>
				  </div>
				  <div class="card shadow mb-5" style="display:none;" id="divView_clientes_documentos">
					<div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
					  <h5 class="m-0 font-weight-bold text-primary" id="viewDocumentos">Estatus Cliente</h5>
					  <button type="button" class="btn btn-default" id="btnRegresarEstatus" style="background-color: #de6947; color: white;"><i class="fa fa-arrow-left" aria-hidden="true"></i>  Regresar</button>
					</div>
					<div class="card-body" id="div_clientes_estatus_form">
						
					</div>
				  </div>
				  ';
	
	return $resultado;
	
}

function clientes_tablaClientes($arrClientes)
{
	$leyendas = array(
		0 => array('label' => 'Sin pendientes', 'color' => 'greenBox', 'weight'=>''),
		1 => array('label' => 'Algunos pendientes', 'color' => 'yellowBox', 'weight'=>''),
		2 => array('label' => 'Muchos pendientes', 'color' => 'redBox', 'weight'=>'')
	);
	
	$leyendasInf = '';
	$leyendasInf .= '<ul>';
	$numOpciones = count($leyendas);

	for ($i = 0; $i < $numOpciones; $i++) {
		$class = 'leyBox ';
		$class .= $leyendas[$i]['color'];
		if ($i > 0) {
			$class .= ' mleft';
		}
		$leyendasInf .= '<li><span class="'.$class.'"></span><span class="leyenda mleft">'.$leyendas[$i]['weight'].'</span><span class="leyenda mleft">'.$leyendas[$i]['label'].'</span></li>';
	}
	$leyendasInf .= '</ul>';
	
	$resultado = '<div class="leyendas_informativas">' . $leyendasInf . '</div><br/><br/><br/>
					<div class="table-responsive bordeada">
						<table class="table table-bordered table-responsive-md table-striped text-center" id="tblClientes" style="width=100%" cellspacing="0">
							<thead>
								<tr>
								  <th>Cliente</th>
								  <th>Razón Social</th>
								  <th>RFC</th>
								  <th>Tipo</th>
								  <th>Acciones</th>
								</tr>
							</thead>
							<tbody>';

	if(sizeof($arrClientes)>0)
	{
		foreach($arrClientes as $clienteEnCurso)
		{
			$styleTr = '';
			if($clienteEnCurso['porcPendientes'] == 100)
			{
				$styleTr = 'trGreen';
			}
			elseif($clienteEnCurso['porcPendientes'] < 100 && $clienteEnCurso['porcPendientes'] >= 50)
			{
				$styleTr = 'trYellow';
			}
			elseif($clienteEnCurso['porcPendientes'] < 50)
			{
				$styleTr = 'trRed';
			}
			
			
			$btnVer ='<a href="#" title="Ver" style="margin-left:5px;" class="btn btn-circle btn-primary btn-sm accionVer" data-id="'.$clienteEnCurso['id'].'"><i class="far fa-eye"></i></a>';
			$btnEditar ='<a href="#" title="Editar" style="margin-left:5px;" class="btn btn-circle btn-info btn-sm accionEditar" data-id="'.$clienteEnCurso['id'].'"><i class="fas fa-edit"></i></a>';
			$btnEstatus ='<a href="#" title="Estatus" style="margin-left:5px;" class="btn btn-circle btn-warning btn-sm accionEstatus" data-id="'.$clienteEnCurso['id'].'" data-nombre="'.$clienteEnCurso['cliente'].'" data-persona="'.$clienteEnCurso['tipo'].'"><i class="fas fa-tasks"></i></a>';
			$btnEliminar ='<a href="#" title="Eliminar" style="margin-left:5px;" class="btn btn-danger btn-circle btn-sm accionEliminar" data-nombre="'.$clienteEnCurso['cliente'].'" data-id="'.$clienteEnCurso['id'].'"><i class="fas fa-trash"></i></a>';
			
			
			$resultado.='<tr class="'.$styleTr.'">
							<td>'.$clienteEnCurso['cliente'].'</td>
							<td>'.$clienteEnCurso['razonSocial'].'</td>
							<td>'.$clienteEnCurso['rfc'].'</td>
							<td>'.$clienteEnCurso['tipo'].'</td>
							<td>'.$btnVer.$btnEditar.$btnEstatus.$btnEliminar.'</td>
						</tr>';
		}
	}
	else
	{
		$resultado.='<tr>
						<td></td>
						<td>No se encontraron clientes.</td>
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

function clientes_vistaEditar($arrInfoCliente,$arrTelCliente,$arrTiposTelefono,$tipoAccion)
{
	$nombreCliente = '';
	if(isset($arrInfoCliente['cliente']))
	{
		$nombreCliente = $arrInfoCliente['cliente'];
	}
	
	$razonSocial = '';
	if(isset($arrInfoCliente['razonSocial']))
	{
		$razonSocial = $arrInfoCliente['razonSocial'];
	}

	$rfc = '';
	if(isset($arrInfoCliente['rfc']))
	{
		$rfc = $arrInfoCliente['rfc'];
	}
	
	$checkedFisica = 'checked';
	$checkedMoral = '';
	if(isset($arrInfoCliente['persona']))
	{
		if($arrInfoCliente['persona'] == 'MORAL')
		{
			$checkedFisica = '';
			$checkedMoral = 'checked';
		}
	}
	
	$direccion = '';
	if(isset($arrInfoCliente['direccion']))
	{
		$direccion = $arrInfoCliente['direccion'];
	}
	
	$colonia = '';
	if(isset($arrInfoCliente['colonia']))
	{
		$colonia = $arrInfoCliente['colonia'];
	}
	
	$dirFiscal = '';
	if(isset($arrInfoCliente['dirFiscal']))
	{
		$dirFiscal = $arrInfoCliente['dirFiscal'];
	}
	
	$correo = '';
	if(isset($arrInfoCliente['correo']))
	{
		$correo = $arrInfoCliente['correo'];
	}
	
	$varTelefonos = '';
	if(sizeof($arrTelCliente)>0)
	{
		// echo "<pre>";
		// print_r($arrTelCliente);
		// die();

		foreach($arrTelCliente as $telClienteEnCurso)
		{
			$varTelefonos.= cliente_filaTelefono($arrTiposTelefono,$tipoAccion,$telClienteEnCurso);
		}
	}
	else
	{
		$varTelefonos = '
						<tr class="emptyRow">
							<td class="pt-3-half">
							</td>
							<td class="pt-3-half">
								Sin teléfonos registrados.
							</td>
							<td>
							</td>
						</tr>';
	}

	$cont = '
			<div class="row">
				<div class="col-md-6">
					<div class="form-group col-xs-12 col-sm-12 col-md-12 text-left">
						<label for="txtNombreCliente" class="label-dark">Nombre (*)</label>
						<input maxlength="100" class="form-control formClientes" id="txtNombreCliente" value="'.$nombreCliente.'" placeholder="Ingrese el nombre del cliente">
					</div>
					<div class="form-group col-xs-12 col-sm-12 col-md-12 text-left">
						<label for="txtRSCliente" class="label-dark">Razón Social</label>
						<input maxlength="100" class="form-control formClientes" id="txtRSCliente" value="'.$razonSocial.'" placeholder="Ingrese la razón social del cliente">
					</div>
					<div class="form-group col-xs-12 col-sm-12 col-md-12 text-left">
						<label for="txtRFCCliente" class="label-dark">RFC</label>
						<input maxlength="100" class="form-control formClientes" id="txtRFCCliente" value="'.$rfc.'" placeholder="Ingrese el RFC del cliente">
					</div>
					<div class="form-group col-xs-12 col-sm-12 col-md-12 text-left">
						<div class="form-check form-check-inline">
							<input class="form-check-input formClientes" type="radio" name="tipoCliente" id="tipoCliente1" value="FÍSICA" '.$checkedFisica.'>
							<label class="form-check-label" for="tipoCliente1">Persona física</label>
						</div>
						<div class="form-check form-check-inline">
							<input class="form-check-input formClientes" type="radio" name="tipoCliente" id="tipoCliente2" value="MORAL" '.$checkedMoral.'>
							<label class="form-check-label" for="tipoCliente2">Persona moral</label>
						</div>
					</div>
					<div class="form-group col-xs-12 col-sm-12 col-md-12 text-left">
						<div id="divTablaTel" class="table-editable">
							'.($tipoAccion=='ver'?'':'<span class="accionAgregarTel float-right mb-3 mr-2"><a href="#!" class="text-success"><i class="fas fa-plus fa-2x" aria-hidden="true"></i></a></span>').'
							<div class="table-responsive">
								<table id="tblTelefonoCliente" class="table table-bordered text-center" style="width:100%;">
									<thead>
										<tr>
											<th class="text-center" style="width:25%;">Tipo</th>
											<th class="text-center" style="width:50%;">Teléfono</th>
											<th class="text-center" style="width:25%;">Acción</th>
										</tr>
									</thead>
									<tbody>
										'.$varTelefonos.'
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group col-xs-12 col-sm-12 col-md-12 text-left">
						<label for="txtDireccionCliente" class="label-dark">Dirección (*)</label>
						<input maxlength="100" class="form-control formClientes" id="txtDireccionCliente" value="'.$direccion.'" placeholder="Ingrese la dirección del cliente">
					</div>
					<div class="form-group col-xs-12 col-sm-12 col-md-12 text-left">
						<label for="txtColoniaCliente" class="label-dark">Colonia (*)</label>
						<input maxlength="100" class="form-control formClientes" id="txtColoniaCliente" value="'.$colonia.'" placeholder="Ingrese la colonia del cliente">
					</div>
					<div class="form-group col-xs-12 col-sm-12 col-md-12 text-left">
						<label for="txtDomFiscalCliente" class="label-dark">Domicilio Fiscal</label>
						<input maxlength="100" class="form-control formClientes" id="txtDomFiscalCliente" value="'.$dirFiscal.'" placeholder="Ingrese el domicilio fiscal del cliente">
					</div>
					<div class="form-group col-xs-12 col-sm-12 col-md-12 text-left">
						<label for="txtCorreoCliente" class="label-dark">Correo (*)</label>
						<input maxlength="100" class="form-control formClientes" id="txtCorreoCliente" value="'.$correo.'" placeholder="Ingrese el correo del cliente">
					</div>
				</div>
			</div>';

	if($tipoAccion <> 'ver')
	{
		$cont.= '
				<div class="col-md-12">
					<hr class="solid">
					<div class="d-flex justify-content-center">
						<button type="button" type="button" class="btn btn-default" id="btnGuardarCliente" data-id="" data-accion="" style="background-color: #1cc88a; color: white;"><i class="far fa-save" aria-hidden="true"></i>  Guardar cliente</button>
					</div>
				</div>';
	}
			
	return $cont;
}

function clientes_vistaEstatus($arrListDocumentos,$id)
{	
	$cont = '';
	
	// echo "<pre>";
	// print_r($arrListDocumentos);
	// die();

	foreach($arrListDocumentos as $docEnCurso)
	{
		
		$adjuntoEnCurso = 'SIN ADJUNTO(S)';
		$isAdj = 0;

		/*
		if(isset($docEnCurso['ruta']))
		{
			if($docEnCurso['ruta'] != '')
			{
				$adjuntoEnCurso = ' <a href="'.$docEnCurso['ruta'].'" download>
										'.$docEnCurso['nombre'].'
									</a>
									&nbsp;&nbsp;
									<button id="btnEliminarAdjunto_'.$docEnCurso['idDoc'].'_'.$docEnCurso['idCE'].'" type="button" class="close" aria-label="Close" title="Eliminar adjunto">
										<span aria-hidden="true">&times;</span>
									</button>';
				$isAdj = 1;
			}
		}
		*/

		if(sizeof($docEnCurso['docsCliente']) > 0)
		{
			
			$adjuntoEnCurso = '';
			$adjuntoEnCurso.= '<div class="row">';
			foreach($docEnCurso['docsCliente'] as $docClienteEnCurso)
			{
				$adjuntoEnCurso.= ' <div class="col-md-12">
										<a href="'.$docClienteEnCurso['ruta'].'" download>
											'.$docClienteEnCurso['nombre'].'
										</a>
										&nbsp;&nbsp;
										<button id="btnEliminarAdjunto_'.$docEnCurso['idDoc'].'_'.$docClienteEnCurso['idCE'].'" data-idce="'.$docClienteEnCurso['idCE'].'" data-nombre="'.$docClienteEnCurso['nombre'].'" type="button" class="close noFloat delUploadDoc" aria-label="Close" title="Eliminar adjunto">
											<span aria-hidden="true">&times;</span>
										</button>
									</div>';
			}
			$adjuntoEnCurso.= '</div>';
			$isAdj = 1;
		}
		
		$cont.= '<div class="form-group col-xs-12 col-sm-12 col-md-12 text-left">
					<div class="">
						<label for="" class="label-dark">'.$docEnCurso['descDoc'].'</label>
					</div>
					<div class="">';
			
		$divAdjunto = ' <div id="lblAdjuntoDocumento_'.$docEnCurso['idDoc'].'" data-isadj="'.$isAdj.'" class="">
							<div class="input-group-append">
								'.$adjuntoEnCurso.'
							</div>
						</div>
						<div id="divAdjNuevo_'.$docEnCurso['idDoc'].'"></div>';
			
		$cont.='
						<div class="input-group mb-3 px-2 py-2 rounded-pill bg-white shadow-sm">
							
							<input id="uploadDocumentoAdjunto_'.$docEnCurso['idDoc'].'" data-iddoc="'.$docEnCurso['idDoc'].'" data-nombre="'.time().rand(0,100).'_'.$id.'_'.$docEnCurso['idDoc'].'" type="file" multiple class="form-control border-0 upload uploadDocumento">
							
							<label for="uploadDocumentoAdjunto_'.$docEnCurso['idDoc'].'" class="font-weight-light text-muted upload-label">Elegir archivo(s)</label>
							<div class="input-group-append">
								<label for="uploadDocumentoAdjunto_'.$docEnCurso['idDoc'].'" class="btn btn-light m-0 rounded-pill px-4"> <i class="fa fa-cloud-upload mr-2 text-muted"></i><small class="text-uppercase font-weight-bold text-muted formCatalogo">Elegir archivo(s)</small></label>
							</div>
						</div>
						'.$divAdjunto.'
						<!--
						<br />
						<div class="d-flex justify-content-center">
							<button type="button" type="button" class="btn btn-default btn-danger delUploadDoc" data-iselimina="0" data-iddoc="'.$docEnCurso['idDoc'].'" id="btnEliminarAdjunto_'.$docEnCurso['idDoc'].'"><i class="fas fa-trash" aria-hidden="true"></i>  Quitar adjunto</button>
						</div>
						-->
					</div>
				</div>
				<br/>';
	}
	
	$cont.='<br />	
			<div class="col-md-12">
				<hr class="solid">
				<div class="d-flex justify-content-center">
					<button type="button" type="button" class="btn btn-default" id="btnGuardarAdjuntos" data-id="'.$id.'" data-accion="" style="background-color: #1cc88a; color: white;"><i class="far fa-save" aria-hidden="true"></i>  Guardar adjuntos</button>
				</div
			</div>';
			
	return $cont;
}

function cliente_filaTelefono($arrTipos,$tipoAccion,$telCliente = array())
{
	$disabled = '';
	if($tipoAccion == 'ver')
	{
		$disabled = 'disabled';
	}

	$selected = '';

	$cmbTipos = '<select class="form-control" '.$disabled.'>';
	foreach($arrTipos as $tipoEnCurso)
	{
		if(isset($telCliente['id_telefonotipo']))
		{
			if($tipoEnCurso['id'] == $telCliente['id_telefonotipo'])
			{
				$selected = 'selected';
			}
		}

		$cmbTipos.='<option id="'.$tipoEnCurso['id'].'" '.$selected.'>'.$tipoEnCurso['tipo'].'</option>';

		$selected = '';
	}					
	$cmbTipos.= '</select>';

	$value = '';
	if(isset($telCliente['telefono_numero']))
	{
		if($telCliente['telefono_numero'] <> '')
		{
			$value = $telCliente['telefono_numero'];
		}
	}
	$cont = '
			<tr>
				<td class="pt-3-half">
					'.$cmbTipos.'
				</td>
				<td class="pt-3-half">
					<input autocomplete="off" onkeydown="javascript: return event.keyCode == 69 ? false : true" value="'.$value.'" class="moneyInput inputPrecio" '.$disabled.' type="text"/>
				</td>
				<td>
				  <div style="margin-top:7px;">
				    '.($tipoAccion == 'ver'?'':'<a href="#" title="Eliminar" class="btn btn-danger btn-circle btn-sm accionEliminarTel"><i class="fas fa-trash"></i></a>').'
				  </div>
				  
				</td>
			</tr>';
			
	return $cont;
}

?>