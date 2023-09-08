<?php
    namespace BasicORM\LOGS;
    date_default_timezone_set("America/Argentina/Buenos_Aires");
    define("MAIN_LOG", "WEBAPI_LOG ". date('Y-m') .".log");
    
    class Log{
        /**
         * Writes lines in the file log
         */
        public static function WriteLog($filePath, $lines = []){
            $file = \fopen($filePath,"a+");
            $date = \date_format(new \DateTime('now', new \DateTimeZone('America/Argentina/Buenos_Aires')), 'Y-m-d H:i:s');
            for($i=0; $i<count($lines); $i++){
                \fwrite($file, "$date: ".$lines[$i].PHP_EOL);
            }
            \fclose($file);
        }
    }
?>