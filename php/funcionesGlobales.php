<?php


function global_reducirImagen($imgOriginal,$w,$h,$tipo)
{
	$im = imagecreatefromstring($imgOriginal);
    $imagenReducida = imagescale($im,$w,$h);
    ob_start();
    if($tipo == 'image/jpeg')
    {
        imagejpeg($imagenReducida,null,100);
    }
    elseif($tipo == 'image/png')
    {
        imagepng($imagenReducida,null,100);
    }
	
    $contents = ob_get_contents();
    ob_end_clean();
    $imagenReducida = base64_encode($contents);
	
	return $imagenReducida;
    
}

?>
