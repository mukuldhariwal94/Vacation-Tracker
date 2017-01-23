<?php

/**
 * Created by PhpStorm.
 * User: is046231
 * Date: 9/30/16
 * Time: 9:19 PM
 */

include "./config/Config.php";


class DB
{
    // property declaration
    private $sql = null;
    private $conn = null;
    private $dbname = null;
    private $user = null;
    private $pass = null;

    // method declaration

    function __construct() {
        $settings = config::getConfig();
        $dbname = $settings['dbname'];
        $user = $settings['user'];
        $pass = $settings['pass'];

        $this->conn = new PDO('mysql:host=127.0.0.1:3306;dbname='.$dbname, $user, $pass);
    }

    public function create($sql)
    {
        $this->conn->exec($sql);
    }

    public function insert($sql)
    {
        var_dump($this->conn);
        $query = $this->conn->prepare($sql);
        $query->execute();
    }

    public function select($sql)
    {
        $query = $this->conn->prepare($sql);
        $query->execute();
        $result = $query->fetchAll();


        return $result;
    }

    public function update($sql)
    {
        $result = $this->conn->exec($sql);
        return $result;
    }

    public function delete($sql)
    {

    }


}

