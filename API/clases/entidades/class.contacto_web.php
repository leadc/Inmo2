<?php

use BasicORM\LOGS\Log;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once 'clases/acceso_datos/DB_Conexion.php';

/** Clase instanciable usada para el manejo de contactos desde la página web
 * Métodos utilizables: 
 *  - GetUsuarioByID
 *  - GetUsuarioByNombre
 *  - VerificarClave
 */
class Contacto_web
{

    public function __construct()
    {
    }

    /** NuevoContacto
     * Guarda un nuevo contacto en la base de datos
     * 
     */
    public static function NuevoContacto($mensaje_recibido, $archivo = null)
    {

        if ($mensaje_recibido->name != null) {
            $nombre  = $mensaje_recibido->name;
        } else {
            return false;
        }
        if ($mensaje_recibido->email != null) {
            $mail  = $mensaje_recibido->email;
        } else {
            return false;
        }
        if ($mensaje_recibido->phone != null) {
            $telefono  = $mensaje_recibido->phone;
        } else {
            return false;
        }
        if ($mensaje_recibido->message != null) {
            $mensaje  = $mensaje_recibido->message;
        } else {
            return false;
        }

  

        $empresa = $mensaje_recibido->empresa;
        $sql_nuevo_mensaje = "INSERT INTO Contactos_web (nombre_completo, mail, telefono, mensaje) VALUES ('$nombre','$mail','$telefono','$mensaje')";
        // 
        $ext_permitidas = array('pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'png');

        

      

        try {
            if (ConecxionPDO::ExecNonQuery($sql_nuevo_mensaje) > 0) {
                // Busco el id insertado
               
                 
                 // Obtén el último ID insertado utilizando SCOPE_IDENTITY()
                 $resultado = ConecxionPDO::GetResultados("SELECT MAX(ID) AS max_id FROM Contactos_web");
                 $maxId = $resultado[0]['max_id'];
                // Creo el código de mensaje
                $codigo_mensaje = strtoupper(substr(sha1($maxId . $mail), 1, 10));
                // Guardo el archivo recibido
                $nombre = '';
                if ($archivo != null) {
                    $nombreArray = explode('.', $archivo->getClientFilename());
                    $extension = $nombreArray[count($nombreArray) - 1];
                    $nombre = "$codigo_mensaje.$extension";
                    if (in_array($extension, $ext_permitidas)) {
                        $archivo->moveTo(__DIR__ . "/../../archivos_contactosweb/$nombre");
                    } else {
                        return false;
                    }
                }
                // Actualizo el registro con el archivo y el código de mensaje
                $sql_update_cod_mensaje = "UPDATE Contactos_web SET cod_mensaje ='$codigo_mensaje', archivo = '$nombre' WHERE id = $maxId";
 
                // Log::WriteLog(MAIN_LOG, [$sql_update_cod_mensaje, "$codigo_mensaje","$id"]);
                // Ejecuto la actualización y envío la información de contactos recibidos
                if (ConecxionPDO::ExecNonQuery($sql_update_cod_mensaje) > 0) {
                    self::EnviarMailConfirmacion($nombre, $mail, $codigo_mensaje);
                    self::InformarNuevoMensaje($nombre, $mail, $codigo_mensaje, $mensaje, $telefono);
                    return $codigo_mensaje;
                }
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }


    public static function NuevoContactoFeed($mensaje_recibido) {
        if ($mensaje_recibido->email != null && $mensaje_recibido->message != null) {
            $mail = $mensaje_recibido->email;
            $mensaje = $mensaje_recibido->message;

            $sql_nuevo_mensaje = "INSERT INTO Mensajes_feed (mail, mensaje) VALUES ('$mail', '$mensaje')";


            try {
                if (ConecxionPDO::ExecNonQuery($sql_nuevo_mensaje) > 0) {
                    self::EnviarMailConfirmacionFeed( $mail);
                    self::InformarNuevoMensajeFeed( $mail, $mensaje);
                    return true;
                }
                return false;
            } catch (Exception $e) {
                return false;
            }
        }
        return false;
    }

    /** EnviarMail
     * Envia el mail de confirmación al cliente
     * 
     */
    private static function EnviarMailConfirmacion($nombre_cliente, $mail_cliente, $codigo)
    {

        $asunto = "Inmobiliaria- Contacto desde la p&aacute;gina web";
        $cuerpo = "<style type='text/css'>.Estilo5 {font-family: Calibri;font-size: 14px;}.Estilo6 {font-family: Calibri;font-size: 14px;color: #CD4E01;}.Estilo7 {font-family: Calibri;font-size: 14px;font-weight: bold;color: #CD4E01;}.Estilo11 {font-family: Calibri;font-size: 14px;font-weight: bold;}</style>" .
            "<img style='width:100% max-height: 90px' src='http://190.210.214.156:11360/webchile/media_responsive/img/encabezadoMailReserva.jpg' longdesc='http://www.applus.com.ar'><br>" .
            "<span class='Estilo11'>Estimado/a $nombre_cliente:</span>" .
            "<p class='Estilo5'>Le agradecemos por comunicarse con nosotros. Responderemos su consulta a la brevedad.</p>" .
            "<span class='Estilo11'>Su c&oacute;digo de contacto es: $codigo</span>" .
            "<br>" .
            "<br>" .
            "<span class='Estilo5'>Saludos.</span><br>" .
            "<span class='Estilo7'>Inmobiliaria</span><br><br>" .
            "<div style='background-color:#fc6500; heigth:3px; width:100%'></div>";
        self::EnviarMail($mail_cliente, $asunto, $cuerpo, '');
    }


    private static function EnviarMailConfirmacionFeed($mail_cliente)
    {

        $asunto = "Inmobiliaria- Contacto desde la p&aacute;gina web";
        $cuerpo = "<style type='text/css'>.Estilo5 {font-family: Calibri;font-size: 14px;}.Estilo6 {font-family: Calibri;font-size: 14px;color: #CD4E01;}.Estilo7 {font-family: Calibri;font-size: 14px;font-weight: bold;color: #CD4E01;}.Estilo11 {font-family: Calibri;font-size: 14px;font-weight: bold;}</style>" .
            "<img style='width:100% max-height: 90px' src='http://190.210.214.156:11360/webchile/media_responsive/img/encabezadoMailReserva.jpg' longdesc='http://www.applus.com.ar'><br>" .
            "<span class='Estilo11'>Estimado/a </span>" .
            "<p class='Estilo5'>Le agradecemos por comunicarse con nosotros. Responderemos su consulta a la brevedad.</p>" .
            "<br>" .
            "<br>" .
            "<span class='Estilo5'>Saludos.</span><br>" .
            "<span class='Estilo7'>Inmobiliaria Monzón</span><br><br>" .
            "<div style='background-color:#fc6500; heigth:3px; width:100%'></div>";
        self::EnviarMail($mail_cliente, $asunto, $cuerpo, '');
    }

    /** InformarNuevoMensaje
     * Informa el nuevo contacto web a los responsables
     * 
     */
    private static function InformarNuevoMensaje($nombre_cliente, $mail_cliente, $codigo, $mensaje_cliente, $telefono_cliente)
    {
        $mensaje_cliente = utf8_decode(str_replace(array("\r", "\n"), "<br>", $mensaje_cliente));
        // $empresa = utf8_decode(str_replace(array("\r", "\n"), "<br>", $empresa));

        $asunto = "Inmobiliaria - Nuevo Mensaje desde la pagina web";
        $cuerpo = "<style type='text/css'>.Estilo5 {font-family: Calibri;font-size: 14px;}.Estilo6 {font-family: Calibri;font-size: 14px;color: #CD4E01;}.Estilo7 {font-family: Calibri;font-size: 14px;font-weight: bold;color: #CD4E01;}.Estilo11 {font-family: Calibri;font-size: 14px;font-weight: bold;}</style>" .
            "<img style='width:100%; max-height: 90px' src='http://190.210.214.156:11360/webchile/media_responsive/img/encabezadoMailReserva.jpg' longdesc='http://www.applus.com.ar'><br>" .
            "<span class='Estilo11'>Nuevo mensaje web:</span>" .
            "<p class='Estilo5'>Hemos recibido un nuevo mensaje a trav&eacute;s de nuestra p&aacute;gina web.</p>" .
            "<span class='Estilo11'>Detalles:</span>" .
            "<ul class='Estilo5'>" .
            "    <li>C&oacute;digo: $codigo</li>" .
            "    <li>Nombre y apellido: $nombre_cliente</li>" .
            "    <li>Empresa: $empresa</li>" .
            "    <li>mail: $mail_cliente</li>" .
            "    <li>Telefono: $telefono_cliente</li>" .
            "    <li>Mensaje: <br>$mensaje_cliente</li>";

        /*      if($nombreArchivo ==! ''){

        Log::WriteLog(MAIN_LOG, ["CORREO:$nombreArchivo--> NOMBRE DEL ARCHIVO<--"]);
    }*/

        $cuerpo = $cuerpo . "</ul>" .
            "<span class='Estilo5'></span><br>" .
            "<span class='Estilo7'>Inmobiliaria</span><br><br>" .
            "<div style='background-color:#fc6500; heigth:3px; width:100%'></div>";

        self::EnviarMail('matii.martini@gmail.com', $asunto, $cuerpo, '');
    }


    private static function InformarNuevoMensajeFeed( $mail_cliente, $mensaje_cliente)
    {
        $mensaje_cliente = utf8_decode(str_replace(array("\r", "\n"), "<br>", $mensaje_cliente));
        $empresa = utf8_decode(str_replace(array("\r", "\n"), "<br>", $empresa));

        $asunto = "Inmobiliaria - Nuevo Mensaje desde la pagina web";
        $cuerpo = "<style type='text/css'>.Estilo5 {font-family: Calibri;font-size: 14px;}.Estilo6 {font-family: Calibri;font-size: 14px;color: #CD4E01;}.Estilo7 {font-family: Calibri;font-size: 14px;font-weight: bold;color: #CD4E01;}.Estilo11 {font-family: Calibri;font-size: 14px;font-weight: bold;}</style>" .
            "<img style='width:100%; max-height: 90px' src='http://190.210.214.156:11360/webchile/media_responsive/img/encabezadoMailReserva.jpg' longdesc='http://www.applus.com.ar'><br>" .
            "<span class='Estilo11'>Nuevo mensaje web:</span>" .
            "<p class='Estilo5'>Hemos recibido un nuevo mensaje a trav&eacute;s de nuestra p&aacute;gina web.</p>" .
            "<span class='Estilo11'>Detalles:</span>" .
            "<ul class='Estilo5'>" .
            "    <li>mail: $mail_cliente</li>" .
            "    <li>Mensaje: <br>$mensaje_cliente</li>";

        /*      if($nombreArchivo ==! ''){

        Log::WriteLog(MAIN_LOG, ["CORREO:$nombreArchivo--> NOMBRE DEL ARCHIVO<--"]);
    }*/


        if ($nombreArchivo == !null) {
            $cuerpo = $cuerpo . "<li>Archivo Cargado: <a href='file://10.98.10.6/c$/Apache24/htdocs/WEB_URUGUAY/Uruguay-WebAPI/archivos_contactosweb/$nombreArchivo'>Click para ver el archivo</a></li>";
        }

        $cuerpo = $cuerpo . "</ul>" .
            "<span class='Estilo5'></span><br>" .
            "<span class='Estilo7'>Inmobiliaria</span><br><br>" .
            "<div style='background-color:#fc6500; heigth:3px; width:100%'></div>";

        self::EnviarMail('matii.martini@gmail.com', $asunto, $cuerpo, '');
    }

    private static function EnviarMail($destino, $asunto, $cuerpo, $altBody)
    {
        try {
            $mail = new PHPMailer(true);
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_OFF;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = 'smtp.sendgrid.net';                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = 'apikey';                     //SMTP username
            $mail->Password   = 'SG.jZ9wOCKdRCiWfUU1j3FShQ.lhk9jhHxO3aGYkllCNwDYOx71QNUw5OAgokvTNDhhFk';                               //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         //Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
            $mail->Port       = 587;                                    //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
            $mail->SMTPSecure = 'tls';
            $mail->setFrom('matias.martini@hotmail.com', 'Inmobiliaria');
            $mail->addAddress($destino, $destino);     //Add a recipient
            $mail->SMTPOptions = array(
                'tls' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = $asunto;
            $mail->Body    = $cuerpo;
            $mail->AltBody = $altBody;

            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}

   /* private static function EnviarMail($destino, $asunto, $cuerpo, $altBody){
		try {
			$mail = new PHPMailer(true);
			//Server settings
			$mail->SMTPDebug = SMTP::DEBUG_OFF;                      //Enable verbose debug output
			$mail->isSMTP();  
        //	$mail->Host       = '  webmail.applusitv.uy';                     //Set the SMTP server to send through                                          //Send using SMTP
        	$mail->Host       = 'smtp.office365.com';                     //Set the SMTP server to send through
			$mail->SMTPAuth   = true;                                   //Enable SMTP authentication
		//	$mail->Username   = 'turnos.auto.uy@applusitv.uy';                     //SMTP username
            $mail->Username   = 'turnos.auto.ar@applus.com'; 	
            $mail->Password   = 'NoJodanDeNoche01$';                               //SMTP password
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         //Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
			$mail->Port       = 587;                                    //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
			$mail->SMTPSecure = 'ssl';

			//Recipients
			$mail->setFrom('turnos.auto.ar@applus.com', 'turnos.auto.ar@applus.com');
			$mail->addAddress($destino, $destino);     //Add a recipient
			$mail->SMTPOptions = array(
				'ssl' => array(
					'verify_peer' => false,
					'verify_peer_name' => false,
					'allow_self_signed' => true
				)
			);

			//Content
			$mail->isHTML(true);                                  //Set email format to HTML
			$mail->Subject = $asunto;
			$mail->Body    = $cuerpo;
			$mail->AltBody = $altBody;

			$mail->send();
			return true;
		} catch (Exception $e) {
			return false;
		}
    }

}*/
