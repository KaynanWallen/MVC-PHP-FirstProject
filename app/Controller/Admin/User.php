<?php


namespace App\Controller\Admin;
use \App\Utils\View;
use \App\Model\Entity\User as EntityUser;
use \WilliamCosta\DatabaseManager\Pagination;

class User extends Page{

    private static function getUserItems($request, &$obPagination){
        $items = '';

        $quantidadeTotal = EntityUser::getUsers(null,null,null,'COUNT(*) as qtd') -> fetchObject() -> qtd;
        $queryParams = $request -> getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        $obPagination = new Pagination($quantidadeTotal, $paginaAtual, 5);

        $results = EntityUser::getUsers(null,'id DESC', $obPagination -> getLimit());

        while ($obUser = $results -> fetchObject(EntityUser::class)){
            $items .= View::render('admin/modules/users/item', [
                'id' => $obUser -> id,
                'nome' => $obUser -> nome,
                'email' => $obUser -> email
            ]);
        }
        return $items;
    }


    public static function getUsers($request){
        $content = View::render('admin/modules/users/index', [
            'itens' => self::getUserItems($request,$obPagination),
            'pagination' => parent::getPagination($request, $obPagination),
            'status' => self::getStatus($request)
        ]);
        return parent::getPanel('Usuários > WDEV', $content, 'users');
    }

    
    private static function getStatus($request){
        $queryParams = $request -> getQueryParams();

        if(!isset($queryParams['status'])) return '';

        switch($queryParams['status']){
            case 'created':
                return Alert::getSuccess('Usuário criado com sucesso!');
            case 'updated':
                return Alert::getSuccess('Usuário atualizado com sucesso!');
            case 'deleted':
                return Alert::getSuccess('Usuário excluido com sucesso!');
            case 'duplicated':
                return Alert::getError('Usuário duplicado, escolha outro E-mail!');
        }
    }

    public static function getNewUser($request){
        $content = View::render('admin/modules/users/form', [
            'title' => 'Cadastrar Usuário',
            'nome' => '',
            'email' => '',
            'senha' => '',
            'status' =>  self::getStatus($request),
        ]);
        return parent::getPanel('Cadastrar usuário > WDEV', $content, 'users');
    }

    public static function setNewUser($request){
        $postVars = $request -> getPostVars();
        $nome = $postVars['nome'] ?? '';
        $email = $postVars['email'] ?? '';
        $senha = $postVars['senha'] ?? '';

        $obUser = EntityUser::getUserByEmail($email);
        if($obUser instanceof EntityUser) {
            $request -> getRouter() -> redirect('/admin/users/new?status=duplicated');
        }

        $obUser = new EntityUser;
        $obUser -> nome = $nome ?? '';
        $obUser -> email =  $email ?? '';
        $obUser -> senha =  password_hash($senha, PASSWORD_DEFAULT) ?? '';
        $obUser -> cadastrar();
        $request -> getRouter() -> redirect('/admin/users/'.$obUser -> id.'/edit?status=created');
    }

    
    public static function getEditUser($request, $id){
        $obUser = EntityUser::getUserById($id);
        if(!$obUser instanceof EntityUser){
            $request -> getRouter() -> redirect('/admin/users');
        }
        $content = View::render('admin/modules/users/form', [
            'title' => 'Editar Usuário',
            'nome' => $obUser -> nome,
            'email' => $obUser -> email,
            'status' => self::getStatus($request)
        ]);
        return parent::getPanel('Editar usuário > WDEV', $content, 'users');
    }

    
    public static function setEditUser($request, $id){
        $postVars = $request -> getPostVars();
        $nome = $postVars['nome'] ?? '';
        $email = $postVars['email'] ?? '';
        $senha = $postVars['senha'] ?? '';

        $obUser = EntityUser::getUserByEmail($email);
        if($obUser instanceof EntityUser && $obUser -> id != $id){
            $request -> getRouter() -> redirect('/admin/users/'.$id.'/edit?status=duplicated');
        }

        $obUser -> nome = $nome ?? '';
        $obUser -> email =  $email ?? '';
        $obUser -> senha =  password_hash($senha, PASSWORD_DEFAULT) ?? '';
        $obUser -> atualizar();
        $request -> getRouter() -> redirect('/admin/users/'.$obUser -> id.'/edit?status=updated');
    }

    public static function getDeleteUser($request, $id){
        $obUser = EntityUser::getUserById($id);
        if(!$obUser instanceof EntityUser){
            $request -> getRouter() -> redirect('/admin/users');
        }
        $content = View::render('admin/modules/users/delete', [
            'nome' => $obUser -> nome,
            'email' => $obUser -> email,
        ]);
        return parent::getPanel('Excluir usuário > WDEV', $content, 'users');
    }

    public static function setDeleteUser($request, $id){
        $obUser = EntityUser::getUserById($id);
        if(!$obUser instanceof EntityUser){
            $request -> getRouter() -> redirect('/admin/users');
        }

        $obUser -> excluir();

        $request -> getRouter() -> redirect('/admin/users?status=deleted');
    }
}