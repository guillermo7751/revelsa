var modulo = 'modulos/catalogos/andamios/catalogoAndamios/catalogoAndamios_cont.php';
var acronimo = 'catalogoAndamios';

$(document).ready(function(){

    js_catalogoAndamios_cargaCatalogo();
    
    $('body').on('click', '#btnNuevoEquipo', function() {
        $('#viewFormulario').text('Agregar Producto');
        
        js_cambiaViewstack(acronimo,'inicio','editar');
        js_catalogoAndamios_cargaVistaEditar('nuevo','');
        
    });
    
    $('body').on('click', '#btnRegresarInicio', function() {
        
        js_cambiaViewstack(acronimo,'editar','inicio');
    });
    
    $('body').on('change', '.chkRenta', function() {
        
        if(this.checked)
        {
            $('#'+$(this).data('id')).prop('disabled',false);
            $('#'+$(this).data('id')).addClass('clsTiempoRenta');
            $('#'+$(this).data('id')).val(0);
        }
        else
        {
            $('#'+$(this).data('id')).prop('disabled',true);
            $('#'+$(this).data('id')).removeClass('clsTiempoRenta');
            $('#'+$(this).data('id')).val('');
        }
    });
    
    $('body').on('click', '#btnGuardarEquipo', function(event) {
        event.preventDefault();
        js_catalogoAndamios_guardaEquipo($(this).data('id'));
    });
    
    $('body').on('click', '.accionEliminar', function() {
        
        var id = $(this).data('id');
        var nombre = $(this).data('nombre');
        js_generaModal('confirmEliminar','Eliminar Producto','¿Seguro que desea eliminar el producto "'+nombre+'"?','confirmacion','js_catalogoAndamios_eliminarEquipo',id);
    });
    
    $('body').on('click', '.accionEditar', function() {
        $('#viewFormulario').text('Editar Producto');
        
        var id = $(this).data('id');
        js_cambiaViewstack(acronimo,'inicio','editar');
        js_catalogoAndamios_cargaVistaEditar('editar',id);
    });
    
    $('body').on('click', '.accionVer', function() {
        $('#viewFormulario').text('Ver Equipo');
        
        var id = $(this).data('id');
        js_cambiaViewstack(acronimo,'inicio','editar');
        js_catalogoAndamios_cargaVistaEditar('ver',id);
    });
    
    $('body').on('change', '#uploadEquipo', function() {
        js_catalogoAndamios_cargarImagen(this,'resultadoEquipo','clsEquipoImg');
    });
    
    $('body').on('click', '#btnEliminarImagen', function() {
        $('#resultadoEquipo').data('vacio','1');
        js_quitarImagen('uploadEquipo','resultadoEquipo','clsEquipoImg');
    });
});

function js_catalogoAndamios_cargaCatalogo()
{
    try
    {
        js_llamadaAjax(modulo,'ajax_catalogoAndamios_cargaCatalogo',null,'js_catalogoAndamios_cargaCatalogoRespuesta');
    }
    catch(e)
    {
        js_generaModal('divError','Error','Error al ejecutar el proceso: '+e,'error');
    }
}


function js_catalogoAndamios_cargaCatalogoRespuesta(data)
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
            $('#div_catalogoAndamios_inicio_tablaCatalogo').html(respuesta.resultado);
            
            js_inicializaDataTable('tblEquipos',true,true,true,false);
            
        }
    }
    catch(e)
    {
        js_generaModal('divError','Error','Error al ejecutar el proceso: '+e,'error');
    } 
}

function js_catalogoAndamios_cargaVistaEditar(tipoAccion,id)
{
    try
    {
        $('#div_catalogoAndamios_editar_form').html('');
        
        var formData = new FormData();
            
        formData.append('tipoAccion',tipoAccion);
        formData.append('id',id);    
        
        js_llamadaAjax(modulo,'ajax_catalogoAndamios_cargaVistaEditar',formData,'js_catalogoAndamios_cargaVistaEditarRespuesta');
    }
    catch(e)
    {
        js_generaModal('divError','Error','Error al ejecutar el proceso: '+e,'error');
    }
}


