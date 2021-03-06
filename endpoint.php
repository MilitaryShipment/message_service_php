<?php

require_once __DIR__ . '/api.php';
require_once __DIR__ . '/models/message.php';
require_once __DIR__ . '/messenger.php';

class EndPoint extends API{

    public function __construct($request,$origin)
    {
        parent::__construct($request);
    }
    protected function example(){
        return array("endPoint"=>$this->endpoint,"verb"=>$this->verb,"args"=>$this->args,"request"=>$this->request);
    }
    protected function send(){
        if($this->method != 'POST'){
            throw new Exception('This resouce is accessible only through POST');
        }
        if(isset($this->request->attachments)){
            $attachments = $this->_parseAttachments();
            $this->_parseRequestArrays();
        }
        $message = new Message();
        $message->status_id = 1;
        $message->setFields($this->request)->create();
        Messenger::send($this->request->send_to,$this->request->send_from,$this->request->fromName,$this->request->replyTo,$this->request->cc,$this->request->bcc,$this->request->subject,$this->request->body,$attachments);
        return $message;
    }
    protected function verify(){
        $contact = null;
        $data = array();
        if($this->method != "GET"){
            throw new Exception('This resouce is accessible only through GET');
        }
        if(isset($this->args[0]) && !isset($this->verb)){
            $contact = $this->args[0];
        }else{
            $contact = $this->verb;
        }
        return Messenger::verify($contact);
    }
    protected function message(){
        $data = null;
        if(!isset($this->verb) && !isset($this->args[0]) && $this->method == 'POST'){ //create
            throw new \Exception('This resouce is accessible only through GET');
        }elseif(!isset($this->verb) && !isset($this->args[0]) && $this->method == 'GET'){ //get all
            $data = Message::get('status_id',1);
        }elseif(!isset($this->verb) &&(int)$this->args[0] && $this->method == 'GET'){ //get by id
            $data = new Message($this->args[0]);
        }elseif((int)$this->args[0] && $this->method == 'PUT'){ //update by id
            throw new \Exception('This resouce is accessible only through GET');
        }elseif(isset($this->verb)){
            throw new \Exception('Malformed Request');
        }else{
            throw new \Exception('Malformed Request');
        }
        return $data;
    }
    protected function _parseAttachments(){
        $attachments = array();
        $this->request->attachments = array();
        foreach($_FILES as $file){
            if(!move_uploaded_file($file['tmp_name'],'/tmp/' . $file['name'])){
                throw new \Exception('Failed Saving File: ' . $file['name']);
            }
            $this->request->attachments[] = $file['name'];
            $attachments[] = '/tmp/' . $file['name'];
        }
        return $attachments;
    }
    protected function _parseRequestArrays(){
        $keys = array('send_to','cc','bcc');
        foreach($keys as $key){
            $this->request->$key = array();
            $pattern = "/" . $key . "/";
            foreach($this->request as $ind => $value){
                if(preg_match($pattern,$ind) && $ind !== $key){
                    unset($this->request->$ind);
                    $this->request->$key[] = $value;
                }
            }
        }
        return $this;
    }

}
