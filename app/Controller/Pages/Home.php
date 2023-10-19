<?php

namespace App\Controller\Pages;
use \App\Utils\View;
use \App\Model\Entity\Organization;

class Home extends Page{
    
    //Retornar o conteudo View da Home
    public static function getHome(){
        $obOrganization = new Organization;

        $content = View::render('pages/home', [
            'name' => $obOrganization->name,
        ]);

        return parent::getPage('WDEV - HOME', $content);
    }

    
}