<?php


namespace App\Controller\Admin;
use \App\Utils\View;
use \App\Model\Entity\Testimony as EntityTestimony;
use \WilliamCosta\DatabaseManager\Pagination;

class Testimony extends Page{

    private static function getTestimonyItems($request, &$obPagination){
        $items = '';

        $quantidadeTotal = EntityTestimony::getTestimonies(null,null,null,'COUNT(*) as qtd') -> fetchObject() -> qtd;
        $queryParams = $request -> getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        $obPagination = new Pagination($quantidadeTotal, $paginaAtual, 5);

        $results = EntityTestimony::getTestimonies(null,'id DESC', $obPagination -> getLimit());

        while ($obTestimony = $results -> fetchObject(EntityTestimony::class)){
            $items .= View::render('admin/modules/testimonies/item', [
                'id' => $obTestimony -> id,
                'nome' => $obTestimony -> nome,
                'mensagem' => $obTestimony -> mensagem,
                'data' => date('d/m/Y H:i:s', strtotime($obTestimony -> data)) 
            ]);
        }
        return $items;
    }


    public static function getTestimonies($request){
        $content = View::render('admin/modules/testimonies/index', [
            'itens' => self::getTestimonyItems($request,$obPagination),
            'pagination' => parent::getPagination($request, $obPagination),
            'status' => self::getStatus($request)
        ]);
        return parent::getPanel('Depoimentos > WDEV', $content, 'testimonies');
    }


    public static function getNewTestimony($request){
        $content = View::render('admin/modules/testimonies/form', [
            'title' => 'Cadastrar Depoimento',
            'nome' => '',
            'mensagem' => '',
            'status' => '',
        ]);
        return parent::getPanel('Cadastrar depoimento > WDEV', $content, 'testimonies');
    }

    public static function setNewTestimony($request){
        $postVars = $request -> getPostVars();
        $obTestimony = new EntityTestimony;
        $obTestimony -> nome = $postVars['nome'] ?? '';
        $obTestimony -> mensagem = $postVars['mensagem'] ?? '';
        $obTestimony -> cadastrar();

        $request -> getRouter() -> redirect('/admin/testimonies/'.$obTestimony -> id.'/edit?status=created');
    }

    private static function getStatus($request){
        $queryParams = $request -> getQueryParams();

        if(!isset($queryParams['status'])) return '';

        switch($queryParams['status']){
            case 'created':
                return Alert::getSuccess('Depoimentos criado com sucesso!');
            case 'updated':
                return Alert::getSuccess('Depoimentos atualizado com sucesso!');
            case 'deleted':
                return Alert::getSuccess('Depoimentos excluido com sucesso!');
        }
    }

    public static function getEditTestimony($request, $id){
        $obTestimony = EntityTestimony::getTestimoniesById($id);
        if(!$obTestimony instanceof EntityTestimony){
            $request -> getRouter() -> redirect('/admin/testimonies');
        }
        $content = View::render('admin/modules/testimonies/form', [
            'title' => 'Editar Depoimento',
            'nome' => $obTestimony -> nome,
            'mensagem' => $obTestimony -> mensagem,
            'status' => self::getStatus($request)
        ]);
        return parent::getPanel('Editar depoimento > WDEV', $content, 'testimonies');
    }

    public static function setEditTestimony($request, $id){
        $obTestimony = EntityTestimony::getTestimoniesById($id);
        if(!$obTestimony instanceof EntityTestimony){
            $request -> getRouter() -> redirect('/admin/testimonies');
        }

        $postVars = $request -> getPostVars();

        $obTestimony -> nome = $postVars['nome'] ?? $obTestimony -> nome;
        $obTestimony -> mensagem = $postVars['mensagem'] ?? $obTestimony -> mensagem;

        $obTestimony -> atualizar();

        $request -> getRouter() -> redirect('/admin/testimonies/'.$obTestimony -> id.'/edit?status=updated');
    }


    public static function getDeleteTestimony($request, $id){
        $obTestimony = EntityTestimony::getTestimoniesById($id);
        if(!$obTestimony instanceof EntityTestimony){
            $request -> getRouter() -> redirect('/admin/testimonies');
        }
        $content = View::render('admin/modules/testimonies/delete', [
            'nome' => $obTestimony -> nome,
            'mensagem' => $obTestimony -> mensagem,
        ]);
        return parent::getPanel('Excluir depoimento > WDEV', $content, 'testimonies');
    }

    public static function setDeleteTestimony($request, $id){
        $obTestimony = EntityTestimony::getTestimoniesById($id);
        if(!$obTestimony instanceof EntityTestimony){
            $request -> getRouter() -> redirect('/admin/testimonies');
        }

        $obTestimony -> excluir();

        $request -> getRouter() -> redirect('/admin/testimonies?status=deleted');
    }

}