<?php

namespace App\Http\Middleware;

class Cache{

    private function isCacheable($request){
        if(getenv("CACHE_TIME") <= 0 ){
            return false;
        }

        if($request -> getHttpMethod() != 'GET'){
            return false;
        }

        $headers = $request ->getHeaders();
        
        if(isset($headers['Cache-Control']) and $headers['Cache-Control'] == 'no-cache'){
            return false;
        }

        return true;
    }


    public function handle($request, $next){
        if(!$this -> isCacheable($request)) return $next($request);

        die("cacheavel");
    }
}