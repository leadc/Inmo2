<?php
    require_once 'clases/entidades/class.usuario.php';
    use BasicORM\LOGS\Log;
    /** API_Usuario
     * Clase para el manejo de usuarios desde el servidor WEB
     */
    class API_Usuario{

        /** Login
         * Código Necesario para realizar el login de un usuario 
         * Retornará los datos del usuario si la clave y el nombre recibidos son correctos
         * Retornará un error 401 de autenticación en caso de que la clave y el usuario sean incorrectos o el usuario no exista
         * Retornará un error 400 en caso de que no se reciba usuario o clave
         * Retornará un error 500 en caso de que se produzca algún error (Conexión a base de datos o de otro tipo)
         */
        public static function Login($request, $response){
            $respuesta = new stdclass();
            try{
                $datos_recibidos = json_decode($request->getBody()->getContents());

                /*$file = fopen("c:/log.txt","a");
                fwrite($file,"\r\nDENTRO DE LA FUNC LOGIN: ".json_encode($datos_recibidos) ."\r\n");
                fclose($file);*/
                if(isset($datos_recibidos->clave) and isset($datos_recibidos->nombre)){
                    //Datos recibidos por request
                    $nombre_usuario = $datos_recibidos->nombre;
                    $clave_usuario = $datos_recibidos->clave;
               
                    //Verifico que el usuario exista en la base de datos con GetUsuarioByNombre
                    $usuario = new Usuario();
                    if($usuario->GetUsuarioByUsuario($nombre_usuario)){
                        //El usuario existe
                        
                        if($usuario->VerificarClave($clave_usuario)){
                            //La clave es correcta
                            $respuesta->usuario = $usuario;
                            return $response->withJson($usuario, 200);
                        }else{
                            //La clave no es correcta
                            $respuesta->error = "Usuario o clave incorrectos";
                            return $response->withJson($respuesta, 401);
                        }
                    }else{
                        //El usuario no existe
                        $respuesta->error = "Usuario inexistente";
                        return $response->withJson($respuesta, 401);
                    }
                }else{
                    //Los parámetros no son correctos
                    $respuesta->error = "Parametros recibidos incorrectos";
                    return $response->withJson($respuesta, 400);
                }
            }catch(Exception $e){
                $respuesta->error = $e->message;
                return $response->withJson($respuesta, 500);
            }
        }

        /** CrearUsuario
         * [POST]
         * Crea un nuevo usuario en la base de datos e informa al mail del mismo su clave de acceso
         */
        public static function CrearUsuario($request, $response){
            $respuesta = new stdclass();
            $datos_recibidos = json_decode($request->getBody()->getContents());
            //Valido los datos recibidos
            if(!isset($datos_recibidos->usuario) || !isset($datos_recibidos->flota)){
                $respuesta->error = "Parametros recibidos incorrectos";
                return $response->withJson($respuesta, 400);
            }
            //Guardo el nuevo usuario
            $resultado = Usuario::NuevoUsuario($datos_recibidos->usuario, $datos_recibidos->flota->id_flota);
            if(!is_numeric($resultado)){
                //En caso de error devuelvo el mensaje
                $respuesta->error = $resultado;
                return $response->withJson($respuesta, 400);
            }
            return $response->withJson($resultado, 200);
        }

        /** EliminarUsuario
         * [POST]
         * Elimina un usuario de flotas
         */
        public static function EliminarUsuario($request, $response){
            $datos_recibidos = json_decode($request->getBody()->getContents());
            $respuesta = new stdClass();
            if(!isset($datos_recibidos->id_usuario)){
                $respuesta->error = "Parametros recibidos incorrectos";
                return $response->withJson($respuesta, 400);
            }
            $resultado = Usuario::EliminarUsuario($datos_recibidos->id_usuario);
            if($resultado !== true){
                $respuesta->error = $resultado;
                return $response->withJson($respuesta, 500);
            }
            return $response->withJson('Usuario eliminado con éxito.', 200);
        }

        /** RecordarCredenciales
         * [POST]
         * Envía un mail recordatorio de credenciales al cliente con su usuario y contraseña
         * Devuelve un mensaje de error en caso de haberlo o un mensaje de confirmación 
         */
        public static function RecordarCredenciales($request, $response){
            $datos_recibidos = json_decode($request->getBody()->getContents());
            $respuesta = new stdClass();
            if(!isset($datos_recibidos->id_usuario)){
                $respuesta->error = "Parametros recibidos incorrectos";
                return $response->withJson($respuesta, 400);
            }

            $user = new Usuario();
            if($user->GetUsuarioByID($datos_recibidos->id_usuario) === true){
                $resultado = $user->RefrescarClave();
                if($resultado !== true){
                    $respuesta->error = $resultado;
                    return $response->withJson($respuesta, 500);
                }
                return $response->withJson('Clave generada con éxito', 200);
            }else{
                $respuesta->error = "El mail ingresado no corresponde a un usuario registrado";
                return $response->withJson($respuesta, 404);
            }
        }

    }
?>