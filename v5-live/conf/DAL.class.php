<?php

header('Content-type: application/json');

/**
 * @ Description : config.class.php contains class DAL, we are making a function that we are using to connect to our database.
 * The connection, as well as all forthcoming queries, will live inside a class named DAL. Wrapping all database involvement
 * inside a single class allows us to manipulate our queries later without needing to touch business or presentation layer scripts.
 * Also, it provides some degree of mock namespacing.
 * @ Copyright : Boombotix
 * @ Developed by : Click Labs Pvt. Ltd.
 */
class DAL {

    private $conn;
    
    public $ImageBasePath;//Global variable for Base path for images stored in s3 bucket
    public $UserImageFolder;//Global variable for folder name for user images stored in s3 bucket
    public $SongImageFolder;//Global variable for folder name for song images stored in s3 bucket
    public $mp3ImageFolder;//Global variable for folder name for mp3 songs stored in s3 bucket

    // making a default constructor
    public function __construct() {
        $this->conn = self::dbconnect(); // will call the dbconnect function automatically when the class DAL will be called everytime.
       $this->ImageBasePath = 'http://boom-botix.s3.amazonaws.com/';
       $this->UserImageFolder = 'user_profile/';
       $this->SongImageFolder = 'song_image/';
       $this->mp3ImageFolder = 'song/';
       $this->popupStatus = 0;
    }

    /*
     * -------------------------------------------------------------------------------
     *  This function is used to execute a query that will be sent by any function of another class.
     *  It will receive two arguments
     *  1. Query String
     *  2. Bind Parameters to execute with the query.
     *  This function will receive the query string and then send that string and parameters to private query function
     *  to get the output response.
     * -------------------------------------------------------------------------------
     */

    public function sqlQuery($query, $bindParams) {
        $output = $this->query($query, $bindParams);
        return $output;
    }

    /**
     * -------------------------------------------------------
     * dbconnect function allows us to connect to the database.
     * Login Username
     * Login Password
     * Server Host name that is mysql host for now.
     * Database name
     * --------------------------------------------------------
     */
    private static function dbconnect() {
        $login = 'root'; // Login username of server host.
        $password = 'Cz3bjPQQRb7dtCjD'; // Password of server host.
        $dsn = "mysql:host=localhost;dbname=boombotix"; // Set up a DSN for connection with Database boombotix.

        $opt = array(
            // any occurring errors wil be thrown as PDOException
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            // an SQL command to execute when connecting
           /// PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"
        );

        // Making a new PDO conenction.
        $conn = new PDO($dsn, $login, $password, $opt);
        // End Connection and Return to other files.

        return $conn; // Returning connection.
    }

    /*
     * -------------------------------------------------------------------------------
     *  This function is called to execute all queries. It will receive two arguments
     *  1. Query String
     *  2. Bind Parameters to execute with the query.
     * -------------------------------------------------------------------------------
     */

    private function query($sql, $bindParams) {
        try {
            $data = $this->conn->prepare($sql); //Prepare SQL query for execution
            $data->execute($bindParams); //Execute query by passing bind parameters
        } catch (Exception $e) {
            die(json_encode(array(
                'error' => $e->getMessage()
                            ))); //Stop execution and print the SQL query error in case query does not execute
        }

        if (strpos($sql, 'SELECT') === false) {  //If SQL query is other then SELECT query then return last insert ID
            if (strstr($sql, 'UPDATE') or strstr($sql, 'DELETE')) {
                return $data->rowCount();
            }
            return $this->conn->lastInsertId();
        }


        $res = $data->fetchAll(PDO::FETCH_ASSOC); //Fetch all the data in case SELECT query is executed

        $results = array();

        foreach ($res as $row) {
            foreach ($row as $k => $v) {
                $result[$k] = $v;
            }
            $results['data'][] = $result;
        }
        // return all the results back to called class.
        return $results;
    }
	

}

/* End of file config.class.php */
/* Location: ./conf/config.class.php */