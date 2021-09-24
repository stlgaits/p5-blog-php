<?php

namespace App\Model;

use PDO;

abstract class Manager
{
    protected $db;

    public function __construct(){
        $this->db = new PDO('mysql:host='.__DBHOST.';dbname='.__DBNAME.';charset=utf8', __DBUSER, __DBPASSWD,
        array(PDO:: ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    }

}
