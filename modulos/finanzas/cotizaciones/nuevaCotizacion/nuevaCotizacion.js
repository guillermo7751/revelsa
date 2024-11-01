var modulo = 'modulos/finanzas/cotizaciones/nuevaCotizacion/nuevaCotizacion_cont.php';
var acronimo = 'nuevaCotizacion';
var tipoCliente = 'fisica';

var idCotEnCurso = 1;

$(document).ready(function(){

	js_nuevaCotizacion_cargaForm();
	
	$("body").on("change","input[name='tipoCliente']", function () {
		
		if($('#tipoCliente2').is(':checked'))
		{
			$(".retIva").show();
		}
		else
		{
			$(".retIva").hide();
			$("#retIVA").val(0);
		}
		
		if(parseFloat($('#subtotal').val()) > 0)
		{
			js_nuevaCotizacion_generaCalculosTotales();
		}
		
		tipoCliente = $("input[name='tipoCliente']:checked").val();
	});
	
	$('body').on('click', '.accionAgregar', function() {
        js_nuevaCotizacion_agregaProducto();
    });
	
	$('body').on('click', '.accionEliminar', function() {
        
		//VALIDO EL NÚMERO DE FILAS, DEBE HABER AL MENOS UNA EN EL MÓDULO.
		var count = 0;
		$( ".accionEliminar" ).each(function() {
            count++;
        });
		
		if(count>1)
		{
			$(this).parents('tr').detach();
			js_nuevaCotizacion_generaCalculosTotales();	
		}
    });
	
	$('body').on('click', '.table-up', function() {
        
		const $row = $(this).parents('tr');

		if ($row.index() === 0) {
		  return;
		}
	 
		$row.prev().before($row.get(0));
		
    });
	
	$('body').on('click', '.table-down', function() {
        
		const $row = $(this).parents('tr');
		$row.next().after($row.get(0));
		
    });
	
	//EVENTOS DE LA TABLA
	$('body').on('change','.inputPrecio', function() {
		js_nuevaCotizacion_generaCalculosTabla($(this).data('idp'));
    });
	
	$('body').on('keyup','.inputPrecio', function() {
		js_nuevaCotizacion_generaCalculosTabla($(this).data('idp'));
    });
	
	$('body').on('change','.inputCant', function() {
		js_nuevaCotizacion_generaCalculosTabla($(this).data('idp'));
    });
	
	$('body').on('keyup','.inputCant', function() {
		js_nuevaCotizacion_generaCalculosTabla($(this).data('idp'));
    });
	
	$('body').on('click','#btnVistaPrevia', function() {
		js_nuevaCotizacion_accionCotizacion('vistaPrevia');
    });
	
	$('body').on('click','#btnSiguiente', function() {
		js_nuevaCotizacion_accionCotizacion('formularioEnvio');
    });
	
	$('body').on('click', '#btnRegresarInicio', function() {
        js_cambiaViewstack(acronimo,'enviar','inicio');
    });
	
	$('body').on('blur', '#txtDestino', function() {
        $(this).val(js_remueveEspacios($(this).val()));
    });
	
	$('body').on('click','#btnEnviarCot', function() {
		js_nuevaCotizacion_accionCotizacion('enviar');
    });
	
	$('body').on('click','#btnGuardarCot', function() {
		js_nuevaCotizacion_accionCotizacion('guardar');
    });
	
});


function js_nuevaCotizacion_cargaForm()
{
	try
    {
		var formData = new FormData();
            
        formData.append('idEnCurso',idCotEnCurso);
		
        js_llamadaAjax(modulo,'ajax_nuevaCotizacion_cargaForm',formData,'js_nuevaCotizacion_resultadoCargaForm');
    }
    catch(e)
    {
        js_generaModal('divError','Error','Error al ejecutar el proceso: '+e,'error');
    }
}

function js_nuevaCotizacion_resultadoCargaForm(data)
{
	try
    {
        var respuesta = jQuery.parseJSON(data);
        
        if(respuesta.Error)
        {
            js_generaModal('divError','Error','Error al ejecutar el proceso: '+respuesta.ErrorMensaje,'alerta');
        }
        else
        {
            $('#div_nuevaCotizacion_inicio_form').html(respuesta.resultado);
			
			var idCot = idCotEnCurso;
			$('#txtEquipo_'+idCotEnCurso).autocomplete({
				lookup: respuesta.arrProductos,
				onSelect: function (suggestion) {
					js_nuevaCotizacion_dameInfoProducto(idCot,suggestion.data);
					
					$('#img_'+idCot).val(suggestion.data.idProducto);
					
					if(suggestion.data.isDefaultDesc == 1)
					{
						$('#txtObservaciones_'+idCot).html(suggestion.data.descProducto);
					}
				},
				triggerSelectOnValidInput: false
			});
			
			idCotEnCurso++;
        }
    }
    catch(e)
    {
        js_generaModal('divError','Error','Error al ejecutar el proceso: '+e,'error');
    } 
}


