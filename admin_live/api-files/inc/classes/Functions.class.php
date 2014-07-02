<?php
session_start();
/**
 * @Description : This class Functions includes all the main functions to be used in the app.
 * @Copyright : Schlum
 * @Developed by : Click Labs Pvt. Ltd.
 */
header('Content-type: application/json');

class Functions {

    private $_dalObj;
    private $_s3;

    public function __construct() {
        
         if (!defined('awsAccessKey'))
            define('awsAccessKey', 'AKIAJTIGKXNQVTRBU45A');
        if (!defined('awsSecretKey'))
            define('awsSecretKey', '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');

        $this->_s3 = new S3(awsAccessKey, awsSecretKey);        
        $this->_dalObj = new DAL;
    }

    function __autoload($class_name) {
        require $class_name . '.class.php';
    }
    
    
    /*
     * ------------------------------------------------------
     *  check for all post parameter whether any blank
     * ------------------------------------------------------
     */

    public function checkBlank($checkArray = array()) {
        if (in_array("", $checkArray)) {
            die(json_encode(array('error' => "Mandatory fields not filled")));
        } else {
            return 0;
        }
    }
    
     function check($checkArray = array()) 
    {
        if (in_array("", $checkArray)) 
        {          
            return 1;
        }
        return 0;
    }
    /*
     * ------------------------------------------------------
     *  function to save images aur mp3 files into s3 bucket
     * ------------------------------------------------------
     */

    public function saveImageFromFile($files, $folder) {



        if (isset($files["name"])) {
            if ($files["error"] > 0) {
                echo json_encode(array("error" => "File parameter missing"));
                exit(1);
            } else {

                $files["name"] = str_replace(" ", "_", $files["name"]);
                // print_r($files);


                $type = $files['type'];  // e.g. gives "image/jpeg"

                $ok = 0;
                switch ($type) {
                    case 'audio/mpeg':
                    case 'image/jpeg':
                    case 'image/jpg':
                    case 'image/png':
                    case 'audio/x-mpeg':
                    case 'audio/mp3':
                    case 'audio/x-mp3':
                    case 'audio/x-mpeg3':
                    case 'audio/mpg':
                    case 'application/octet-stream':
                    case 'audio/x-m4a':
                        $ok = 1;
                        break;
                    default:
                        echo json_encode(array("error" => "File type error"));
                        exit(1);
                }
                if ($ok == 1) {
                    //  $contents = $this->_s3->getBucket("boom-botix");
                    // print_r($contents);
                    //die();
                    //  foreach ($contents as $file) {
                    //  $fname = $file['name'];
                    //  echo $fname."<br>";
                    //   $fname= explode("/",$fname);
                    //  if($fname[0]==$folder)
                    // {
                    //  if ($fname[1] == $files["name"]) 
                    //  {

                    $length = 5;
                    $str = '';
                    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
                    $size = strlen($chars);
                    for ($i = 0; $i < $length; $i++) {
                        $str .= $chars[rand(0, $size - 1)];
                    }
                    $files["name"] = $str . "-" . $files["name"];
                    // }
                    // }
                    //}


                    $fileName = $files["name"];
                    //$fileName=str_replace(" ","_",$files["name"]);
                    $fileTempName = $files["tmp_name"];



                    if ($this->_s3->putObjectFile($fileTempName, $type, "boom-botix", "$folder/" . $fileName, S3::ACL_PUBLIC_READ)) {

                        $path = $fileName;

                        return $path;
                    } else {

                        if ($folder == 'song_image') {
                            $path = 'default.png';
                            return $path;
                        }

                        $json_error = array("error" => "some thing went wrong.");
                        echo json_encode($json_error);
                        exit(1);
                    }
                }
            }
        } else {
            echo json_encode(array("error" => "No file to upload"));
            exit(1);
        }
    }
    
    
    
    

    /*
     * ------------------------------------------------------------------
     * To get Login Information of the user.
     * Input Parameters : Username and Password
     * Output: User id, name and password.
     */

    public function getLoginIntoFromUsernameAndPassword($username, $password) {
        
        $password = md5($password);
        
        $sql = "SELECT `id`,`name`,`password` FROM `tb_admin` WHERE";
        $sql.= "`name` = ? and `password` = ? LIMIT 1";                  
        $bindParams = array($username, $password);
        $response = $this->_dalObj->sqlQuery($sql, $bindParams);
        if (count($response) == 0) {
            die(json_encode(array('log' => 0)));
        } else {
            $response['log'] = 1;
            $_SESSION['login_access'] = $username;            
            return $response;
        }
    }
    
    /*
     * ------------------------------------------------------------------
     *  To check whether access token provided by user is valid or not
     *  Input : Access token provided by user
     *  Input : an associative array of extra data to return
     *          $extraData = array(
     *              "Image_path" => "Image_path",
     *              "Full_name" => "Full_name")
     *          );
     *  Response : an associative array including UserId and the
     *             extra fields requested in $extraData
     * -------------------------------------------------------------------
     */

    public function authenticateAccessTokenReturnExtraData($userAccessToken, $extraData) {
        $sql = "SELECT `user_id`";
        foreach ($extraData as $fieldName => $fieldDefinition) {
            $sql .= ", $fieldDefinition AS `$fieldName`";
        }
        $sql.= " FROM `Users`";
        $sql.= " WHERE `access_token`= ? LIMIT 1";
        $bindParams = array($userAccessToken);
        $response = $this->_dalObj->sqlQuery($sql, $bindParams);
        if (count($response) > 0) {
            return $response['data'][0];
        } else {
            die(json_encode(array(
                "error" => "Invalid Access Token !!"
                            ), JSON_PRETTY_PRINT));
        }
    }

}

/* End of file Functions.class.php */
/* Location: ./inc/classes/Functions.class.php */
?>