<?php

require_once __DIR__ . '/../db_record_php_7/record.php';

class Message extends Record{

    const DRIVER = 'mssql';
    const DB = 'Sandbox';
    const TABLE = 'tbl_sent_messages';
    const PRIMARYKEY = 'id';

    public $send_to = array();
    public $send_from;
    public $fromName;
    public $replyTo;
    public $cc = array();
    public $bcc = array();
    public $subject;
    public $body;
    public $created_date;
    public $updated_date;
    public $created_by;
    public $updated_by;
    public $status_id;

    public function __construct($id)
    {
        parent::__construct(self::DRIVER,self::DRIVER,self::DB,self::TABLE,self::PRIMARYKEY,$id);
    }
    public static function get($key,$value){
        $ids = array();
        $data = array();
        $results = $GLOBALS['db']->suite(self::DRIVER)
            ->driver(self::DRIVER)
            ->database(self::DB)
            ->table(self::TABLE)
            ->select(self::PRIMARYKEY)
            ->where($key,"=",$value)
            ->get();
        while($row = sqlsrv_fetch_array($results,SQLSRV_FETCH_ASSOC)){
            $ids[] = $row[self::PRIMARYKEY];
        }
        foreach($ids as $id){
            $data[] = new self($id);
        }
        return $data;
    }
}
