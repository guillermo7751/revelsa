var modulo = 'modulos/finanzas/clientes/clientes_cont.php';
var acronimo = 'clientes';


$(document).ready(function(){

	js_clientes_cargaClientes();
	
	$('body').on('click', '#btnNuevoCliente', function() {
        $('#viewFormulario').text('Nuevo Cliente');
        
        js_cambiaViewstack(acronimo,'inicio','editar');
        js_clientes_cargaVistaEditar('nuevo','');
		
    });
	
	$('body').on('click', '#btnRegresarNuevo', function() {
        
        js_cambiaViewstack(acronimo,'editar','inicio');
    });
	
	$('body').on('click', '#btnRegresarEstatus', function() {
        js_cambiaViewstack(acronimo,'documentos','inicio');
        js_clientes_cargaClientes();
    });
	
	$('body').on('click', '#btnGuardarCliente', function() {
        event.preventDefault();
        js_clientes_guardaCliente($(this).data('id'));
    });
	
	$('body').on('click', '.accionVer', function() {
        $('#viewFormulario').text('Ver Cliente');
        
        var id = $(this).data('id');
        js_cambiaViewstack(acronimo,'inicio','editar');
        js_clientes_cargaVistaEditar('ver',id);
    });
	
	$('body').on('click', '.accionEditar', function() {
        $('#viewFormulario').text('Editar Cliente');
        
        var id = $(this).data('id');
        js_cambiaViewstack(acronimo,'inicio','editar');
        js_clientes_cargaVistaEditar('editar',id);
    });
	
	$('body').on('click', '.accionEstatus', function() {
		$('#viewDocumentos').html('Estatus Cliente - <b>'+$(this).data('nombre')+'</b>');
		
        var id = $(this).data('id');
		var persona = $(this).data('persona');
		js_cambiaViewstack(acronimo,'inicio','documentos');
		js_clientes_cargaVistaDocumentos(id,persona);
    });
	
	$('body').on('click', '.accionEliminar', function() {
        
        var id = $(this).data('id');
        var nombre = $(this).data('nombre');
        js_generaModal('confirmEliminar','Eliminar Cliente','¿Seguro que desea eliminar al cliente "'+nombre+'"?','confirmacion','js_clientes_eliminarCliente',id);
    });
	
	$('body').on('change', '.uploadDocumento', function() {
        
		var idDoc = $(this).data('iddoc');
		var archivoAdj = '';
		
		for (var i = 0; i < $(this).get(0).files.length; ++i)
		{
			var size = $(this).get(0).files[i].size/1024/1024;
		
			if(size > 2)
			{
				//$('#lblAdjuntoDocumento_'+idDoc).data('isadj',0);
				js_generaModal('divError','Error','Uno de los archivos seleccionados excede el máximo de 2MB por archivo.','alerta');
				//$('#lblAdjuntoDocumento_'+idDoc).html('SIN ADJUNTO');
				$('.uploadDocumento').val('');
			
				break;
			}
			else
			{
				var archivo = $(this).get(0).files[i].name;
				
				archivoAdj += '<div class="input-group-append">'+
										archivo+
										'&nbsp;&nbsp;'+
										'<button type="button" class="close" aria-label="Close" title="Eliminar adjunto">'+
											'<span aria-hidden="true">&times;</span>'+
										'</button>'+
								 '</div>';				
			}
		}		
		
		if($('#lblAdjuntoDocumento_'+idDoc).data('isadj') == 0 && archivoAdj == '')
		{
			$('#lblAdjuntoDocumento_'+idDoc).html('SIN ADJUNTO(S)');
			$('#divAdjNuevo_'+idDoc).html('');
			$('.uploadDocumento').val('');
		}
		else
		{
			if($('#lblAdjuntoDocumento_'+idDoc).data('isadj') == 0)
			{
				$('#lblAdjuntoDocumento_'+idDoc).html('');
			}
			
			$('#divAdjNuevo_'+idDoc).html(archivoAdj);	
		}

    });
	
	$('body').on('click', '.delUploadDoc', function() {
		
		var idCE = $(this).data('idce');
        var nombreDoc = $(this).data('nombre');

        js_generaModal('confirmEliminar','Eliminar Cliente','¿Seguro que desea eliminar el documento "'+nombreDoc+'"?','confirmacion','js_clientes_eliminarDocumento',idCE);

    });
	
	$('body').on('click', '#btnGuardarAdjuntos', function() {
        event.preventDefault();
        js_clientes_guardaAdjuntos($(this).data('id'));
    });

    $('body').on('click', '.accionAgregarTel', function() {

        $('#tblTelefonoCliente > tbody  > tr').each(function(index, tr) {
            if($(this).hasClass( "emptyRow" ))
            {
                $(this).remove();
            }
         });

        js_clientes_agregaTelefono();
    });

    $('body').on('click', '.accionEliminarTel', function() {
        $(this).closest("tr").remove();
    });
});

