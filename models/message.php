<?php

require_once __DIR__ . '/../db_record_php_7/record.php';

class Message extends Record{

    const DRIVER = 'mssql';
    const DB = 'Sandbox';
    const TABLE = 'tbl_sent_messages';
    const PRIMARYKEY = 'id';

    public $to;
    public $from;
    public $fromName;
    public $replyTo;
    public $cc;
    public $bcc;
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
}