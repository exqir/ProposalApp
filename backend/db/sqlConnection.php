<?php

class SqlConnection {
    protected $mysqli;

    /**
     * Establishes a connection to the db and sets the charset to utf8
     **/
    public function __construct($host, $user, $pw, $db_name) {
        $this->mysqli = new mysqli($host, $user, $pw, $db_name);
        if(mysqli_connect_errno()) {
            printf("Connect failed: %s\n", mysqli_connect_error());
            exit();
        }
        if (!$this->mysqli->set_charset("utf8")) {
            printf("Error loading character set utf8: %s\n", $this->mysqli->error);
            exit();
        }
    }

    public function getConnection() {
        return $this->mysqli;
    }

    /**
     * Terminates the connection to the db
     **/
    public function closeConnection() {
        $this->mysqli->close();
    }

    protected function sqlQuery($query, $params, $success,$referenceObject) {
        if($stmt = $this->mysqli->prepare($query)) {
            if($params !== NULL) {
                $bindParams = $this->getParamsAsArrayByReference(
                    array_merge(array($this->getParameterTypesAsString($params)),$params));
                call_user_func_array(array($stmt,'bind_param'), $bindParams);
            }
            if($stmt->execute()) {
                return call_user_func(array($this,$success),$stmt,$referenceObject);
            } else {
                printf('errno: %d, error: %s called by: %s', $stmt->errno, $stmt->error, $success);
                return -2; // Execute Error
            }
        } else {
            printf('errno: %d, error: %s', $this->mysqli->errno, $this->mysqli->error);
            return -1; // Query Error
        }
    }

    protected function getParamsAsArrayByReference($params) {
        $array = array();
        array_map(function($param) use (&$array) {
            $array[] = &$param;
        },$params);
        return $array;
    }

    protected function getParameterTypesAsString($parameterArray) {
        $s = '';
        array_map(function($param) use (&$s) {
            if(is_string($param)) $s .= 's';
            else if (is_int($param)) $s .= 'i';
        },$parameterArray);
        return $s;
    }

    protected function getInsertId($stmt, $referenceObject) {
        return $this->mysqli->insert_id;
    }
}