function js_catalogoAndamios_cargaVistaEditarRespuesta(data)
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
            $('#div_catalogoAndamios_editar_form').html(respuesta.resultado);
            
            $(".chosen-select").chosen({width:"100%",no_results_text: "No se encontraron registros."});
    
            if(respuesta.tipoAccion == 'ver')
            {
                js_deshabilitaFormulario();
            }
            else if(respuesta.tipoAccion == 'nuevo' || respuesta.tipoAccion == 'editar')
            {
                $('#btnGuardarEquipo').data('id',respuesta.id);
                $('#btnGuardarEquipo').data('accion',respuesta.tipoAccion);
            }
        
            if($('#resultadoEquipo').prop('src')!= window.location.href)
            {
                $('.clsEquipoImg').addClass('image-no-before');
            }
        }
    }
    catch(e)
    {
        js_generaModal('divError','Error','Error al ejecutar el proceso: '+e,'error');
    } 
}

function js_catalogoAndamios_guardaEquipo(id)
{
    try
    {
        
        var mensajeError = '';
        
        var nombre = $('#txtNombreEquipo').val();
        var desc = $('#txtDescEquipo').val();
        var isDefaultDesc = 0;
        if($('#descDefault'). is(":checked"))
        {
            isDefaultDesc = 1;
        }
        
        var montoVenta = $('#txtMontoVenta').val();
        
        if(nombre == '')
        {
            mensajeError = 'Favor de ingresar un nombre para el equipo.';
        }
        
        if(montoVenta == '' || isNaN(montoVenta) || montoVenta<0)
        {
            mensajeError = 'Favor de ingresar un precio a la venta válido para el equipo.';
        }
        
        var arrPreciosRenta = [];
        //RECORREMOS LA TABLA PARA LOS PRECIOS A LA RENTA
        $( ".clsTiempoRenta" ).each(function() {
            arrPreciosRenta.push($(this).attr('id') + '_'+$(this).val());
        });
        
        var imagen = $('#uploadEquipo')[0].files[0];
        
        var isImgVacio = $('#resultadoEquipo').data('vacio');
        
        if(mensajeError == '')
        {
            var formData = new FormData();
            formData.append('nombre',nombre);
            formData.append('montoVenta',montoVenta);
            formData.append('desc',desc);
            formData.append('isDefaultDesc',isDefaultDesc);
            formData.append('strPreciosRenta',arrPreciosRenta);
            formData.append('imagen',imagen);
            formData.append('isImgVacio',isImgVacio);
            formData.append('id',id);
            js_llamadaAjax(modulo,'ajax_catalogoAndamios_guardaEquipo',formData,'js_catalogoAndamios_resultadoGuardaEquipo');
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

function js_catalogoAndamios_cargarImagen(data,idImg,cls)
{
    
    var imgValida = js_cargarImagen(data,idImg,cls);
    
    if(imgValida)
    {
        $('#resultadoEquipo').data('vacio','0');
    }
}

function js_catalogoAndamios_resultadoGuardaEquipo(data)
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
            js_catalogoAndamios_cargaCatalogo();
            js_cambiaViewstack(acronimo,'editar','inicio');
        }
    }
    catch(e)
    {
        js_generaModal('divError','Error','Error al ejecutar el proceso: '+e,'error');
    } 
}

function js_catalogoAndamios_eliminarEquipo(id)
{
    try
    {
        var formData = new FormData();
        formData.append('id',id);
        js_llamadaAjax(modulo,'ajax_catalogoAndamios_eliminaEquipo',formData,'js_catalogoAndamios_resultadoEliminarEquipo');
        
    }
    catch(e)
    {
        js_generaModal('divError','Error','Error al ejecutar el proceso: '+e,'error');
    }
}

function js_catalogoAndamios_resultadoEliminarEquipo(data)
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
            js_catalogoAndamios_cargaCatalogo();
        }
    }
    catch(e)
    {
        js_generaModal('divError','Error','Error al ejecutar el proceso: '+e,'error');
    } 
}

function js_deshabilitaFormulario()
{
    $('.formCatalogo').prop('disabled',true);
    $('#btnEliminarImagen').addClass('disabled');
}

