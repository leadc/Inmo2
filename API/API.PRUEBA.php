<?php
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: Origin, Content-Type, token");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Allow: GET, POST, OPTIONS, PUT, DELETE");
    //header('Content-Type : application/json');
    header("charset=utf-8");

    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;
    
    require 'vendor/autoload.php';
    // Acceso a datos
    require_once 'clases/acceso_datos/DB_Conexion.php';

    // Middeware
    require_once 'clases/middleware/mw.autentificador_jwt.php';
    require_once 'clases/middleware/mw.cors.php';
    require_once 'clases/middleware/mw.media_type_parser.php';

    // Clases API
    require_once 'clases/entidades_api/api.usuario.php';
    require_once 'clases/entidades_api/api.plantas.php';

    //GENERAR DOCUMENTACION APIDOC PARA USO CON apidoc -o doc_para_uso -f "API.php$" -t apidoc-template

    $config['displayErrorDetails'] = true; //para obtener información sobre los errores
    $config['addContentLengthHeader'] = true;  //permite al servidor web establecer el encabezado Content-Length, lo que hace que Slim se comporte de manera más predecible

    $app = new \Slim\App(["settings" => $config]);

    /**
     * @api {get} /plantas/reservar_turno Reserva un turno en la planta
     * @apiVersion 0.1.0
     * @apiName reservar turno
     * @apiGroup Plantas
     * @apiPermission Todos
     *
     * @apiDescription Ejecuta la reserva de un turno enviado por parámetro
     *
     * @apiParam {Reserva} reserva objeto con los datos que se deben recibir.
     * @apiParam {Reserva.vahiculo} Datos del vehículo
     * @apiParam {Reserva.cliente} Datos del cliente
     * @apiParam {Reserva.turno} Datos del turno seleccionado
     * 
     * @apiSuccess {Reserva}  Datos de la reserva realizada
     * @apiSuccess {Reserva.codigo_reserva}  Codigo de reserva generado
     *
     * @apiError Error_Interno No se pudo crear obtener los turnos debido a un error interno en el servidor 
     */
    $app->get('/', \API_Plantas::class . ':PRUEBA' );
    $app->post('/', \API_Plantas::class . ':PRUEBAPOST' );
    
    $app->run();
?>