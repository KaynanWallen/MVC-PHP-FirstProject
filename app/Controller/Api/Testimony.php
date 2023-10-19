<?php

namespace App\Controller\Api;


use \App\Model\Entity\Testimony as EntityTestimony;
use \WilliamCosta\DatabaseManager\Pagination;
use \Exception;

class Testimony extends Api{

    private static function getTestimonyItems($request, &$obPagination){
        $items = [];

        $quantidadeTotal = EntityTestimony::getTestimonies(null,null,null,'COUNT(*) as qtd') -> fetchObject() -> qtd;
        $queryParams = $request -> getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        $obPagination = new Pagination($quantidadeTotal, $paginaAtual, 5);

        $results = EntityTestimony::getTestimonies(null,'id DESC', $obPagination -> getLimit());

        while ($obTestimony = $results -> fetchObject(EntityTestimony::class)){
            $items[] = [
            'id' => (int)$obTestimony -> id,
            'nome' => $obTestimony -> nome,
            'mensagem' => $obTestimony -> mensagem,
            'data' => $obTestimony -> data
            ];
        }
        return $items;
    }


    public static function getTestimonies($request){
        return [
            'depoimentos' => self::getTestimonyItems($request,$obPagination),
            'pagination' => parent::getPagination($request, $obPagination)
        ];
    }

    public static function getTestimony($request, $id){
        $obTestimony = EntityTestimony::getTestimoniesById($id); 
        if(!$obTestimony instanceof EntityTestimony){
            throw new Exception("O depoimento de id : ".$id." não foi encontrado", 404);
        }
        return [
            'id' => (int)$obTestimony -> id,
            'nome' => $obTestimony -> nome,
            'mensagem' => $obTestimony -> mensagem,
            'data' => $obTestimony -> data
        ];
    }

    public static function setNewTestimony($request){
        $postVars = $request -> getPostVars();
        return [
            'sucesso' => true
        ];
    }
}