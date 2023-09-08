<?php

require_once 'clases/acceso_datos/DB_Conexion.php';

/** Clase instanciable usada para el manejo de usuarios
 * Métodos utilizables: 
 *  - GetUsuarioByID
 *  - GetUsuarioByNombre
 *  - VerificarClave
 */
class Usuario{
    public $id;
    public $nombre;
    public $usuario;
    public $rut;
    public $id_flota;
    public $email_usuario;
    private $clave;

    public function __construct($id ="", $nombre ="", $clave =""){
        $this->id = $id;
        $this->nombre = $nombre;
        $this->clave = $clave;
    }

    /** GetUsuarioByID
     * Obtiene los datos de un usuario según su ID
     * Devuelve true en caso de éxito, false en caso de no encontrar coincidencias
     */
    public function GetUsuarioByID($id){
        return $this->GetUsuarioBy("ID",$id);
    }

    /** GetUsuarioByNombre
     * Obtiene los datos de un usuario según su Nombre
     * Devuelve true en caso de éxito, false en caso de no encontrar coincidencias
     */
    public function GetUsuarioByUsuario($usuario){
        return $this->GetUsuarioBy("usuario",$usuario);
    }

    /** GetUsuarioByMail
     * Obtiene los datos de un usuario según su mail
     * Devuelve true en caso de éxito, false en caso de no encontrar coincidencias
     */
    public function GetUsuarioByMail($mail){
        return $this->GetUsuarioBy("email_usuario",$mail);
    }

    /** GetUsuarioByRut
     * Obtiene los datos de un usuario según su Nombre
     * Devuelve true en caso de éxito, false en caso de no encontrar coincidencias
     */
    public function GetUsuarioByRUT($rut){
        return $this->GetUsuarioBy("rut",$rut);
    }

