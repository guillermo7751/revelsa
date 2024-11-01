<?php

ini_set("display_errors",1);
error_reporting(E_ALL);

/****************************************************************************************************
FUNCION: revelsa_muestraLogin()
OBJETIVO: Mostrar la pantalla Login
PARAMETROS: variable con algún mensaje adicional que se requiera mostrar, se define el tipo de mensaje y el mensaje separado por |
****************************************************************************************************/

function revelsa_muestraLogin($isAcceso,$msj)
{
    $mensaje = '';
    
    if(!$isAcceso)
    {
		$mensaje = '<div class="alert-danger-login" role="alert"><i class="fa fa-exclamation-circle"></i> <span class="sr-only">Error:</span> '.$msj.'</div>'; 
    }
 
	$formulario = ' <body>  
                        <div class="limiter">
                        <div class="container-login100">
                            <div class="wrap-login100 p-l-55 p-r-55 p-t-65 p-b-50">
                                <form name="frmAcceso" id="frmAcceso" method="Post">
                                    <span class="login100-form-title p-b-15">
                                        <img src="img/logo.png" alt="revelsa" height="80%" width="80%">
                                    </span>
                
                                    <div class="wrap-input100 validate-input">
                                        <input class="input100 login-form" type="text" name="txtUsr" id="txtUsr" placeholder="Usuario">
                                        <span class="focus-input100-1"></span>
                                        <span class="focus-input100-2"></span>
                                    </div>
                
                                    <div class="wrap-input100 rs1 validate-input">
                                        <input class="input100 login-form" type="password" name="txtPassword" id="txtPassword" placeholder="Contraseña">
                                        <span class="focus-input100-1"></span>
                                        <span class="focus-input100-2"></span>
                                    </div>
                
                                    <div class="container-login100-form-btn m-t-20">
                                        <button type="button" id="btnLogin" class="login100-form-btn">
                                            INICIAR SESIÓN
                                        </button>
                                    </div>
                                </form>'
                                .$mensaje.'
                            </div>
                        </div>
                    </body>';
	
	return $formulario;	
}

function revelsa_muestraSistema()
{
    
    if(isset($_POST['modulo']) && $_POST['modulo']=="-1")
	{
		revelsa_cerrarSesion();
		$sistema = revelsa_muestraLogin(true,'');
	}
    else
    {
        if(isset($_POST['modulo']) && trim($_POST['modulo'])!="")
		{
			$_SESSION['modulo'] = $_POST['modulo'];
		}
        
        $sistema = '
                    <body id="page-top">
                        <div id="divModalModulo"></div>
                        <form name="frmSistema" id="frmSistema" method="post"  enctype="multipart/form-data" >
                            <div id="wrapper">
                                <!-- Sidebar -->
                                <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
                                  <br />
                                  <!-- Sidebar - Brand -->
                                  <a class="logoInicio sidebar-brand d-flex align-items-center justify-content-center" href="#">
                                    <div class="sidebar-brand-icon">
                                      <img src="img/logo-btn.png" alt="revelsa" height="60%" width="60%">
                                    </div>
                                  </a>
                                  <br />
                                  <!-- Divider -->
                                  <hr class="sidebar-divider">
                            
                                  '.revelsa_muestraMenu($_SESSION['idPerfil'],$_SESSION['modulo']).'
                            
                                  <!-- Divider -->
                                  <hr class="sidebar-divider d-none d-md-block">
                            
                            
                                  <!-- Sidebar Toggler (Sidebar) -->
                                  <div class="text-center d-none d-md-inline">
                                    <button type="button" class="rounded-circle border-0" id="sidebarToggle"></button>
                                  </div>
                            
                                </ul>
                                <!-- End of Sidebar -->
                            
                                <!-- Content Wrapper -->
                                <div id="content-wrapper" class="d-flex flex-column">
                            
                                  <!-- Main Content -->
                                  <div id="content">
                            
                                    <!-- Topbar -->
                                    <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                            
                                      <!-- Sidebar Toggle (Topbar) -->
                                      <button id="sidebarToggleTop" type="button" class="btn btn-link d-md-none rounded-circle mr-3">
                                        <i class="fa fa-bars"></i>
                                      </button>
                            
                                      <!-- Topbar Navbar -->
                                      <ul class="navbar-nav ml-auto">
                            
                                        <!-- Nav Item - User Information -->
                                        <li class="nav-item dropdown no-arrow">
                                          <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span class="mr-2 d-lg-inline text-gray-600 small">'.$_SESSION['usuario'].'</span>
                                            <input type="hidden" id="hid" value="'.$_SESSION['idUsuario'].'"/>
                                            <i class="fas fa-user-circle fa-md fa-fw mr-2 text-gray-400"></i>
                                          </a>
                                          <!-- Dropdown - User Information -->
                                          <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                              <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                              Salir
                                            </a>
                                          </div>
                                        </li>
                            
                                      </ul>
                            
                                    </nav>
                                    <!-- End of Topbar -->
                            
                                    <!-- Begin Page Content -->
                                    <div class="container-fluid">
                                        '.revelsa_muestraModulo($_SESSION['modulo']).'
                                    </div>
                                    <!-- /.container-fluid -->
                                    <input type="hidden" id="modulo" name="modulo" value="" />
                                  </div>
                                  <!-- End of Main Content -->
                            
                                  <!-- Footer -->
                                  <footer class="sticky-footer bg-white">
                                    <div class="container my-auto">
                                      <div class="copyright text-center my-auto">
                                        <span>Copyright &copy; Revelsa '.date('Y').'</span>
                                      </div>
                                    </div>
                                  </footer>
                                  <!-- End of Footer -->
                            
                                </div>
                                <!-- End of Content Wrapper -->
                            
                              </div>
                              <!-- End of Page Wrapper -->
                            
                              <!-- Scroll to Top Button-->
                              <a class="scroll-to-top rounded" href="#page-top">
                                <i class="fas fa-angle-up"></i>
                              </a>
                              
                              '.revelsa_muestra_modalLogout().'
                        </form>
                    </body>';
    }
    
    return $sistema;
}