function js_nuevaCotizacion_agregaProducto()
{
	try
    {
		var formData = new FormData();
            
        formData.append('idEnCurso',idCotEnCurso);
		
        js_llamadaAjax(modulo,'ajax_nuevaCotizacion_agregaProducto',formData,'js_nuevaCotizacion_resultadoAgregaProducto');
    }
    catch(e)
    {
        js_generaModal('divError','Error','Error al ejecutar el proceso: '+e,'error');
    }
}

function js_nuevaCotizacion_resultadoAgregaProducto(data)
{
	try
    {
        var respuesta = jQuery.parseJSON(data);
        
        if(respuesta.Error)
        {
            js_generaModal('divError','Error','Error al ejecutar el proceso: '+respuesta.ErrorMensaje,'alerta');
        }
        else
        {
            $('#tblProductosCotizacion > tbody:last-child').append(respuesta.resultado);
			
			var idCot = idCotEnCurso;
			$('#txtEquipo_'+idCotEnCurso).autocomplete({
				lookup: respuesta.arrProductos,
				onSelect: function (suggestion) {
					//alert('You selected: ' + suggestion.value + ', ' + suggestion.data.idProducto);
					
					js_nuevaCotizacion_dameInfoProducto(idCot,suggestion.data);
					
					$('#img_'+idCot).val(suggestion.data.idProducto);
					
					if(suggestion.data.isDefaultDesc == 1)
					{
						$('#txtObservaciones_'+idCot).html(suggestion.data.descProducto);
					}
				},
				triggerSelectOnValidInput: false
			});
			
			idCotEnCurso++;
        }
    }
    catch(e)
    {
        js_generaModal('divError','Error','Error al ejecutar el proceso: '+e,'error');
    } 
}


function js_nuevaCotizacion_dameInfoProducto(idCot,data)
{
	
	js_limpiaValoresFila(idCot);
	
	//INPUT PRECIO SIN VALOR PREDEFINIDO
	var inputPrecioSV = '<input autocomplete="off" class="moneyInput inputPrecio" type="number" min="0" name="txtPrecio_'+idCot+'" id="txtPrecio_'+idCot+'" data-idp="'+idCot+'"/>';
	
	var tipoCotizacion = $('input[name ="tipoCotizacion"]:checked').val();

	if(tipoCotizacion == 'venta')
	{
		//console.log(data);
		$('#tdPrecio_'+idCot).html(inputPrecioSV);
		
		$('#txtPrecio_'+idCot).val(data.precioVentaProd);
		
		js_nuevaCotizacion_generaCalculosTabla(idCot);
	
	}
	else
	{
		if(data.precioRentaProd != '')
		{
			var htmlListRenta = '<input autocomplete="off" type="number" class="moneyInput inputPrecio" list="dtlPrecioRenta_'+idCot+'" name="txtPrecio_'+idCot+'" id="txtPrecio_'+idCot+'" data-idp="'+idCot+'">'+
								'<datalist id="dtlPrecioRenta_'+idCot+'">';
			
			var preciosRenta = data.precioRentaProd.split(',');
			
			$.each(preciosRenta, function( index, value ) {
				var precioRentaEnCurso = value.split('|');
				
				htmlListRenta+='<option value="'+precioRentaEnCurso[1]+'">'+precioRentaEnCurso[0]+'</option>';
				
			});
			
			htmlListRenta+='</datalist>';
			
			$('#tdPrecio_'+idCot).html(htmlListRenta);
		}
		else
		{
			$('#tdPrecio_'+idCot).html(inputPrecioSV);
		}
	}
	
	
}

function js_nuevaCotizacion_generaCalculosTabla(id)
{	
	var monto = $('#txtPrecio_'+id).val();	
	var cant = $('#txtCant_'+id).val();
	
	var total = 0;
	if((cant!='' && !isNaN(cant)) && (monto !='' && !isNaN(monto)))
	{
		total = parseFloat(monto)*parseFloat(cant);
	}
	
	$('#txtTotal_'+id).val(total.toFixed(1)+'0');
	
	js_nuevaCotizacion_generaCalculosTotales();
	
}

