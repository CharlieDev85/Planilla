<?php
/**
 * Created by PhpStorm.
 * User: carle
 * Date: 4/10/2016
 * Time: 10:20 PM
 */

require_once(LIB_PATH.DS."config.php");



class MySQLDatabase{

    private $connection;

    function __construct() {
        $this->open_connection();
    }

    /**
     *It opens the connection
     */
    public function open_connection() {
        $this->connection = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
        if(mysqli_connect_errno()) {
            die("Database connection failed: " .
                mysqli_connect_error() .
                " (" . mysqli_connect_errno() . ")"
            );
        }
    }

    /**
     *It closes a connection if there is an open connection.
     */
    public function close_connection() {
        if(isset($this->connection)) {
            mysqli_close($this->connection);
            unset($this->connection);
        }
    }

    /**
     * It receives a mysql query and returns a resultset if it is a select.
     * @param $sql
     * @return bool|mysqli_result
     *
     */
    public function query($sql) {
        //echo $sql . '<br>';
        //$sql_escaped = $this->escape_value($sql);
        $result = mysqli_query($this->connection, $sql);
        $this->confirm_query($result);
        return $result;
    }

    /**
     * It calls a store procedure.
     * @param $sql
     * @return bool|mysqli_result
     *
     */
    public function query_call($sql){
        echo $sql . '<br>';
        $result = mysqli_query($this->connection, $sql);
        $this->confirm_query($result);
        $this->open_connection();
        return $result;
    }

    /**
     * @param $result
     * @return bool
     * It receives a resultset and if it was executed correctly it will return true, otherwise it will end the connection
     * showing the message "Database query failed"
     */
    private function confirm_query($result) {
//        var_dump($result);
        if (!$result) {
            die("Database query failed.");
        }
        return true;
    }


    /**
     * It receives an array and returns a string separated by commas.
     * @param $array_fields
     * @return string
     *
     */
    public function implode_fields($array_fields){
        return implode(',', $array_fields);
    }


    /**
     * It receives the name of a table and returns true if the table is empty. If table is not empty, it returns false.
     * @param $table_name
     * @return bool
     */
    public function table_is_empty($table_name){
        $sql = 'SELECT * FROM ' . $table_name;
        $result = $this->query($sql);
        return $this->result_is_empty($result);
    }


    /**
     * Receives a resultset and returns true if it is empty.
     * @param $result
     * @return bool
     */
    public function result_is_empty($result){
        $num_of_rows = $this->num_rows($result);
        if($num_of_rows == 0){
            return true;
        }
        return false;
    }

    /**
     * Escapes special characters in a string for use in an SQL statement,
     * @param $string
     * @return string
     */
    public function escape_value($string) {
        $escaped_string = mysqli_real_escape_string($this->connection, $string);
        return $escaped_string;
    }

    // "database neutral" functions

    /**
     * It receives a mysql resultset and returns a numeric array.
     * @param $result_set
     * @return array|null
     */
    public function fetch_all($result_set) {
        return mysqli_fetch_all($result_set, MYSQLI_NUM);
    }

    /**
     * It receives a mysql-resultset and returns a asscociated array.
     * @param $result_set
     * @return array|null
     */
    public function fetch_array($result_set) {
        return mysqli_fetch_array($result_set, MYSQLI_ASSOC);
    }


    /**
     * It receives a mysql-resultset and returns a asscociated array.
     * @param $result_set
     * @return array|null
     */
    public function fetch_array_assoc($result_set) {
        return mysqli_fetch_assoc($result_set);
    }

    /**
     * It returns the number of rows that the given resultset has.
     * @param $result_set
     * @return int
     */
    public function num_rows($result_set) {
        return mysqli_num_rows($result_set);
    }

    /**
     * get the last id inserted over the current db connection
     * @return int|string
     */
    public function get_last_id_inserted() {
        return mysqli_insert_id($this->connection);
    }

    /**
     * It returns true if there is at least one affected row in a previous MYSQL operation.
     * @return bool
     */
    public function affected_rows() {
        if(mysqli_affected_rows($this->connection) > 0){
            return true;
        }
        return false;
    }
}

$db = new MySQLDatabase();


