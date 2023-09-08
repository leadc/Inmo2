<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Accept, Content-Type, token");
header("Content-Type: application/json; charset=utf-8");

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require 'vendor/autoload.php';
require_once 'clases/middleware/mw.autentificador_jwt.php';
require_once 'clases/middleware/mw.cors.php';
require_once 'clases/middleware/mw.media_type_parser.php';
require_once 'clases/entidades_api/api.inmuebles.php';

$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

$app = new \Slim\App(["settings" => $config]);

$app->get('/', \API_Inmuebles::class . ':ObtenerPropiedadesDestacadas' );

$app->run();
?>
