<?php
class MW_Parser{
    public static function MediaTextJSParser($request, $response, $next){
        $request->registerMediaTypeParser(
            "text/javascript",
            function ($input) {
                return json_decode($input, true);
            }
        );
        return $next($request,$response);
    }

}
?>