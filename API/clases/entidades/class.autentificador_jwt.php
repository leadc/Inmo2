<?php
require_once 'vendor/autoload.php';
use Firebase\JWT\JWT;
class AutentificadorJWT
{   
    private static $key = 'Applus01$$WebToken'; //Clave de encriptación
    private static $format = array('HS256'); //Tipo de encirptación 
    //private static $time_token = (60*60*1); //Segundos*Minutos*Horas
    private static $time_token = (1000*60*8); //Segundos*Minutos*Horas Para validez del token

    public static function CrearToken($datos){
        $time = time();
        $token = array(
            'iat' => $time, // Tiempo que inició el token
            'aud' => self::Aud(),
            'exp' => $time + self::$time_token, // Tiempo que expirará el token (S*M*X - X = horas)
            //'exp' => $time + (60*3), // Tiempo que expirará el token (+3 minutos para prueba)
            'data' => [ // información del usuario
                'datos' => $datos
            ]
        );
        return JWT::encode($token, self::$key);
    }
    
    public static function VerificarToken($token){
        if(empty($token)){
            return false;
        }
        try{
            $decode = JWT::decode($token,self::$key,self::$format);
            if($decode->aud !== self::Aud()){
                return false;
            }
            $time = time();
            $decode->exp = $time + self::$time_token;
            return JWT::encode($decode, self::$key);
        }catch(Exception $e){
            return false;
        }
    }

    public static function VerificarTokenGestionITV($token){
        if(empty($token)){
            return false;
        }
        try{
            $headers = [ 'ACCEPT: '.$token];
            $file = fopen(__DIR__.'/archivo.txt',"w");
            fwrite($file,json_encode($headers).PHP_EOL);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,"http://10.98.10.6/Gestion_ITV/API/gestionITV.login.php");
            curl_setopt($ch, CURLOPT_HTTPHEADER,$headers); 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $nuevo_token = curl_exec ($ch);
            fwrite($file,json_encode($nuevo_token).PHP_EOL);
            fclose($file);
            if(isset($nuevo_token->token)){
                return $nuevo_token->token;
            }
            curl_close($ch);
            return false;
        }catch(Exception $e){
            return false;
        }
    }
    
   
     public static function ObtenerPayLoad($token)
    {
        return JWT::decode(
            $token,
            self::$key,
            self::$format
        );
    }
     public static function ObtenerData($token)
    {
        return JWT::decode(
            $token,
            self::$key,
            self::$format
        )->data;
    }
    private static function Aud()
    {
        $aud = '';
        
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $aud = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $aud = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $aud = $_SERVER['REMOTE_ADDR'];
        }
        
        $aud .= @$_SERVER['HTTP_USER_AGENT'];
        $aud .= gethostname();
        
        return sha1($aud);
    }
}
?>