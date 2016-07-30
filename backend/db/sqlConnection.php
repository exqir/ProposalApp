<?php

class SqlConnection {
    private $mysqli;

    /**
     * Establishes a connection to the db and sets the charset to utf8
     **/
    public function __construct($host, $user, $pw, $db_name)
    {

        $this->mysqli = new mysqli($host, $user, $pw, $db_name);
        if(mysqli_connect_errno())
        {
            printf("Connect failed: %s\n", mysqli_connect_error());
            exit();
        }
        if (!$this->mysqli->set_charset("utf8")) {
            printf("Error loading character set utf8: %s\n", $this->mysqli->error);
            exit();
        }
    }

    /**
     * Terminates the connection to the db
     **/
    public function closeConnection()
    {
        $this->mysqli->close();
    }
}