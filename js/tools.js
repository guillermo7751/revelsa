function js_generaModal(idModal,tituloModal,mensajeModal,tipo,callback,args)
{
    if(typeof callback === "undefined")
    {
        callback = '';
    }
    
    if(typeof args === "undefined")
    {
        args = '';
    }
    
    try
    {
        var rand = '';
        
        if(tipo == 'espera')
        {
            $("#divModalModulo").append(js_modalEspera(idModal,tituloModal));
            
            $("#"+idModal).modal({
                                backdrop: 'static',
                                keyboard: false
                                });
        }
        else if(tipo == 'confirmacion')
        {
            rand = Math.floor((Math.random() * 100000) + 1);
            
            idModal+='_'+rand;
            
            $("#divModalModulo").append(js_modalConfirmacion(idModal,tituloModal,mensajeModal,null,rand));
            
            $("#"+idModal).modal({
                                backdrop: 'static',
                                keyboard: false
                                });
            
            $('#btnAceptar_'+rand).on('click',function(evt){
                //delegamos la operacion a la funcion del control de modulo
                if(callback==null || callback == '')
                {
                    //intenciona por el momento
                }
                else
                {
                    var fn ="";
                    if (jQuery.isFunction( callback)   )
                    {
                        fn = functionCallBack;
                    }
                    else
                    {
                        fn = window[callback];
                    }
                    
                    if (jQuery.isFunction(fn))
                    {
                        //Si la recupero o pudo asignar 
                        var callbacks = $.Callbacks();
                        callbacks.add(fn);
                        callbacks.fire(args);
                    }
                }
            });
        }
        else
        {
            var clase = '';
            if(tipo == 'error')
            {
                clase = 'modalError';
            }
            else if(tipo == 'alerta')
            {
                clase = 'modalAlerta';
            }
            else
            {
                clase = 'modalExito';
            }
            
            rand = Math.floor((Math.random() * 100000) + 1);
            
            idModal+='_'+rand;
            
            $("#divModalModulo").append(js_modalGeneral(idModal,tituloModal,mensajeModal,clase,rand,callback));
            
            if(callback != null && callback != '')
            {
                $("#"+idModal).modal({
                                backdrop: 'static',
                                keyboard: false
                                });
            }
            
            $('#btnAceptar_'+rand).on('click',function(evt){
                //delegamos la operacion a la funcion del control de modulo
                if(callback==null || callback == '')
                {
                    //intenciona por el momento
                }
                else
                {
                    var fn ="";
                    if (jQuery.isFunction( callback)   )
                    {
                        fn = functionCallBack;
                    }
                    else
                    {
                        fn = window[callback];
                    }
                    
                    if (jQuery.isFunction(fn))
                    {
                        //Si la recupero o pudo asignar 
                        var callbacks = $.Callbacks();
                        callbacks.add(fn);
                        callbacks.fire(args);
                    }
                }
            });
            
            $("#"+idModal).modal(); 
        }
        
    }
    catch(eAlerta)
    {
        alert(eAlerta);
    }   
}

function js_modalGeneral(idModal,tituloModal,mensajeModal,clase,rand,callback)
{
    var botonClose = '<button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>';
    
    if(callback!=null && callback != '')
    {
        botonClose = '';
    }
       
	var modalGeneral = '<div id="'+idModal+'" class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">'+
							'<div class="modal-dialog" role="document">'+
							  '<div class="modal-content">'+
								'<div class="modal-header '+clase+'">'+
								  '<h5 class="modal-title" id="exampleModalLabel">'+tituloModal+'</h5>'+
								  botonClose+
								'</div>'+
								'<div class="modal-body">'+mensajeModal+'</div>'+
								'<div class="modal-footer">'+
								  '<button id="btnAceptar_'+rand+'" class="btn btn-secondary" type="button" data-dismiss="modal">Ok</button>'+
								'</div>'+
							  '</div>'+
							'</div>'+
						'</div>';
		
	return modalGeneral;
}


function js_modalEspera(idModal,tituloModal)
{
	var modalEspera = '<div id="'+idModal+'" class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">'+
							'<div class="modal-dialog modal-sm" role="document">'+
							  '<div class="modal-content">'+
								'<div class="modal-body text-center">'+
                                    '<div class="spinner-border text-primary" style="margin:10px" role="status"></div>'+
                                    '<div class="text-center"><h4>Espere un momento...</h4></div>'+
                                '</div>'+
							  '</div>'+
							'</div>'+
						'</div>';
		
	return modalEspera;
}


function js_modalConfirmacion(idModal,tituloModal,mensajeModal,clase,rand)
{
	var modalConfirmacion = '<div class="modal fade" id="'+idModal+'" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">'+
                                '<div class="modal-dialog" role="document">'+
                                  '<div class="modal-content">'+
                                    '<div class="modal-header '+clase+'">'+
                                      '<h5 class="modal-title" id="exampleModalLabel">'+tituloModal+'</h5>'+
                                      '<button class="close" type="button" data-dismiss="modal" aria-label="Close">'+
                                        '<span aria-hidden="true">×</span>'+
                                      '</button>'+
                                    '</div>'+
                                    '<div class="modal-body">'+mensajeModal+'</div>'+
                                    '<div class="modal-footer">'+
                                      '<button class="btn btn-secondary" type="button" data-dismiss="modal">Cancelar</button>'+
                                      '<button id="btnAceptar_'+rand+'" class="btn btn-primary" type="button" data-dismiss="modal">Aceptar</button>'+
                                    '</div>'+
                                  '</div>'+
                                '</div>'+
                            '</div>';
		
	return modalConfirmacion;
}

function js_cerrarModal(idModal)
{
    $('#'+idModal).remove();
    
    if($('.modal-backdrop').size()==1)//Cuando sólo hay una ventana
    {
        $('.modal-backdrop').remove();
        $('body').removeClass( "modal-open" );
    }
    else if($('.modal-backdrop').size()>1)//Cuando hay más de una ventana
    {
        $('.modal-backdrop').each(function() {
            $( this ).remove();
            return false;//Quito el primer fondo, dejo los demás
        });
    }
}
