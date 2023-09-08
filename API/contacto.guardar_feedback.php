<?php
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: Accept, Content-Type, token");
    //header("Content-Type: multipart/form-data");
    //header('Content-Type : application/json');
    header("Content-Type: application/json; charset=utf-8");

    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;
   //use BasicORM\LOGS\Log;
    
    require 'vendor/autoload.php';
    
    // Middeware
    require_once 'clases/middleware/mw.autentificador_jwt.php';
    require_once 'clases/middleware/mw.cors.php';
    require_once 'clases/middleware/mw.media_type_parser.php';

    // Clases API
    require_once 'clases/entidades_api/api.contacto_web.php';

    //GENERAR DOCUMENTACION APIDOC PARA USO CON apidoc -o doc_para_uso -f "API.php$" -t apidoc-template

    $config['displayErrorDetails'] = true; //para obtener información sobre los errores
    $config['addContentLengthHeader'] = false;  //permite al servidor web establecer el encabezado Content-Length, lo que hace que Slim se comporte de manera más predecible

    $app = new \Slim\App(["settings" => $config]);

    /**
     * @api {post} contacto.guardar_mensaje.php Guarda un nuevo mensaje
     * @apiVersion 0.1.0
     * @apiName contacto_web
     * @apiGroup contacto_web
     * @apiPermission Todos
     *
     * @apiDescription Guarda un nuevo mensaje de cliente desde la página web e informa a los responsables del mismo
     *
     * @apiParam {nombre} Nombre del cliente
     * @apiParam {empresa} Empresa
     * @apiParam {mail} Mail del cliente
     * @apiParam {telefono} Teléfono del cliente
     * @apiParam {mensaje} Mensaje del cliente
     * 
     * @apiSuccess {codigo_de_mensaje}  Datos de la reserva realizada
     *
     * @apiError Error_Interno No se pudo guardar el mensaje
     */
    $app->post('/', \API_Contacto_web::class . ':GuardarMensajeFeedBack' );
    
    $app->run();

?>