function revelsa_muestra_modalLogout()
{
    $modal = '<!-- Logout Modal-->
                  <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="exampleModalLabel">¿Seguro que deseas salir?</h5>
                          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                          </button>
                        </div>
                        <div class="modal-body">Presiona "Salir" si estás listo para terminar la sesión.</div>
                        <div class="modal-footer">
                          <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancelar</button>
                          <a class="btn btn-primary salir" href="#">Salir</a>
                        </div>
                      </div>
                    </div>
                </div>';
                
    return $modal;
}


function revelsa_muestraModulo($idModulo)
{
    $modulo = '';
    
	if(empty($idModulo))
	{
		$modulo = '';
	}
	else
	{
		$sql="CALL sp_sistema_dameInfoModulo(".$idModulo.");";		
		$res = bd_consultaSQL($sql);
		$fila = bd_dameRegistro($res);
		
		$modulo='';
		$idDivModulo = 'divModulo_'.$idModulo;
		
		if(file_exists('modulos'.DIRECTORY_SEPARATOR.$fila['ruta']))
		{
			require_once('modulos'.DIRECTORY_SEPARATOR.$fila['ruta']);
			
            //<h1 class="h3 mb-2 text-gray-800">'.($fila['nombre']).'</h1>
			$modulo = '
			<div id="'.$idDivModulo.'">
				'.$modulo.'
			</div>
			';
		}
		else
		{
			$modulo = '
			<div id="'.$idDivModulo.'">
				<div class="page-title">
					<div class="title-left">
						<div class="alert alert-info">
                            No se encontró el módulo "<strong>'.($fila['nombre']).'</strong>".
						</div>	
					</div>		 
				</div>
			</div>
			';
		}
        
        bd_liberaResultSet($res);
	}
		
	return validaSesion().$modulo;
}

