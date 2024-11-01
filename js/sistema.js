$(document).ready(function(){
   

    $("#btnLogin").on('click',function(){
        validarLogin();
    });
    
    $("#frmAcceso .login-form").on('keypress',function(e){
        if(e.which == 13 || e.keyCode == 13){
        	validarLogin();
        }
    });
    
    $(".salir").on('click',function(){
        salirSistema();
    });
    
});

var alertaBox = 0;

function validarLogin(){
   
    var error = 0;
    
    $("#frmAcceso .login-form").each(function(){
        if($.trim($(this).val()).length > 0)
        {
            hideValidate(this);          
        }
        else
        {
            showValidate(this);
            error++;
        }
    });
        
    if(error > 0)
    {
        if(alertaBox == 0)
        {
            alertaBox = 1;
            $("form").append($('<div class="alert-danger-login" role="alert"><i class="fa fa-exclamation-circle"></i> <span class="sr-only">Error:</span> Favor de ingresar los datos que faltan.</div>').fadeIn('slow').delay(3000).slideUp('slow'));
        
            setTimeout(function() { 
                alertaBox = 0;
            }, 4000);
        }
    } 
    else
    {
        $('#txtPassword').val(encriptaPassword($('#txtPassword').val()));//Encripto
        $("#frmAcceso").submit();//Navego para intentar loguear
    } 
   
}

function showValidate(input)
{
    var thisAlert = $(input).parent();

    $(thisAlert).addClass('alert-validate');
}

function hideValidate(input)
{
    var thisAlert = $(input).parent();

    $(thisAlert).removeClass('alert-validate');
}
    
function navegar_modulo(destino)
{
	$("#modulo").val(destino);
    $("#frmSistema").submit();
}

function navegar(destino)
{
	document.frmSistema.accion.value = destino; 
	$("#frmSistema").submit();
}


function salirSistema()
{
    
    $("#modulo").val(-1);
    $("#frmSistema").submit();
}