function js_clientes_cargaClientes()
{
	try
    {
        js_llamadaAjax(modulo,'ajax_clientes_cargaClientes',null,'js_clientes_cargaClientesResultado');
    }
    catch(e)
    {
        js_generaModal('divError','Error','Error al ejecutar el proceso: '+e,'error');
    }
}


function js_clientes_cargaClientesResultado(data)
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
            $('#div_clientes_inicio_tablaClientes').html(respuesta.resultado);
            
            js_inicializaDataTable('tblClientes',true,true,true,false);
            
        }
    }
    catch(e)
    {
        js_generaModal('divError','Error','Error al ejecutar el proceso: '+e,'error');
    } 
}


function js_clientes_cargaVistaEditar(tipoAccion,id)
{
    try
    {
        $('#div_clientes_editar_form').html('');
        
        var formData = new FormData();
            
        formData.append('tipoAccion',tipoAccion);
        formData.append('id',id);    
        
        js_llamadaAjax(modulo,'ajax_clientes_cargaVistaEditar',formData,'js_clientes_cargaVistaEditarRespuesta');
    }
    catch(e)
    {
        js_generaModal('divError','Error','Error al ejecutar el proceso: '+e,'error');
    }
}


function js_clientes_cargaVistaEditarRespuesta(data)
{
    try
    {
        var respuesta = jQuery.parseJSON(data);
        
        if(respuesta.Error)
        {
            js_generaModal('divError','Error','Error al ejecutar el proceso: '+respuesta.ErrorMensaje,'error');
        }
        else
        {
            $('#div_clientes_editar_form').html(respuesta.resultado);
            
			if(respuesta.tipoAccion == 'ver')
            {
                js_deshabilitaFormulario();
            }
			else if(respuesta.tipoAccion == 'nuevo' || respuesta.tipoAccion == 'editar')
            {
                $('#btnGuardarCliente').data('id',respuesta.id);
                $('#btnGuardarCliente').data('accion',respuesta.tipoAccion);
            }
        }
    }
    catch(e)
    {
        js_generaModal('divError','Error','Error al ejecutar el proceso: '+e,'error');
    } 
}

