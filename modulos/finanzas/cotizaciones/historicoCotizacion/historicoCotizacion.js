var modulo = 'modulos/finanzas/cotizaciones/historicoCotizacion/historicoCotizacion_cont.php';
var acronimo = 'historicoCotizacion';

$(document).ready(function(){
	
	js_historicoCotizacion_dameCotizaciones();
	
	
	$('body').on('click', '.accionVer', function() {
		
		window.open(window.location.href.split('#')[0]+($(this).data('ruta')));
	});
});


function js_historicoCotizacion_dameCotizaciones()
{
    try
    {
        js_llamadaAjax(modulo,'ajax_historicoCotizacion_dameCotizaciones',null,'js_historicoCotizacion_resultadoDameCotizaciones');
    }
    catch(e)
    {
        js_generaModal('divError','Error','Error al ejecutar el proceso: '+e,'error');
    }
}


function js_historicoCotizacion_resultadoDameCotizaciones(data)
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
            $('#div_historicoCotizacion_inicio_tablaCotizaciones').html(respuesta.resultado);
            
            js_inicializaDataTable('tblCotizaciones',true,true,true,false);
            
        }
    }
    catch(e)
    {
        js_generaModal('divError','Error','Error al ejecutar el proceso: '+e,'error');
    } 
}
