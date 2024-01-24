<?php
ini_set('display_errors', 1);
//validamos la peticion ajax
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
	if (isset($_POST['peticion'])) {
		require_once(dirname(__DIR__) . '/libraries/rutas.php');
		require_once(rutaBase . 'php' . DS . 'libraries' . DS . 'sesion.php');
		require_once(rutaBase . 'php' . DS . 'libraries' . DS . 'validaciones.php');
		//antes de aceptar cualquier peticion validop que exista la sesion
		//y que tenga permisos sobre el modulo
		$usuario = sesion::getparametro('usuario');
		$permisos = sesion::getparametro('permisos');
		if (!empty($permisos)) {
			$peticion = $_POST['peticion'];
			switch ($peticion) {
				case "guardarActividad":
					$unidadServicios = isset($_POST['unidadServicios']) ? trim(mb_strtoupper($_POST['unidadServicios'], "UTF-8")) : null;
					$empresaPlan = isset($_POST['empresaPlan']) ? trim(mb_strtoupper($_POST['empresaPlan'], "UTF-8")) : null;
					$fechaInicio= isset($_POST['fechaInicio']) ? trim(mb_strtoupper($_POST['fechaInicio'], "UTF-8")) : null;
					$fechaFin = isset($_POST['fechaFin']) ? trim(mb_strtoupper($_POST['fechaFin'], "UTF-8")) : null;
					$responsableActividad = isset($_POST['responsableActividad']) ? trim(mb_strtoupper($_POST['responsableActividad'], "UTF-8")) : null;
					$observaciones = isset($_POST['observaciones']) ? trim(mb_strtoupper($_POST['observaciones'], "UTF-8")) : null;
					$observacionesCierre = isset($_POST['observacionesCierre']) ? trim(mb_strtoupper($_POST['observacionesCierre'], "UTF-8")) : null;
					$tipoActividad = isset($_POST['tipoActividad']) ? trim(mb_strtoupper($_POST['tipoActividad'], "UTF-8")) : null;

					// Convierte las fechas a formato numérico
					//$fechaInicioArray = explode('-', $fechaInicio);
					//$fechaInicio = date('d-m-Y', strtotime(implode('-', array_map('intval', $fechaInicioArray))));

					//$fechaFinArray = explode('-', $fechaFin);
					//$fechaFin = date('d-m-Y', strtotime(implode('-', array_map('intval', $fechaFinArray))));

					if (validar::letras($unidadServicios)
					&& validar::letras($empresaPlan)
					//&& validar::fecha($fechaInicio,'-', 'dma') 
					//&& validar::fecha($fechaFin,'-', 'dma') 
					&& validar::letras($responsableActividad)
					&& validar::letras($observaciones)
					&& validar::letras($observacionesCierre)
					&& validar::letras($tipoActividad)) {
					
					require_once(rutaBase . 'php' . DS . 'model' . DS . 'model_calendarioActividades.php');
					echo model_calendarioActividades::guardarActividad($unidadServicios, $empresaPlan, $fechaInicio, $fechaFin, $responsableActividad, $observaciones, $observacionesCierre, $tipoActividad);				
					} else {
						echo json_encode("o__0");
					}
					break;

					case "obtenerActividades":
						require_once(rutaBase . 'php' . DS . 'model' . DS . 'model_calendarioActividades.php');
						$actividades = model_calendarioActividades::obtenerActividades();
						echo $actividades;
						break;

						case "obtenerDetallesActividad":
							$actividadId = $_POST['actividadId'];	
							require_once(rutaBase . 'php' . DS . 'model' . DS . 'model_calendarioActividades.php');
							$detalleActividad = model_calendarioActividades::obtenerDetallesActividad($actividadId);
							echo json_encode($detalleActividad);
							break;
					
						case "editarActividad":
							$actividadId = $_POST['actividadId'];
							$unidadServicios = $_POST['unidadServiciosEditar'];
							$empresaPlan = $_POST['empresaPlanEditar'];
							$fechaInicio = $_POST['fechaInicioEditar'];
							$fechaFin = $_POST['fechaFinEditar'];
							$responsableActividad = $_POST['responsableActividadEditar'];
							$observaciones = $_POST['observacionesEditar'];
							$observacionesCierre = $_POST['observacionesCierreEditar'];
							$tipoActividad = $_POST['tipoActividadEditar'];
						
							// Validaciones y sanitizaciones para los nuevos datos, si es necesario
						
							require_once(rutaBase . 'php' . DS . 'model' . DS . 'model_calendarioActividades.php');
							echo model_calendarioActividades::editarActividad($actividadId, $unidadServicios, $empresaPlan, $fechaInicio, $fechaFin, $responsableActividad, $observaciones, $observacionesCierre, $tipoActividad);
							break;

						case "eliminarActividad":
							$actividadId = $_POST['actividadId'];
							require_once(rutaBase . 'php' . DS . 'model' . DS . 'model_calendarioActividades.php');
							echo model_calendarioActividades::eliminarActividad($actividadId);
							break;			
			}
		} else {
			echo json_encode('0_o');
		}
	}
}
