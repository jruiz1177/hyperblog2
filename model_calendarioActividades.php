<?php
require(rutaBase . 'php/vendor/autoload.php');

class model_calendarioActividades
{
    public static function guardarActividad(
        $unidadServicios,
        $empresaPlan,
        $fechaInicio,
        $fechaFin,
        $responsableActividad,
        $observaciones,
        $observacionesCierre,
        $tipoActividad
    ) {
        require_once(rutaBase . 'php' . DS . 'conexion' . DS . 'conexion.php');
        $conexion = new conexion();
        $connPgsql = $conexion->conectar();
        ini_set('display_errors', 0);

        $unidadServicios = pg_escape_string($connPgsql, mb_strtoupper($unidadServicios, "UTF-8"));
        $empresaPlan = pg_escape_string($connPgsql, mb_strtoupper($empresaPlan, "UTF-8"));
        $fechaInicio = date('Y-m-d H:i:s', strtotime($fechaInicio));
        $fechaFin = date('Y-m-d H:i:s', strtotime($fechaFin));
        $responsableActividad = pg_escape_string($connPgsql, mb_strtoupper($responsableActividad, "UTF-8"));
        $observaciones = pg_escape_string($connPgsql, mb_strtoupper($observaciones, "UTF-8"));
        $observacionesCierre = pg_escape_string($connPgsql, mb_strtoupper($observacionesCierre, "UTF-8"));
        $tipoActividad = pg_escape_string($connPgsql, mb_strtoupper($tipoActividad, "UTF-8"));


        $sql = "INSERT INTO calendario_actividades(unidadservicios, empresaplan, fechainicio, fechafin, responsableactividad, observaciones, observacionescierre, tipoactividad)
        VALUES ('$unidadServicios', '$empresaPlan', '$fechaInicio', '$fechaFin', '$responsableActividad', '$observaciones', '$observacionesCierre', '$tipoActividad') RETURNING id;";

        try {
            $rtaWeb = pg_query($connPgsql, $sql);
            if ($rtaWeb) {
                $idActividad = pg_fetch_array($rtaWeb)['id'];
                $respuesta = array(
                    'status' => '1',
                    'mensaje' => 'Actividad guardada con éxito. ID de Actividad: ' . $idActividad
                );
            } else {
                $respuesta = array(
                    'status' => '2',
                    'mensaje' => 'Error al guardar la actividad.'
                );

            }
        } catch (\Throwable $th) {
            $respuesta = array(
                'status' => '3',
                'mensaje' => $th->getMessage() . ' ' . pg_last_error($connPgsql)
            );
        }

        pg_close($connPgsql);
        return json_encode($respuesta);
    }
    public static function obtenerActividades()
    {
        require_once(rutaBase . 'php' . DS . 'conexion' . DS . 'conexion.php');
        $conexion = new conexion();
        $connPgsql = $conexion->conectar();
        ini_set('display_errors', 0);
    
        $sql = "SELECT id, tipoactividad, fechainicio, fechafin FROM calendario_actividades";
    
        try {
            $rtaWeb = pg_query($connPgsql, $sql);
    
            if ($rtaWeb) {
                $actividades = array();
    
                while ($row = pg_fetch_assoc($rtaWeb)) {
                    // Formatear las fechas al formato ISO 8601
                    $row['fechainicio'] = date('c', strtotime($row['fechainicio']));
                    $row['fechafin'] = date('c', strtotime($row['fechafin']));
    
                    $actividades[] = $row;
                }
    
                $respuesta = array(
                    'status' => '1',
                    'actividades' => $actividades
                );
            } else {
                $respuesta = array(
                    'status' => '2',
                    'mensaje' => 'Error al obtener las actividades.'
                );
            }
        } catch (\Throwable $th) {
            $respuesta = array(
                'status' => '3',
                'mensaje' => $th->getMessage() . ' ' . pg_last_error($connPgsql)
            );
        }
    
        pg_close($connPgsql);
        return json_encode($respuesta);
    }

