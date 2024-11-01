var modulo = 'modulos/catalogos/maquinaria/catalogoEquipos/catalogoEquipos_cont.php';
var acronimo = 'catalogoEquipos';

$(document).ready(function(){

    js_catalogoEquipos_cargaCatalogo();
    
    $('body').on('click', '#btnNuevoEquipo', function() {
        $('#viewFormulario').text('Agregar Equipo');
        
        js_cambiaViewstack(acronimo,'inicio','editar');
        js_catalogoEquipos_cargaVistaEditar('nuevo','');
        
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
        js_catalogoEquipos_guardaEquipo($(this).data('id'));
    });
    
    $('body').on('click', '.accionEliminar', function() {
        
        var id = $(this).data('id');
        var nombre = $(this).data('nombre');
        js_generaModal('confirmEliminar','Eliminar Equipo','¿Seguro que desea eliminar el equipo "'+nombre+'"?','confirmacion','js_catalogoEquipos_eliminarEquipo',id);
    });
    
    $('body').on('click', '.accionEditar', function() {
        $('#viewFormulario').text('Editar Equipo');
        
        var id = $(this).data('id');
        js_cambiaViewstack(acronimo,'inicio','editar');
        js_catalogoEquipos_cargaVistaEditar('editar',id);
    });
    
    $('body').on('click', '.accionVer', function() {
        $('#viewFormulario').text('Ver Equipo');
        
        var id = $(this).data('id');
        js_cambiaViewstack(acronimo,'inicio','editar');
        js_catalogoEquipos_cargaVistaEditar('ver',id);
    });
    
    $('body').on('change', '#uploadEquipo', function() {
        js_catalogoEquipos_cargarImagen(this,'resultadoEquipo','clsEquipoImg');
    });
    
    $('body').on('click', '#btnEliminarImagen', function() {
        $('#resultadoEquipo').data('vacio','1');
        js_quitarImagen('uploadEquipo','resultadoEquipo','clsEquipoImg');
    });
});

function js_catalogoEquipos_cargaCatalogo()
{
    try
    {
        js_llamadaAjax(modulo,'ajax_catalogoEquipos_cargaCatalogo',null,'js_catalogoEquipos_cargaCatalogoRespuesta');
    }
    catch(e)
    {
        js_generaModal('divError','Error','Error al ejecutar el proceso: '+e,'error');
    }
}


function js_catalogoEquipos_cargaCatalogoRespuesta(data)
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
            $('#div_catalogoEquipos_inicio_tablaCatalogo').html(respuesta.resultado);
            
            js_inicializaDataTable('tblEquipos',true,true,true,false);
            
        }
    }
    catch(e)
    {
        js_generaModal('divError','Error','Error al ejecutar el proceso: '+e,'error');
    } 
}

function js_catalogoEquipos_cargaVistaEditar(tipoAccion,id)
{
    try
    {
        $('#div_catalogoEquipos_editar_form').html('');
        
        var formData = new FormData();
            
        formData.append('tipoAccion',tipoAccion);
        formData.append('id',id);    
        
        js_llamadaAjax(modulo,'ajax_catalogoEquipos_cargaVistaEditar',formData,'js_catalogoEquipos_cargaVistaEditarRespuesta');
    }
    catch(e)
    {
        js_generaModal('divError','Error','Error al ejecutar el proceso: '+e,'error');
    }
}


function js_catalogoEquipos_cargaVistaEditarRespuesta(data)
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
            $('#div_catalogoEquipos_editar_form').html(respuesta.resultado);
            
            $("#cmbMarcas option:first").attr('disabled', 'disabled');
            
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
        }
    }
    catch(e)
    {
        js_generaModal('divError','Error','Error al ejecutar el proceso: '+e,'error');
    } 
}

function js_catalogoEquipos_guardaEquipo(id)
{
    try
    {
        
        var mensajeError = '';
        
        var nombre = $('#txtNombreEquipo').val();
        var desc = $('#txtDescEquipo').val();
        var idMarca = $('#cmbMarcas option:selected').val();
        var montoVenta = $('#txtMontoVenta').val();
        
        if(nombre == '')
        {
            mensajeError = 'Favor de ingresar un nombre para el equipo.';
        }
        
        if(idMarca == 0)
        {
            mensajeError = 'Favor de seleccionar una marca para el equipo.';
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
            formData.append('idMarca',idMarca);
            formData.append('montoVenta',montoVenta);
            formData.append('desc',desc);
            formData.append('strPreciosRenta',arrPreciosRenta);
            formData.append('imagen',imagen);
            formData.append('isImgVacio',isImgVacio);
            formData.append('id',id);
            js_llamadaAjax(modulo,'ajax_catalogoEquipos_guardaEquipo',formData,'js_catalogoEquipos_resultadoGuardaEquipo');
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

function js_catalogoEquipos_cargarImagen(data,idImg,cls)
{
    
    var imgValida = js_cargarImagen(data,idImg,cls);
    
    if(imgValida)
    {
        $('#resultadoEquipo').data('vacio','0');
    }
}

function js_catalogoEquipos_resultadoGuardaEquipo(data)
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
            js_catalogoEquipos_cargaCatalogo();
            js_cambiaViewstack(acronimo,'editar','inicio');
        }
    }
    catch(e)
    {
        js_generaModal('divError','Error','Error al ejecutar el proceso: '+e,'error');
    } 
}

function js_catalogoEquipos_eliminarEquipo(id)
{
    try
    {
        var formData = new FormData();
        formData.append('id',id);
        js_llamadaAjax(modulo,'ajax_catalogoEquipos_eliminaEquipo',formData,'js_catalogoEquipos_resultadoEliminarEquipo');
        
    }
    catch(e)
    {
        js_generaModal('divError','Error','Error al ejecutar el proceso: '+e,'error');
    }
}

function js_catalogoEquipos_resultadoEliminarEquipo(data)
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
            js_catalogoEquipos_cargaCatalogo();
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
    $('#cmbMarcas').prop('disabled', true).trigger("chosen:updated");
}

