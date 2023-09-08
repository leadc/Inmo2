<?php
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: Origin, Content-Type, token");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Allow: GET, POST, OPTIONS, PUT, DELETE");
    //header('Content-Type : application/json');
    //header("Content-Type: application/json; charset=utf-8");

    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;
    use BasicORM\Encuestas\EncuestaRealizada;
    use BasicORM\Encuestas\RespuestaEncuesta;

    require 'vendor/autoload.php';
    
    // Middeware
    require_once 'clases/middleware/mw.autentificador_jwt.php';
    require_once 'clases/middleware/mw.cors.php';
    require_once 'clases/middleware/mw.media_type_parser.php';

    // Clases API
    require_once 'clases/entidades_api/api.usuario.php';
    require_once 'clases/entidades_api/api.plantas.php';

    //GENERAR DOCUMENTACION APIDOC PARA USO CON apidoc -o doc_para_uso -f "API.php$" -t apidoc-template

    $config['displayErrorDetails'] = true; //para obtener información sobre los errores
    $config['addContentLengthHeader'] = false;  //permite al servidor web establecer el encabezado Content-Length, lo que hace que Slim se comporte de manera más predecible

    $app = new \Slim\App(["settings" => $config]);

    $app->post('/', function ($request, $response){

        try{
            $datos = json_decode($request->getBody()->getContents());
            if(!isset($datos->encuesta)
                || !isset($datos->encuesta->fechaITV)
                || !isset($datos->encuesta->patente)
                || !isset($datos->encuesta->planta)
                || !isset($datos->encuesta->respuestas)
                ){
                return $response->withJson("Falta enviar encuesta",401, JSON_UNESCAPED_UNICODE);
            }
            $nombre = null;
            if(isset($datos->encuesta->nombre)){
                $nombre = $datos->encuesta->nombre;
            }

            $encuesta = EncuestaRealizada::CrearEncuesta($nombre,$datos->encuesta->patente,$datos->encuesta->fechaITV,$datos->encuesta->planta,$datos->encuesta->respuestas);

            return $response->withJson($encuesta, 200, JSON_UNESCAPED_UNICODE);
        }catch(\Exception $e){
            return $response->withJson("Error al guardar encuesta, por favor vuelva a intentarlo luego", 500, JSON_UNESCAPED_UNICODE);
        }
    } );

    $app->run();
    


    /**
     * @api {Vista en Base de Datos} VW_LISTADO_PLANTAS Listado de plantas
     * @apiVersion 0.1.0
     * @apiName VW_LISTADO_PLANTAS
     * @apiGroup Base de datos
     *
     * @apiDescription VIEW VW_LISTADO_PLANTAS AS SELECT (SELECT a.id, a.codigo, a.nombre, a.imagen, a.telefono AS nro_contacto, b.direccion, b.lat, b.lng, b.ciudad FROM dbo.TPlantas AS a INNER JOIN dbo.TDirecciones_Plantas AS b ON a.id = b.cod_planta AND GETDATE() BETWEEN b.fecha_desde AND b.fecha_hasta FOR JSON PATH) AS plantas
     *
     */

    /**
     * @api {Vista en Base de Datos} VW_CRONOGRAMA_PLANTAS_MOVILES Cronograma de plantas móviles
     * @apiVersion 0.1.0
     * @apiName VW_CRONOGRAMA_PLANTAS_MOVILES
     * @apiGroup Base de datos
     *
     * @apiDescription VW_CRONOGRAMA_PLANTAS_MOVILES AS SELECT (SELECT a.codigo, a.nombre, cronograma = (SELECT b.ciudad, b.direccion, b.lat, b.lng, b.fecha_desde, b.fecha_hasta FROM TDirecciones_Plantas AS b WHERE a.id = b.cod_planta AND b.fecha_hasta > getdate() FOR JSON PATH) FROM TPlantas AS a WHERE a.tipo = 'movil' FOR JSON AUTO) AS cronogramas)
     *
     */

    
 
    // * @api {post} /plantas/listado_de_plantas Read data of a User
    // * @apiVersion 0.3.0
    // * @apiName URUGUAY - WEBAPI
    // * @apiGroup User
    // * @apiPermission admin
    // *
    // * @apiDescription Compare Verison 0.3.0 with 0.2.0 and you will see the green markers with new items in version 0.3.0 and red markers with removed items since 0.2.0.
    // *
    // * @apiParam {String} id The Users-ID.
    // *
    // * @apiExample Example usage:
    // * curl -i http://localhost/user/4711
    // *
    // * @apiSuccess {String}  PARAMETRO DESCIPCION DE PARAMETRO
    // *
    // * @apiError NOMBRE DESCRIPCION ERROR

?>