function js_cargarImagen(input,idArea,clsArea)
{
    var imgValida = false;
    
    var idUploader = $(input).attr('id');
    
    if (input.files && input.files[0]) {
        
        var size = (input.files[0].size/1024/1024);
        
        if(size > 2)
        {
            js_generaModal('divError','Error','La imagen seleccionada excede el máximo de 2MB por archivo.','alerta');
            $('#'+idUploader).val('');
            $('.'+clsArea).removeClass('image-no-before');
            $('#'+idArea).attr('src','');
        }
        else
        {    
            var reader = new FileReader();
    
            reader.onload = function (e) {
                
                var image = new Image();
      
                image.onload=function(){
                    var canvas=document.createElement("canvas");
                    var context=canvas.getContext("2d");
                    canvas.width=image.width/4;
                    canvas.height=image.height/4;
                    context.drawImage(image,
                        0,
                        0,
                        image.width,
                        image.height,
                        0,
                        0,
                        canvas.width,
                        canvas.height
                    );
    
                    $('#'+idArea).attr('src', canvas.toDataURL());
                };
                
                image.src=e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
            
            $('.'+clsArea).addClass('image-no-before');
            
            imgValida = true;
        }
    }
    
    return imgValida;
}

function js_quitarImagen(idUploader,idArea,clsArea)
{
    $('#'+idUploader).val('');
    $('.'+clsArea).removeClass('image-no-before');
    $('#'+idArea).attr('src','');
}