function js_clientes_guardaCliente(id)
{
	try
    {
		var mensajeError = '';
		
		var nombreCliente = $('#txtNombreCliente').val();
		var direccionCliente = $('#txtDireccionCliente').val();
		var coloniaCliente = $('#txtColoniaCliente').val();
		var correoCliente = $('#txtCorreoCliente').val();
		var dirFiscal = $('#txtDomFiscalCliente').val();
		var razonSocial = $('#txtRSCliente').val();
		var rfcCliente = $('#txtRFCCliente').val();
		var personaCliente = $('input[name ="tipoCliente"]:checked').val();
	
		if(nombreCliente == '')
		{
			mensajeError = 'Favor de ingresar el nombre del cliente.';
		}
		
		if(direccionCliente == '')
		{
			mensajeError = 'Favor de ingresar la dirección del cliente.';
		}
		
		if(coloniaCliente == '')
		{
			mensajeError = 'Favor de ingresar la colonia del cliente.';
		}
		
		if(correoCliente == '')
		{
			mensajeError = 'Favor de ingresar el correo del cliente.';
		}
		
		if(mensajeError == '')
		{

            var telefonos = [];
            $('#tblTelefonoCliente > tbody  > tr').each(function(index, tr) {
                
                var fila = {};
                $(tr).find('td').each (function(index,td) {
                    
                    //console.log(index);
                    if(index == 0)
                    {
                        //console.log($(td).find('select').val());
                        fila['tipo'] = $(td).find('select option:selected').attr('id');

                    }
                    else if(index == 1)
                    {
                        fila['numero'] = $(td).find('input').val();
                    }
                });  

                telefonos.push(fila);
             });
            
            var strTelefonos = JSON.stringify(telefonos);

            //console.log(string);

			var formData = new FormData();
            formData.append('nombre',nombreCliente);
			formData.append('direccion',direccionCliente);
			formData.append('colonia',coloniaCliente);
			formData.append('correo',correoCliente);
			formData.append('dirFiscal',dirFiscal);
			formData.append('razonSocial',razonSocial);
			formData.append('rfc',rfcCliente);
			formData.append('persona',personaCliente);
			formData.append('id',id);
            formData.append('telefonos',strTelefonos);
			js_llamadaAjax(modulo,'ajax_clientes_guardaCliente',formData,'js_clientes_resultadoGuardaCliente');
		}
		else
		{
			js_generaModal('divError','Alerta',mensajeError,'alerta');
		}
	}
    catch(e)
    {
        js_generaModal('divError','Error','Error al ejecutar el proceso: '+e,'error');
    }
}


function js_clientes_resultadoGuardaCliente(data)
{
    try
    {
        var respuesta = jQuery.parseJSON(data);
        
        if(respuesta.Error)
        {
            js_generaModal('divError','Error','Error al ejecutar el proceso: '+respuesta.ErrorMensaje,'error');
        }
        else
        {
            js_cambiaViewstack(acronimo,'editar','inicio');
            
			js_clientes_cargaClientes();
        }
    }
    catch(e)
    {
        js_generaModal('divError','Error','Error al ejecutar el proceso: '+e,'error');
    } 
}

function js_clientes_cargaVistaDocumentos(id,persona)
{
	try
    {
        $('#div_clientes_estatus_form').html('');
        
        var formData = new FormData();
    
        formData.append('id',id);
		formData.append('persona',persona);    
        
        js_llamadaAjax(modulo,'ajax_clientes_cargaVistaDocumentos',formData,'js_clientes_cargaVistaDocumentosResultado');
    }
    catch(e)
    {
        js_generaModal('divError','Error','Error al ejecutar el proceso: '+e,'error');
    }
}

function js_clientes_cargaVistaDocumentosResultado(data)
{
	try
    {
        var respuesta = jQuery.parseJSON(data);
        
        if(respuesta.Error)
        {
            js_generaModal('divError','Error','Error al ejecutar el proceso: '+respuesta.ErrorMensaje,'error');
        }
        else
        {
            $('#div_clientes_estatus_form').html(respuesta.resultado);
        }
    }
    catch(e)
    {
        js_generaModal('divError','Error','Error al ejecutar el proceso: '+e,'error');
    }
}

function js_clientes_eliminarCliente(id)
{
	try
    {
        var formData = new FormData();
        formData.append('id',id);
        js_llamadaAjax(modulo,'ajax_clientes_eliminaCliente',formData,'js_clientes_resultadoEliminarCliente');
        
    }
    catch(e)
    {
        js_generaModal('divError','Error','Error al ejecutar el proceso: '+e,'error');
    }
}

