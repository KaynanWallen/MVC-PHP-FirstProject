<?php 
namespace App\Http;

class Response {
    private $httpCode = 200;
    private $headers = [];
    private $contentType = 'text/html';
    private $content;

    public function __construct($httpCode, $content,$contentType = 'text/html'){
        $this -> httpCode = $httpCode;
        $this -> content = $content;
        $this -> setContentType($contentType);
    }

    public function setContentType($contentType){
        $this -> contentType = $contentType;
        $this -> Addheader('Content-Type', $contentType);
    }

    public function Addheader($key, $value){
        $this -> headers[$key] = $value;
    }

    private function sendHeader(){
        http_response_code($this -> httpCode);
        foreach($this -> headers as $key => $value){
            header($key.':'.$value);
        }
    }

    public function sendResponse(){
        self::sendHeader();
        switch($this -> contentType){
            case 'text/html':
                echo $this -> content;
                exit;
            case 'application/json':
                echo json_encode($this -> content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            default:
                echo $this -> content;
                exit;
                
        }
    }

}

?>