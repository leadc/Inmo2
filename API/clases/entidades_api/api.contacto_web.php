<?php

use BasicORM\LOGS\Log;
use Slim\Http\Request;

require_once 'clases/entidades/class.contacto_web.php';

class API_Contacto_web{

    /** GuardarMensaje
     * 
     */
    public static function GuardarMensaje(Request $request, $response){
        
        // Obtengo los datos del mensaje
        $mensaje = (object)$request->getParsedBody();
        // Busco el archivo recibido

        // Guardo el contacto web en la base de datos
        $cod_mensaje = Contacto_web::NuevoContacto($mensaje, $archivo);
        if($cod_mensaje != false){
            \ob_clean();
            return $response->withJson($cod_mensaje, 200, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
        }else{
            //Log::WriteLog(MAIN_LOG);
           // Log::WriteLog(MAIN_LOG, [$mensaje]);
            return $response->withJson("Error al insertar mensaje", 500);
        }
    }
    

    public static function GuardarMensajeFeedBack(Request $request, $response) {

        $mensaje = (object)$request->getParsedBody();

        $cod_mensaje = Contacto_web::NuevoContactoFeed($mensaje);
        if ($cod_mensaje != false) {
            \ob_clean();
            return $response->withJson($cod_mensaje, 200, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
        } else {
            return $response->withJson("Error al insertar mensaje", 500);
        }
    }
}

?>