<?php
use Slim\Http\Request;

require_once 'clases/entidades/class.inmuebles.php'; // Asegúrate de que esta ruta sea correcta
use BasicORM\LOGS\Log;

class API_Inmuebles {

    public static function ObtenerInmuebles($request, $response) {
        try {
            $inmuebles = Inmuebles::ObtenerInmuebles();
            return $response->withJson($inmuebles, 200, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return $response->withJson("Error al obtener los inmuebles" . $e, 500);
        }
    }

    public static function ObtenerInmueblePorId($request, $response) {
        try {
            $queryParams = $request->getQueryParams();
            $id = $queryParams['id'];
    
            $inmueble = Inmuebles::ObtenerInmueblePorId($id);
    
            if ($inmueble) {
                $jsonData = json_encode($inmueble, JSON_UNESCAPED_UNICODE);
                Log::WriteLog(MAIN_LOG, ["ID del inmueble: $id", "Respuesta JSON enviada al cliente: $jsonData"]);
                return $response->withJson($inmueble, 200, JSON_UNESCAPED_UNICODE);
            } else {
                return $response->withJson("Inmueble no encontrado", 404);
            }
        } catch (Exception $e) {
            return $response->withJson("Error al obtener el inmueble: " . $e->getMessage(), 500);
        }
    }

    public static function ObtenerPropiedadesDestacadas($request, $response) {
        try {
            $propiedadesDestacadas = Inmuebles::ObtenerPropiedadesDestacadas();
            return $response->withJson($propiedadesDestacadas, 200, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return $response->withJson("Error al obtener las propiedades destacadas" . $e, 500);
        }
    }

    public static function ObtenerPropiedadesRelacionadas($request, $response) {
        try {
          $id = $request->getQueryParam('id');
          $propiedadesRelacionadas = Inmuebles::ObtenerPropiedadesRelacionadas($id);
          return $response->withJson($propiedadesRelacionadas, 200, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
          return $response->withJson("Error al obtener las propiedades relacionadas" . $e, 500);
        }
      }

      public static function ObtenerUbicaciones($request, $response) {
        try {
          $ubicaciones = Inmuebles::ObtenerUbicaciones(); // Asegúrate de tener el método en tu clase Ubicaciones
          return $response->withJson($ubicaciones, 200, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
          return $response->withJson("Error al obtener las ubicaciones" . $e, 500);
        }
      }

      public static function GuardarPropiedad(Request $request, $response) {
        $formData = $request->getParsedBody();
    
        // Verifica si el formulario contiene el campo 'images'


          Log::WriteLog(MAIN_LOG, [json_encode($formData)]);

        if (isset($formData['images']) && is_array($formData['images'])) {
            $resultado = Inmuebles::NuevoInmueble($formData);
    
            if ($resultado) {
                // La propiedad se guardó exitosamente, devuelve una respuesta JSON con éxito
                return $response->withJson(["mensaje" => "La propiedad se guardó exitosamente"], 200);
            } else {
                // Hubo un error al guardar la propiedad, devuelve una respuesta JSON de error
                return $response->withJson(["error" => "No se pudo guardar la propiedad"], 500);
            }
        } else {
            // Maneja el caso en el que 'images' no está presente en el formulario
            return $response->withJson(["error" => "El campo 'images' es requerido"], 400);
        }
    }

      //guardar propiedad
}