function js_nuevaCotizacion_generaCalculosTotales()
{
	setTimeout(function(){
	
		var subtotal = 0;
		$( ".inputSub" ).each(function() {
			if(!isNaN($(this).val()) && $(this).val() != '')
			{
				subtotal += parseFloat($(this).val());
			}
			else
			{
				subtotal+=0;
			}
        });
		
		subtotal = parseFloat(subtotal);
		
		$('#subtotal').val(subtotal.toFixed(1)+'0');
		
		var iva = subtotal*(0.16);
		iva = iva.toFixed(1);
		
		$('#iva').val(iva+'0');
		
		var retIVA = 0;

		if(tipoCliente == 'moral')
		{
			retIVA = subtotal*(0.1067);
			retIVA = retIVA.toFixed(1);
	  
			$('#retIVA').val(retIVA+'0');
		}
  
		var total = parseFloat(subtotal)+parseFloat(iva)-parseFloat(retIVA);
		total = parseFloat(total);
		total = total.toFixed(1);
		$('#total').val(total+'0');
		
						 
	},300);
}

function js_nuevaCotizacion_accionCotizacion(accion)
{
	var alerta = '';
	
	var cliente = $('#txtCliente').val();
	if(cliente == '')
	{
		alerta = 'Favor de capturar el nombre del cliente.';
	}
	
	var idUsuario = $('#hid').val();
	var direccion = $('#txtDireccion').val();
	var observaciones = $('#txtNotas').val();
	var vigencia = $('#txtVigencia').val();
	var tipoCotizacion = $('input[name ="tipoCotizacion"]:checked').val();
	var subtotal = $('#subtotal').val();
	var iva = $('#iva').val();
	var retIVA = $('#retIVA').val();
	var total = $('#total').val();
	
	var $rows = $('#tblProductosCotizacion').find('tr');
	var headers = [];
	var dataTabla = [];
	var errorTabla = false;
	
	// A few jQuery helpers for exporting only
	jQuery.fn.pop = [].pop;
	jQuery.fn.shift = [].shift;

	// Get the headers (add special header logic here)
	$($rows.shift()).find('th:not(:empty)').each(function () {
	  headers.push($(this).text().toLowerCase());
	});
	
	// Turn all existing rows into a loopable array
	$rows.each(function () {
	  var $td = $(this).find('td');
	  var h = {};
	  
	  // Use the headers from earlier to name our hash keys
	  headers.forEach(function (header, i) {
		
		var enc = '';
		var txt = '';
		
		switch(header)
		{
			case 'producto':
				enc = 'producto';
				txt = $td.eq(i).find('input').val();
				
				if(txt == '')
				{
					errorTabla = true;
				}
			break;
		
			case 'notas':
				enc = 'notas';
				txt = $td.eq(i).text();  
			break;
		
			case 'precio ($)':
				enc = 'precio';
				txt = $td.eq(i).find('input').val();
				
				if(txt == '')
				{
					errorTabla = true;
				}
			break;
		
			case 'cant.':
				enc = 'cantidad';
				txt = $td.eq(i).find('input').val();
				
				if(txt == '')
				{
					errorTabla = true;
				}
			break;
		
			case 'total ($)':
				enc = 'total';
				txt = $td.eq(i).find('input').val();
			break;
		
			case 'acción':
				enc = 'img';
				txt = $td.eq(i).find('input').val();
			break;
		}
		
		h[enc] = txt;   
	  });
	  
	  dataTabla.push(h);
	});
	
	// Output the result
	//$EXPORT.text(JSON.stringify(data));
	
	if(errorTabla)
	{
		alerta = 'Favor de validar los datos de la tabla de cotización.';
	}
	
	var infoTabla = JSON.stringify(dataTabla);
	
	if(alerta == '')
	{
		if(accion == 'vistaPrevia')
		{
			var data = {};
			
			data.urlReporte = 'finanzas/cotizaciones/nuevaCotizacion/rptCotizacion.php';
			data.cliente = cliente;
			data.direccion = direccion;
			data.tipoCliente = tipoCliente;
			data.observaciones = observaciones;
			data.vigencia = vigencia;
			data.tipoCotizacion = tipoCotizacion;
			data.infoTabla = infoTabla;
			data.subtotal = subtotal;
			data.iva = iva;
			data.retIVA = retIVA;
			data.total = total;
			data.idUsuario = idUsuario;
			
			js_formExterno(data,'modulos/reportes.php');
		}
		else if(accion == 'formularioEnvio')
		{
			js_cambiaViewstack(acronimo,'inicio','enviar');
			js_nuevaCotizacion_cargaFormEnvio();
		}
		else if(accion == 'enviar' || accion == 'guardar')
		{
			try
			{
				var cuenta = $("#cmbCorreos option:selected").val();
				var correo = $('#txtDestino').val();
				var asunto = $('#txtAsunto').val();
				var mensaje = $('#txtAreaMensaje').val();
				
				var errorAccion = '';
				
				if(accion == 'enviar')
				{
					var isCorreoValido = js_validaEmail(correo);
					
					if(correo == '' || !isCorreoValido)
					{
						errorAccion = 'Favor de ingresar un correo de destino válido.';
					}
					
					if(asunto == '')
					{
						errorAccion = 'Favor de ingresar un asunto para el correo.';
					}
					
					if(mensaje == '')
					{
						errorAccion = 'Favor de ingresar un mensaje para el correo.';
					}

				}
				
				if(errorAccion == '')
				{
					var formData = new FormData();
					
					formData.append('cliente',cliente);
					formData.append('direccion',direccion);
					formData.append('tipoCliente',tipoCliente);
					formData.append('observaciones',observaciones);
					formData.append('vigencia',vigencia);
					formData.append('tipoCotizacion',tipoCotizacion);
					formData.append('infoTabla',infoTabla);
					formData.append('subtotal',subtotal);
					formData.append('iva',iva);
					formData.append('retIVA',retIVA);
					formData.append('total',total);
					formData.append('idUsuario',idUsuario);
					formData.append('cuenta',cuenta);
					formData.append('correo',correo);
					formData.append('asunto',asunto);
					formData.append('mensaje',mensaje);
					formData.append('accion',accion);
					
				
					js_llamadaAjax(modulo,'ajax_nuevaCotizacion_accionCotizacion',formData,'ajax_nuevaCotizacion_resultadoAccionCotizacion');
				}
				else
				{
					js_generaModal('divError','Error',errorAccion,'alerta');
				}
				
			}
			catch(e)
			{
				js_generaModal('divError','Error','Error al ejecutar el proceso: '+e,'error');
			}
		}
	}
	else
	{
		js_generaModal('divError','Error',alerta,'alerta');
	}
}

