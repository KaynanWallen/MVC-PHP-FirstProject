<?php
namespace App\Http;

class Request {
    private $router;
    private $httpMethod;
    private $uri;
    private $queryParams = [];
    private $postVars =[];
    private $headers = [];

    public function __construct($router){
        $this -> router = $router;
        $this ->  queryParams = $_GET ?? [];
        $this ->  headers = getallheaders();
        $this -> httpMethod = $_SERVER['REQUEST_METHOD'] ?? '';
        $this -> setUri();
        $this -> setPostVars();
    }

    private function setPostVars(){
        if ($this->httpMethod == 'GET') {
            // Se for um método GET, não precisa processar o corpo da solicitação
            return false;
        }
        
        $inputRaw = file_get_contents('php://input');
        
        if (strlen($inputRaw)) {
            $jsonVars = json_decode($inputRaw, true);
            if ($jsonVars !== null) {
                // Verificar se a decodificação JSON foi bem-sucedida
                $this->postVars = json_decode($inputRaw, true);
            } else {
                // Se não for JSON válido, então provavelmente são dados de formulário
                $this->postVars = $_POST ?? [];
            }
        } else {
            // Se não há corpo na solicitação, use os dados do formulário, se houver
            $this->postVars = $_POST ?? [];
        }
        
    }


    private function setUri(){
        $this -> uri =  $_SERVER['REQUEST_URI'] ?? '';
        $xURI = explode('?', $this -> uri);
        $this -> uri = $xURI[0];
    }

    public function getRouter(){
        return $this -> router;
    }

    public function getHttpMethod(){
        return $this -> httpMethod;
    }

    public function getUri(){
        return $this -> uri;
    }

    public function getHeaders(){
        return $this -> headers;
    }

    public function getQueryParams(){
        return $this -> queryParams;
    }

    public function getPostVars(){
        return $this -> postVars;
    }




}


?>