function revelsa_muestraMenu($perfil,$idModuloSeleccionado)
{
    $menu = '';
    
    $sqlMenu = "CALL sp_sistema_dameModulosMenu(0,".$perfil.")";
    $resMenu = bd_consultaSQL($sqlMenu);
    
    if(bd_cuentaRegistros($resMenu) > 0)
	{
        $rowCount = 0;
        
        while ($fila = bd_dameRegistro($resMenu))
		{
            if($rowCount > 0)
            {
                $menu.='<!-- Divider -->
                        <hr class="sidebar-divider">';
            }
            
           
            $menu.= '<div class="sidebar-heading">
                        '.$fila['modulo'].'
                      </div>';
                      
            $sqlMenuN2 = "CALL sp_sistema_dameModulosMenu(".$fila['id'].",".$perfil.")";
            $resMenuN2 = bd_consultaSQL($sqlMenuN2);
            
            if(bd_cuentaRegistros($resMenuN2) > 0)
            {
                while ($filaMenu2 = bd_dameRegistro($resMenuN2))
                {
                    $clasePadre = '';
                    
                    $sqlMenuN3 = "CALL sp_sistema_dameModulosMenu(".$filaMenu2['id'].",".$perfil.")";
                    $resMenuN3 = bd_consultaSQL($sqlMenuN3);
                    
                    if(bd_cuentaRegistros($resMenuN3) > 0)
                    {
                        $menuCategorias = '';
                        
                        while ($filaMenu3 = bd_dameRegistro($resMenuN3))
                        {
                            //VALIDAMOS QUE EXISTAN ITEMS PARA EL MENÚ NIVEL 3
                            
                            $sqlMenuN4 = "CALL sp_sistema_dameModulosMenu(".$filaMenu3['id'].",".$perfil.")";
                            $resMenuN4 = bd_consultaSQL($sqlMenuN4); 
                            
                            if(bd_cuentaRegistros($resMenuN4) > 0)
                            {
                                $menuItems= '';
                                while ($filaMenu4 = bd_dameRegistro($resMenuN4))
                                {
                                    $claseHijo = '';
                                    
                                    if($idModuloSeleccionado == $filaMenu4['id'])
                                    {
                                        $clasePadre = 'active';
                                        $claseHijo = 'active';
                                    }
                                    
                                    $menuItems.='<a class="collapse-item '.$claseHijo.'" href="#" onclick="navegar_modulo('.$filaMenu4['id'].')">'.$filaMenu4['modulo'].'</a>';
                                    
                                }
                                
                                $menuCategorias.='  <div id="collapse_'.$filaMenu3['id'].'" class="collapse col_'.$filaMenu2['id'].'" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                                            <div class="bg-white py-2 collapse-inner rounded">
                                              <h6 class="collapse-header">'.$filaMenu3['modulo'].'</h6>
                                              '.$menuItems.'
                                            </div>
                                          </div>';
                                
                            }
                            
                            
                        }
                    }
                    
                    $menu.=' <!-- Nav Item - Pages Collapse Menu -->
                               <li class="nav-item '.$clasePadre.'">
                                 <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target=".col_'.$filaMenu2['id'].'" aria-expanded="true" aria-controls="collapseTwo">
                                   <i class="'.$filaMenu2['icono'].'"></i>
                                   <span>'.$filaMenu2['modulo'].'</span>
                                 </a>
                                 '.$menuCategorias.'
                               </li>';
                }
            }
            
            $rowCount++;
        }
        
    }
    
    bd_liberaResultSet($resMenu);
    
    return $menu;
}

function validaSesion()
{
	
	$cont = '';
	
	if(!isset($_SESSION['idUsuario']))
	{
		$_SESSION['idUsuario'] = "-1";
	}
	
	$hash = md5($_SESSION['idUsuario'].'valida');
	$hash64 = base64_encode($hash);
	
	$cont.="
	
	<script>
	
		var wwVSesion;
	
		iniciaWWVSesion();
		
		
		function iniciaWWVSesion()
		{
			if(typeof(Worker) !== 'undefined')
			{
				if(typeof(wwVSesion) == 'undefined')
				{
					wwVSesion = new Worker('motor/ww_sesion_funciones.js');
					wwVSesion.postMessage(['".$hash."','".$hash64."']);
				}
				
				wwVSesion.onmessage = function(event)
				{
					
					var jsonData = jQuery.parseJSON(event.data);
					
					if(jsonData.Error)
					{
						finalizaWWVSesion();
						navegar_modulo(-1);
					}
					
				};
			}
			else
			{
				Alert.show('Web Worker no soporta este navegador.');
			}
		}
		
		function finalizaWWVSesion()
		{
			if(typeof(wwVSesion)!=='undefined')
			{
				wwVSesion.terminate();
			}
			
			wwVSesion = undefined;
		}
		
	</script>
	";
	
	
	
	
	
	
	
	return $cont;
}

?>