    public static function obtenerDetallesActividad($actividadId)
    {
        require_once(rutaBase . 'php' . DS . 'conexion' . DS . 'conexion.php');
        $conexion = new conexion();
        $connPgsql = $conexion->conectar();
        ini_set('display_errors', 0);
    
        $sql = "SELECT * FROM calendario_actividades WHERE id = $actividadId";
    
        try {
            $rtaWeb = pg_query($connPgsql, $sql);
    
            if ($rtaWeb) {
                $detallesActividad = pg_fetch_assoc($rtaWeb);
    
                // Formatear las fechas al formato ISO 8601
                $detallesActividad['fechainicio'] = date('c', strtotime($detallesActividad['fechainicio']));
                $detallesActividad['fechafin'] = date('c', strtotime($detallesActividad['fechafin']));
    
                $respuesta = array(
                    'status' => '1',
                    'detalle' => $detallesActividad
                );
            } else {
                $respuesta = array(
                    'status' => '2',
                    'mensaje' => 'Error al obtener los detalles de la actividad.'
                );
            }
        } catch (\Throwable $th) {
            $respuesta = array(
                'status' => '3',
                'mensaje' => $th->getMessage() . ' ' . pg_last_error($connPgsql)
            );
        }
    
        pg_close($connPgsql);
        return $respuesta;
    }

    public static function eliminarActividad($actividadId)
    {
        require_once(rutaBase . 'php' . DS . 'conexion' . DS . 'conexion.php');
        $conexion = new conexion();
        $connPgsql = $conexion->conectar();
    
        $actividadId = pg_escape_string($connPgsql, $actividadId);
    
        $sql = "DELETE FROM calendario_actividades WHERE id = '$actividadId'";
    
        try {
            $resultado = pg_query($connPgsql, $sql);
    
            if ($resultado) {
                $respuesta = array(
                    'status' => '1',
                    'mensaje' => 'Actividad eliminada con éxito.'
                );
            } else {
                $respuesta = array(
                    'status' => '2',
                    'mensaje' => 'Error al eliminar la actividad.'
                );
            }
        } catch (\Throwable $th) {
            $respuesta = array(
                'status' => '3',
                'mensaje' => $th->getMessage() . ' ' . pg_last_error($connPgsql)
            );
        }
    
        pg_close($connPgsql);
        return json_encode($respuesta);
    }

    public static function editarActividad($actividadId, $unidadServicios, $empresaPlan, $fechaInicio, $fechaFin, $responsableActividad, $observaciones, $observacionesCierre, $tipoActividad)
    {
        require_once(rutaBase . 'php' . DS . 'conexion' . DS . 'conexion.php');
        $conexion = new conexion();
        $connPgsql = $conexion->conectar();
    
        $unidadServicios = pg_escape_string($connPgsql, mb_strtoupper($unidadServicios, "UTF-8"));
        $empresaPlan = pg_escape_string($connPgsql, mb_strtoupper($empresaPlan, "UTF-8"));
        $fechaInicio = date('Y-m-d H:i:s', strtotime($fechaInicio));
        $fechaFin = date('Y-m-d H:i:s', strtotime($fechaFin));
        $responsableActividad = pg_escape_string($connPgsql, mb_strtoupper($responsableActividad, "UTF-8"));
        $observaciones = pg_escape_string($connPgsql, mb_strtoupper($observaciones, "UTF-8"));
        $observacionesCierre = pg_escape_string($connPgsql, mb_strtoupper($observacionesCierre, "UTF-8"));
        $tipoActividad = pg_escape_string($connPgsql, mb_strtoupper($tipoActividad, "UTF-8"));
    
        $sql = "UPDATE calendario_actividades SET
                unidadservicios = '$unidadServicios',
                empresaplan = '$empresaPlan',
                fechainicio = '$fechaInicio',
                fechafin = '$fechaFin',
                responsableactividad = '$responsableActividad',
                observaciones = '$observaciones',
                observacionescierre = '$observacionesCierre',
                tipoactividad = '$tipoActividad'
                WHERE id = '$actividadId'";
    
        try {
            $resultado = pg_query($connPgsql, $sql);

            if ($resultado) {
                $respuesta = array(
                    'status' => '1',
                    'mensaje' => 'Actividad editada con éxito.'
                );
            } else {
                $respuesta = array(
                    'status' => '2',
                    'mensaje' => 'Error al editar la actividad.'
                );
            }
        } catch (\Throwable $th) {
            $respuesta = array(
                'status' => '3',
                'mensaje' => $th->getMessage() . ' ' . pg_last_error($connPgsql)
            );
        }

        pg_close($connPgsql);
        return json_encode($respuesta);
    }
}

?>