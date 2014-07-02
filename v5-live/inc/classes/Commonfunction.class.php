<?php

/**
 * @ Description : This class Functions includes all the main functions to be used in the app.

 * @ Copyright : Boombotix
 * @ Developed by : Click Labs Pvt. Ltd.
 */
header('Content-type: application/json');

class Commonfunction {

    private $_s3;
    private $_DAL;

    public function __construct() {

        if (!defined('awsAccessKey'))
            define('awsAccessKey', 'AKIAICBNMK4N2BGIIXKA');
        if (!defined('awsSecretKey'))
            define('awsSecretKey', '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');

        $this->_s3 = new S3(awsAccessKey, awsSecretKey);
        $this->_DAL = new DAL;

        // parent::__construct();
    }

    function __autoload($class_name) {
        require $class_name . '.class.php';
    }

    /*
     * ------------------------------------------------------
     *  authenticate customer by access token
     * ------------------------------------------------------
     */

    public function authenticateAccessToken($access_token) {


        $sql = "SELECT `user_id` FROM `tb_users` WHERE user_access_token=? LIMIT 1";

        $bindParams = array($access_token);
        $response = $this->_DAL->sqlQuery($sql, $bindParams);

        return $response;
    }

    /*
     * ------------------------------------------------------
     *  check for all post parameter whether any blank
     * ------------------------------------------------------
     */

    public function checkBlank($checkArray = array()) {
        if (in_array("", $checkArray)) {
            return 1;
        } else {
            return 0;
        }
    }

    /*
     * ------------------------------------------------------
     *  to find distance between logitude and latitude
     * ------------------------------------------------------
     */

    public function toMiles($lat1, $lon1, $lat2, $lon2) {
        // Formula for calculating distances
        // from latitude and longitude.
        $dist = acos(sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($lon1 - $lon2)));

        $dist = rad2deg($dist);
        $miles = (float) $dist * 69;

        // To get kilometers, multiply miles by 1.61
        $km = (float) $miles * 1.61;

        // This is all displaying functionality
        $display = sprintf("%0.2f", $miles) . ' miles';
        // $display .= ' ('.sprintf("%0.2f",$km).' kilometers)' ;

        return $display;
    }
    
    
    /*
     * ------------------------------------------------------
     *  Evaluating date difference
     * ------------------------------------------------------
     */

    public function getDateDifference($date1, $date2) {           // difference of two dates
        $diff = abs(strtotime($date2) - strtotime($date1));

        $years = floor($diff / (365 * 60 * 60 * 24));
        $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
        $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) /
                (60 * 60 * 24));

        $hours = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 *
                24 - $days * 60 * 60 * 24) / (60 * 60));

        $minuts = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 *
                24 - $days * 60 * 60 * 24 - $hours * 60 * 60) / 60);

        $seconds = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 *
                24 - $days * 60 * 60 * 24 - $hours * 60 * 60 - $minuts * 60));


        $date_data = array($days, $hours, $minuts, $seconds);

        return $date_data;
    }
     /*
     * ------------------------------------------------------
     *  getting time difference in hours:minutes:seconds
     * ------------------------------------------------------
     */

    public function timeDifference($timeEnd, $timeStart) {
        $tResult = strtotime($timeEnd) - strtotime($timeStart);
        return date("G:i:s", $tResult);
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
     * ------------------------------------------------------
     *  Sending mail function
     * ------------------------------------------------------
     */

    public function sendMailFromReceiveridSubjectAndBody($to, $subject, $body) {
        require 'class.phpmailer.php';
//$from = "drawtimeserver@gmail.com";
        $from = "boombotixserver@gmail.com";

        $mail = new PHPMailer();
        $mail->IsSMTP(true); // SMTP
        $mail->SMTPAuth = true;  // SMTP authentication
        $mail->Mailer = "smtp";
        $mail->Host = "tls://smtp.gmail.com";
        $mail->Port = 465;
//$mail->Username   = "drawtimeserver@gmail.com";
        $mail->Username = "boombotixserver@gmail.com";

        $mail->Password = "clicklabs";

//$mail->SetFrom($from, 'Draw Time Admin');
        $mail->SetFrom($from, 'BoomBotix Admin');


        $mail->Subject = $subject;
        $mail->MsgHTML($body);
        $address = $to;
        $mail->AddAddress($address, $to);

        if (!$mail->Send())
            return false;
        else
            return true;
    }

}

