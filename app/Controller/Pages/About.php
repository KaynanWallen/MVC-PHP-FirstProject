<?php

namespace App\Controller\Pages;
use \App\Utils\View;
use \App\Model\Entity\Organization;

class About extends Page{
    
    //Retornar o conteudo View da Home
    public static function getAbout(){
        $obOrganization = new Organization;

        $content = View::render('pages/about', [
            'name' => $obOrganization->name,
            'description' => $obOrganization -> description,
            'site' => $obOrganization -> site
        ]);

        return parent::getPage('WDEV - Sobre', $content);
    }

    
}