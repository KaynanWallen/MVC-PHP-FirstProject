<?php

namespace App\Controller\Api;

class Api{
    public static function getDetails($request){
        return [
            'nome' => 'API - WDEV',
            'versao' => 'v1.0.0',
            'autor' => 'Kaynan Wallen',
            'email' => 'wallenkaynan@gmail.com'
        ];
    }

    protected static function getPagination($request, $obPagination){
        $queryParams = $request -> getQueryParams();
        $pages = $obPagination -> getPages();
        return [
            'paginaAtual' => isset($queryParams['page']) ? (int)$queryParams['page'] : 1,
            'quantidadepaginas' => !empty($pages) ? count($pages) : 1 
        ];
    }
}