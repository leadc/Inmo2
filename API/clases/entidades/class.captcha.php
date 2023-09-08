<?php

/** Clase para validar el captcha con el servidor de google
 */
class Captcha{

    private static $googleApiEndpoint = 'https://www.google.com/recaptcha/api/siteverify';
    //private static $googleApiSecretKey = '6LeC6pkbAAAAAHNcQ_IGoAR7UrVc0-EOHaXFPOc1';
    private static $googleApiSecretKey = '6Le6oJsbAAAAALseandOUfy-gRDvQeFg4U25wLtt';


    /** Devuelve true/false si el token del captcha es válido */
    public static function ValidarCaptcha($token) {
        # La API en donde verificamos el token
        $url = self::$googleApiEndpoint;
        # Clave secreta del sitio de google (se obtiene en https://www.google.com/recaptcha/admin)
        $claveSecreta = self::$googleApiSecretKey;
        # Petición
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "secret=$claveSecreta&response=$token");
        $headers = array(
            "Content-Type: application/x-www-form-urlencoded",
            
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $resultado = curl_exec($ch);
        $resultado = json_decode($resultado);
        # La variable que nos interesa para saber si el usuario pasó o no la prueba
        # está en success
        return $resultado && isset($resultado->success) ? $resultado->success : false;
    }
}

?>