function ajax_nuevaCotizacion_resultadoAccionCotizacion(data)
{
	try
    {
        var respuesta = jQuery.parseJSON(data);
        
        if(respuesta.Error)
        {
            js_generaModal('divError','Error',respuesta.ErrorMensaje,'alerta');
        }
        else
        {
			window.open(window.location.href.split('#')[0]+"/archivos/cotizaciones/"+respuesta.PDF, "_blank");
			
			if(respuesta.Accion == 'enviar')
			{	
				js_generaModal('divExito','Proceso correcto','El correo se envió satisfactoriamente al destinatario.','exito','js_callBackExito');
			}
			else
			{
				js_generaModal('divExito','Proceso correcto','La cotización se guardó correctamente.','exito','js_callBackExito');
			}
			
			
			//js_cambiaViewstack(acronimo,'enviar','inicio');
			//js_nuevaCotizacion_cargaForm();
        }
    }
    catch(e)
    {
        js_generaModal('divError','Error','Error al ejecutar el proceso: '+e,'error');
    } 
}

function js_callBackExito()
{
	js_cambiaViewstack(acronimo,'enviar','inicio');
	js_nuevaCotizacion_cargaForm();
}

function js_nuevaCotizacion_cargaFormEnvio()
{
	try
    {
		var formData = new FormData();
		
        js_llamadaAjax(modulo,'ajax_nuevaCotizacion_cargaFormEnvio',formData,'js_nuevaCotizacion_resultadoCargaFormEnvio');
    }
    catch(e)
    {
        js_generaModal('divError','Error','Error al ejecutar el proceso: '+e,'error');
    }
}

function js_nuevaCotizacion_resultadoCargaFormEnvio(data)
{
	try
    {
        var respuesta = jQuery.parseJSON(data);
        
        if(respuesta.Error)
        {
            js_generaModal('divError','Error','Error al ejecutar el proceso: '+respuesta.ErrorMensaje,'alerta');
        }
        else
        {
            $('#div_nuevaCotizacion_enviar_form').html(respuesta.resultado);
        }
    }
    catch(e)
    {
        js_generaModal('divError','Error','Error al ejecutar el proceso: '+e,'error');
    } 	
}

function js_limpiaValoresFila(id)
{
	$('#txtCant_'+id).val(1);
	$('#txtTotal_'+id).val('');
	$('#txtObservaciones_'+id).text('');
}