function js_clientes_resultadoEliminarCliente(data)
{
	try
     {
        var respuesta = jQuery.parseJSON(data);
        
        if(respuesta.Error)
        {
            js_generaModal('divError','Error','Error al ejecutar el proceso: '+respuesta.ErrorMensaje,'error');
        }
        else
        {
            js_clientes_cargaClientes();
        }
    }
    catch(e)
    {
        js_generaModal('divError','Error','Error al ejecutar el proceso: '+e,'error');
    } 
}

function js_clientes_guardaAdjuntos(id)
{
	try
    {
        var formData = new FormData();
        formData.append('id',id);
		
		var arrEliminaAdjuntos = [];
		$('.delUploadDoc').each(function(){
			if($(this).data('iselimina') == 1)
			{
				arrEliminaAdjuntos.push($(this).data('iddoc'));
			}
		});
		
		formData.append('arrEliminaAdj',arrEliminaAdjuntos);
		
		$('.uploadDocumento').each(function(){
			
			for (var i = 0; i < $(this).get(0).files.length; ++i)
			{
				var archivo = $(this)[0].files[i];
				var nombre = $(this).data('nombre');
				nombre+='_'+i;
				formData.append(nombre,archivo);
			}
		});
		
        js_llamadaAjax(modulo,'ajax_clientes_guardaAdjuntos',formData,'js_clientes_guardaAdjuntosResultado');
        
    }
    catch(e)
    {
        js_generaModal('divError','Error','Error al ejecutar el proceso: '+e,'error');
    }
}

function js_clientes_guardaAdjuntosResultado(data)
{
	try
     {
        var respuesta = jQuery.parseJSON(data);
        
        if(respuesta.Error)
        {
            js_generaModal('divError','Error','Error al ejecutar el proceso: '+respuesta.ErrorMensaje,'error');
        }
        else
        {
            js_cambiaViewstack(acronimo,'documentos','inicio');
            
			js_clientes_cargaClientes();
        }
    }
    catch(e)
    {
        js_generaModal('divError','Error','Error al ejecutar el proceso: '+e,'error');
    } 
}

function js_clientes_eliminarDocumento(idCE)
{
    try
    {
        var formData = new FormData();
        formData.append('idCE',idCE);
        js_llamadaAjax(modulo,'ajax_clientes_eliminarDocumento',formData,'js_clientes_eliminarDocumentoResultado');
        
    }
    catch(e)
    {
        js_generaModal('divError','Error','Error al ejecutar el proceso: '+e,'error');
    }
}

function js_clientes_eliminarDocumentoResultado(data)
{
    try
    {
        var respuesta = jQuery.parseJSON(data);
        
        if(respuesta.Error)
        {
            js_generaModal('divError','Error','Error al ejecutar el proceso: '+respuesta.ErrorMensaje,'error');
        }
        else
        {
            js_clientes_cargaVistaDocumentos(respuesta.IdCliente,respuesta.PersonaCliente);
        }
    }
    catch(e)
    {
        js_generaModal('divError','Error','Error al ejecutar el proceso: '+e,'error');
    } 
}


function js_deshabilitaFormulario()
{
	$('.formClientes').prop('disabled',true);
}

function js_clientes_agregaTelefono()
{
    try
    {
        js_llamadaAjax(modulo,'ajax_clientes_agregaTelefono',null,'js_clientes_agregaTelefonoResultado');
    }
    catch(e)
    {
        js_generaModal('divError','Error','Error al ejecutar el proceso: '+e,'error');
    }
}

function js_clientes_agregaTelefonoResultado(data)
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
            $('#tblTelefonoCliente > tbody:last-child').append(respuesta.resultado);
        }
    }
    catch(e)
    {
        js_generaModal('divError','Error','Error al ejecutar el proceso: '+e,'error');
    } 
}