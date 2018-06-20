<?php

require_once __DIR__ . '/api.php';
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
        $message = new Message();
        $message->setFields($this->request)->create();
        Messenger::send($this->request->to,$this->request->from,$this->request->fromName,$this->request->replyTo,$this->request->cc,$this->request->bcc,$this->request->subject,$this->request->body,$this->request->attachments);
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

}