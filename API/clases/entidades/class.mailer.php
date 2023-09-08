<?php
require_once 'clases/acceso_datos/DB_Conexion.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use BasicORM\LOGS\Log;
require 'vendor/phpmailer/phpmailer/src/Exception.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';

/** Clase no instanciable usada para el envio de mails
 * Métodos utilizables: 
 *  - EnviarMail
 */
class Mailer{
    /** CONFIGURACIONES PARA ENVÍO DE MAILS CON APPLUSITV.UY */
  /*  public static $Host = 'webmail.applusitv.uy'; //Host del mail
    public static $Is_smtp = true; // Set mailer to use SMTP
    public static $SMTPAuth = true; //Requiere autenticación
    public static $Username = ['info.auto.uy@applusitv.uy'];
    public static $Password = ['NoJodanDeNoche01$'];
    public static $From = "Applus ITV"; //Nombre para el from
    public static $SMTPSecure_enabled = false; //Habilita la configuración de SMTP Secure
    public static $SMTPSecure = 'ssl'; // Enable TLS encryption, `ssl` also accepted
    public static $Port = 25;*/

    /** CONFIGURACIONES PARA ENVÍO DE MAILS CON OFFICE 365 */
    
    public $Host = 'smtp.sendgrid.net'; //Host del mail
    public $Is_smtp = true; // Set mailer to use SMTP
    public $SMTPAuth = true; //Requiere autenticación
    public $Username = 'apikey';
    public $Password = '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';
    public $From = ('noresapplus@gmail.com');   //Nombre para el from
    public $SMTPSecure_enabled = true; //Habilita la configuración de SMTP Secure
    public $SMTPSecure = 'tls'; // Enable TLS encryption, `ssl` also accepted
    public $Port = 587;
    


    /** EnviarMail
     * Envía un mail usando los parámetros configurados en la clase
     * Desde: Indice que indica el mail que enviará el mensaje
     * Asunto: Asunto del mensaje
     * Cuerpo_mail: Cuerpo del mensaje
     * destinatario: Mail del destinatario
     * copiado: Mail de copia (Puede omitirse)
     */
    public static function EnviarMail($desde, $asunto, $cuerpo_mail, $destinatario, $copiado = null){
        $mail = new PHPMailer(true);
        try{
            //Aplicando Configuraciones
            if(self::$Is_smtp){ $mail->isSMTP(); }
            $mail->Host = self::$Host;
            $mail->SMTPAuth = self::$SMTPAuth;
            $mail->Username = self::$Username[$desde];
            $mail->Password = self::$Password[$desde];
            if(self::$SMTPSecure_enabled){ $mail->SMTPSecure = self::$SMTPSecure; }
            $mail->Port = self::$Port;
            $mail->isHTML(true); // Set email format to HTML
            //$mail->SMTPDebug = 0;  //No debug

            //Ingreso el asunto y el cuerpo del mail
            $mail->Subject = $asunto;
            $mail->Body = $cuerpo_mail;
            //Ingreso el From
            $mail->setFrom(self::$Username[$desde], self::$From);
            //Ingreso los destinatarios
            $mail->addAddress($destinatario);
            if($copiado != null){ $mail->addCC($copiado); }
            //$mail->addAttachment('href', 'assets/pdf/Información útil previo a inspección técnica.pdf');
            //$mail->addAttachment('src/assets/pdf/Información útil previo a inspección técnica.pdf');
            //Attachments
            //$mail->addAttachment('C:/Apache24/htdocs/WEB_URUGUAY/Uruguay-WebApp/src/assets/pdf/Información útil previo a inspección técnica.pdf');
            //$mail->addAttachment('href', 'assets/pdf/Información útil previo a inspección técnica.pdf');
            
      

           // $url = 'http://app.pimsaseguros.com/_files/_img/_holidays/040616-160454_img001.pdf';
            //$mail->addAttachment('http://app.pimsaseguros.com/_files/_img/_holidays/040616-160454_img001.pdf');
            //$fichero = file_get_contents($url);
            //$mail->addStringAttachment($fichero, 'solicitud.pdf');

            $mail->send();
            return true;
        }catch(Exception $e){
            
            Log::WriteLog(MAIN_LOG, ["CORREO: $e", "--> CORREO ERROR CLASS MAILER<--"]);
            return false;
        }
    }
}

?>