    /** GetUsuarioBy
     * Obtiene los datos de un usuario según la clave y el valor pasados por parámetro
     * Devuelve true en caso de éxito, false en caso de no encontrar coincidencias
     */
    private function GetUsuarioBy($clave, $valor){
        $resultado = ConecxionPDO::GetResultados("SELECT 
                                                    us.id,
                                                    Fl.nombre_flota as nombre_cliente,
                                                    us.usuario,
                                                    Fl.rut,
                                                    us.clave,
                                                    Fl.id_flota,
                                                    us.email_usuario
                                                    from UsuariosFlotas us, Flotas Fl
                                                    where us.id_flota = Fl.id_flota
                                                    and ".$clave." = '".$valor."'");
        if(count($resultado) > 0){
            $this->id = $resultado[0][0];
            $this->nombre = $resultado[0][1];
            $this->usuario = $resultado[0][2];
            $this->rut = $resultado[0][3];
            $this->clave = $resultado[0][4];
            $this->id_flota = $resultado[0][5];
            $this->email_usuario = $resultado[0][6];
            return true;
        }else{
            return false;
        }
    }

    /** VerificarClave
     * Compara una clave pasada por parámetro con la guardada en la clave del usuario
     * retorna true en caso de que sea correcta o false de no serlo
     */
    public function VerificarClave($clave){
        if($this->id != "" and $this->clave != "" and $clave != ""){          
            if($clave == $this->clave){
                return true;
            }
        }
        return false;
    }

    /** NuevoUsuario
     * Guarda los datos de un nuevo usuario e informa 
     */
    public static function NuevoUsuario($usuario, $id_flota, $informar_usuario = true){
        try{
            $clave = self::GenerarClave();
            $sql = "INSERT INTO UsuariosFlotas (usuario, clave, email_usuario, id_flota) VALUES('$usuario->usuario', '$clave', '$usuario->email_usuario', $id_flota)";
            $user = new Usuario();
            //Verifico que no exista el mismo usuario
            if($user->GetUsuarioByUsuario($usuario->usuario) === true){
                return 'Nombre de usuario ya usado';
            }
            //Verifico que no exista el mismo mail para otro usuario
            /*if($user->GetUsuarioByMail($usuario->email_usuario) === true){
                return 'Email de usuario ya usado';
            }*/
            //Inserto el usuario
            $resultado = ConecxionPDO::ExecNonQuery($sql);
            //Evalúo si se insertaron filas
            if($resultado <= 0){
                return 'No se pudo guardar el usuario';
            }
            //Obtengo el id del último usuario insertado
            $id_nuevo_usuario = ConecxionPDO::RetornarUltimoIdInsertado();
            //Informar al mail del cliente su nuevo usuario
            if($informar_usuario){
                self::InformarUsuario($usuario->email_usuario,$usuario->usuario,$clave);
            }
            //Devuelvo el id del último usuario insertado
            return $id_nuevo_usuario;
        }catch(Exception $e){
            return 'Se produjo un error al tratar de guardar el usuario';
        }
    }

    /** RefrescarClave
     * Vuelve a generar una clave de usuario y la envía al cliente según el mail registrado
     */
    public function RefrescarClave(){
        try{
            $this->clave = Usuario::GenerarClave();
            $sql = "UPDATE UsuariosFlotas SET clave = '$this->clave' WHERE id = $this->id";
            if(ConecxionPDO::ExecNonQuery($sql) > 0){
                Usuario::InformarUsuario($this->email_usuario,$this->usuario,$this->clave);
                return true;
            }
            return 'No se pudo guardar la nueva clave';
        }catch(Exception $e){
            return 'Error al volver a generar clave';
        }

    }

    /** GenerarClave
     * Genera una clave aleatoria de la cantidad de caracteres deseados (por defecto 6)
     */
    public static function GenerarClave($digitos=6){
        $caracteres = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!$$?";
        $pass = '';
        do{
            $pass = $pass.$caracteres[rand(0,strlen($caracteres)-1)];
        }while(strlen($pass)<$digitos);
        return $pass;
    }

    /** InformarUsuario
     * Informa al nuevo usuario (o uno existente) sus credenciales para ingreso a la página
     */
    public static function InformarUsuario($mail, $usuario, $clave){

        $cuerpo = "<div style='max-width:745px; margin:auto'>".
                "<style type='text/css'>.Estilo5 {font-family: Calibri;font-size: 14px;}.Estilo6 {font-family: Calibri;font-size: 14px;color: #CD4E01;}.Estilo7 {font-family: Calibri;font-size: 14px;font-weight: bold;color: #CD4E01;}.Estilo11 {font-family: Calibri;font-size: 14px;font-weight: bold;}</style>".
                "<img style='width:100%' src='http://190.210.214.156:11360/webchile/media_responsive/img/encabezadoMailReserva.jpg' longdesc='http://www.applusitv.uy'>".
                "<p class='Estilo5'>Estimado usuario, <br><br>Nos ponenmos en contacto para acercarle sus credenciales de acceso para consulta de flotas en nuestra p&aacute;gina web:</p>".
                "<br>".
                "Usuario: $usuario<br>".
                "Clave: $clave<br>".
                "<p class='Estilo5'>Ante cualquier duda puede ponerse en contacto con nosotros desde el formulario de contacto en nuestro sitio.</p>".
                "<br><br>".
                "<span class='Estilo5'> Muchas gracias,</span><br>".
                "<span class='Estilo7'>El equipo de Applus ITV</span><br>".
                "</div>";

        $asunto = "ITV - Usuario de flota generado";

        self::EnviarMailGenerico($mail, $asunto, $cuerpo);
    }

    public static function EliminarUsuario($id_usuario){
        try{
            $sql = "DELETE FROM UsuariosFlotas WHERE id = $id_usuario";
            if(ConecxionPDO::ExecNonQuery($sql) > 0){
                return true;
            }
            return "No se encontró el usuario a eliminar";
        }catch(Exception $e){
            return "Ocurrió un error al eliminar el usuario";
        }
    }

    /** EnviarMailGenerico
     * Envía un mail genérico usando la cuenta configurada
     */
    public static function EnviarMailGenerico($destino, $asunto, $cuerpo){
        //$cmd = 'C:/Mailer_uru.exe "turnos.auto.ar@applusglobal.com" "Jd_k9!?J01" "'.$destino.'" "'.$asunto.'" "'.$cuerpo.'"';
        $cmd = 'C:/Mailer.exe "turnos.auto.uy@applusitv.uy" "NoJodanDeNoche01$" "'.$destino.'" "'.$asunto.'" "'.$cuerpo.'"';
        pclose(popen("start /B ". $cmd, "r")); 
    }
}
