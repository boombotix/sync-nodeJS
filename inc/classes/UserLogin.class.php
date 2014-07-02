<?php

/**
 * @ Description : UserLogin.class.php contains class UserLogin.
 * It include all the functions related to user login.
 * @ Copyright : Boombotix
 * @ Developed by : Click Labs Pvt. Ltd.
 */
header('Content-type: application/json');

class UserLogin {

    private $_funcObj;
    private $_playlist;
    private $_DAL;
    private $_s3;

    public function __construct() {

        if (!defined('awsAccessKey'))
            define('awsAccessKey', 'AKIAJTIGKXNQVTRBU45A');
        if (!defined('awsSecretKey'))
            define('awsSecretKey', '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');

        $this->_s3 = new S3(awsAccessKey, awsSecretKey);

        $this->_DAL = new DAL;
        $this->_funcObj = new Commonfunction;
        $this->_playlist = new PlayList;


        // parent::__construct();
    }

    function __autoload($class_name) {
        require $class_name . '.class.php';
    }

    /*
     * ------------------------------------------------------
     *  get user data if registered otherwise register user
     * ------------------------------------------------------
     */

    public function getUserData($data = array()) {

        $useremail = $data['email'];
        $pass = $data['pass'];
        $device_token = $data['device_token'];
        $longitude = $data['long'];
        $latitude = $data['lat'];
         $appVersion = $data['app_version'];

        $retResult = $this->_funcObj->checkBlank($checkData = array($useremail, $longitude, $latitude));

        if ($retResult == 1) {
            $response = array("error" => "Some Parameter Missing!");
            return $response;
        }

        $sql = "SELECT user_id,user_active FROM tb_users WHERE user_email=?  LIMIT 1";
        $bindParams = array($useremail);
        $response = $this->_DAL->sqlQuery($sql, $bindParams);


        if (count($response) == 0) {
            //register 
            $register_data = $this->registerUserFromEmailPasswordUsernameLongitudeLatitudeAndDeviceToken($useremail, $device_token, $longitude, $latitude, 1,$appVersion);
            return $register_data;
        } else {
            //login  
            if ($response['data'][0]['user_active'] == 0) {
               
                $password = $this->passwordGenerator($useremail);

                $sql = "UPDATE tb_users set user_active=?,password=? Where user_id=?";
                $bindParams = array(1, $password, $response['data'][0]['user_id']);
                $response_user = $this->_DAL->sqlQuery($sql, $bindParams);

                $response1 = $this->getLoginUserData($response['data'][0]['user_id']);
                $versionUpdate=$this->updateVersionFromUserId($response['data'][0]['user_id'],$appVersion);
                
                $user_data = array("device_token" => $device_token, "access_token" => $response1['data'][0]['personal_data'][0]['user_access_token'], "longitude" => $longitude, "latitude" => $latitude);
                $this->updateUserDeviceTokenLoginTimeLongitudeLatitudeFromAccessToken($user_data);

                //$userData=array("user_id"=>$response['data'][0]['user_id']);
                //$result=$this->_playlist->setSessionIdFromUserId($userData);
                // $response1['data'][0]['personal_data'][0]['session_id'] = $result;
                $response1['data'][0]['personal_data'][0]['popup'] = 0;
                $response1['data'][0]['personal_data'][0]['popup_status'] = $this->_DAL->popupStatus;

                return $response1;
            } else {
//                if ($pass == "" || $pass == null) {
//                    $json_error = array("error" => "Please check password from your mail");
//                    echo json_encode($json_error);
//                    exit(1);
//                } else {


                    //$pass = md5($pass);
               
                    $sql = "SELECT user_id FROM tb_users Where user_email=?  LIMIT 1";
                    $bindParams = array($useremail);
                    $response = $this->_DAL->sqlQuery($sql, $bindParams);


                    if (count($response) > 0) {
                        $response1 = $this->getLoginUserData($response['data'][0]['user_id']);
                       
                        $user_data = array("device_token" => $device_token, "access_token" => $response1['data'][0]['personal_data'][0]['user_access_token'], "longitude" => $longitude, "latitude" => $latitude);
                        $this->updateUserDeviceTokenLoginTimeLongitudeLatitudeFromAccessToken($user_data);
                        
                        $versionUpdate=$this->updateVersionFromUserId($response['data'][0]['user_id'],$appVersion);
                       
                        $updateAppPopUp = $this->checkAppVersion($response['data'][0]['user_id']);
                        
                        $sessionResult = $this->_playlist->checkSessionForUserId($response['data'][0]['user_id']);
                        if($response1['data'][0]['personal_data'][0]['is_premium']==0)
                        {
                        $secsData=$this->checkBroadcastDataForUserId($response['data'][0]['user_id'],$response1['data'][0]['personal_data'][0]['broadcast_secs'],$response1['data'][0]['personal_data'][0]['broadcast_start_time']);
                        list($secs,$broadcastTime)=$secsData;
                        $current_date = date("Y-m-d  H:i:s");


                        $outputdate = $this->_funcObj->getDateDifference($current_date, $broadcastTime);


                        list($days, $hours, $minuts, $seconds) = $outputdate;
                        $timeStart = $hours . ":" . $minuts . ":" . $seconds;
                        $timeEnd = '23:60:60';

                        $tResult = $this->_funcObj->timeDifference($timeEnd, $timeStart);
                        
                        }
                        else
                        {
                           $secs=600; 
                           $tResult='23:59:59';
                        }
                        //if want to destory previous session playlist and all playlist sharing data
                        //$user_data=array("access_token" => $response1['data'][0]['personal_data'][0]['user_access_token']);
                        //$responseLogout=  $this->logoutFromUserId($user_data);
                        // $userData=array("user_id"=>$response['data'][0]['user_id']);
                        // $result=$this->_playlist->setSessionIdFromUserId($userData);
                        $response1['data'][0]['personal_data'][0]['broadcast_secs'] = $secs;
                        $response1['data'][0]['personal_data'][0]['expires_in'] = $tResult;
                        $response1['data'][0]['personal_data'][0]['session_exist'] = $sessionResult;
                        $response1['data'][0]['personal_data'][0]['popup'] = $updateAppPopUp;
                        $response1['data'][0]['personal_data'][0]['popup_status'] = $this->_DAL->popupStatus;
                        
                        

                        return $response1;
                    } else {
                        $json_error = array("error" => "Incorrect Username Password!");
                        echo json_encode($json_error);
                        exit(1);
                    }
                //}
            }
        }
    }
    
  public function  checkBroadcastDataForUserId($user_id,$sec,$time)
  {
      
      
      $date1= date("Y-m-d  H:i:s");
      $broadcastTime=$time;
      $dayDiff = $this->_funcObj->getDateDifference($date1, $time);
      list($days, $hours, $minuts, $seconds) = $dayDiff;
      
      if($days>0)
      {
        $sql = "UPDATE tb_users set broadcast_secs=?,broadcast_start_time=? Where user_id=? LIMIT 1";
                $bindParams = array(600,$date1, $user_id);
                $response_user = $this->_DAL->sqlQuery($sql, $bindParams);
                $broadcastTime=$date1;
                $sec=600;
      }
      $dataArray=array($sec,$broadcastTime);
      return $dataArray;
      
  }

    public function getLoginUserData($user_id) {
        $login_data = $this->getUserInfoFromUserid($user_id);


        // $user_data = array("device_token" => $device_token, "access_token" => $login_data['data'][0]['user_access_token'], "longitude" => $longitude, "latitude" => $latitude);
        // $this->updateUserDeviceTokenLoginTimeLongitudeLatitudeFromAccessToken($user_data);


        $response1['data'][0]['personal_data'] = $login_data['data'];

        //$user_data = array("user_id" => $user_id, "lon" => $longitude, "lat" => $latitude);
        //$response1['data'][0]['near_dj'] = $this->nearByDjFromUserId($user_data);
        // $response1['data'][0]['user_Dj_Playlist'] = $this->_playlist->getDjPlayListFromUserId($user_data);



        return $response1;
    }

    /*
     * ------------------------------------------------------
     *  Get facebook user data if registered otherwise register facebook user
     * ------------------------------------------------------
     */

    public function getFbUser($data = array()) {

        $fb_id = $data['fb_id'];
        $fbname = $data['fbname'];
        $device_token = $data['device_token'];
        $longitude = $data['long'];
        $latitude = $data['lat'];
        $appVersion = $data['app_version'];
	$fb_access_token = $data['fb_access_token'];
        $fb_mail = $data['fb_email'];

        $retResult = $this->_funcObj->checkBlank($checkData = array($fb_id, $fbname, $longitude, $latitude));
        if ($retResult == 1) {
            $response = array("error" => "Some Parameter Missing!");
            return $response;
        }
	$json_url = "https://graph.facebook.com/" . $fb_id . "?fields=updated_time&access_token=" . $fb_access_token; 
        // Initializing curl
        $ch = curl_init($json_url);

        // Configuring curl options
        $options = array(CURLOPT_RETURNTRANSFER => true, CURLOPT_HTTPHEADER => array('Content-type: application/json'));

        // Setting curl options
        curl_setopt_array($ch, $options);

        // Getting results
        $result = curl_exec($ch); // Getting jSON result string
        $obj = json_decode($result, true);

        if ($obj['error']) {
            $response = array("error" => "Not An Authenticated User!");
            return $response;
        }

        $sql = "SELECT `user_id`,`user_active` FROM `tb_users` WHERE user_fb_id=? LIMIT 1";
        $bindParams = array($fb_id);
        $response = $this->_DAL->sqlQuery($sql, $bindParams);


        if (count($response) == 0) {
            $register_data = $this->registerFbUserFromFbidFbnameFbusernameLongitudeLatitudeAndDeviceToken($fb_id, $fbname, $device_token, $longitude, $latitude, 1,$appVersion,$fb_mail);
            return $register_data;
        } else {

            if ($result['data'][0]['user_active'] == 0) {
                $sql = "UPDATE tb_users set user_active=? Where user_id=?";
                $bindParams = array(1, $response['data'][0]['user_id']);
                $response_user = $this->_DAL->sqlQuery($sql, $bindParams);
                $versionUpdate=$this->updateVersionFromUserId($response['data'][0]['user_id'],$appVersion);
                
            }
            $isPremiumUser = $this->checkPremiumUserOrNot($fb_mail);
            
            $sql = "UPDATE tb_users set is_premium=?,user_fb_email =? Where user_id=? LIMIT 1";
            $bindParams = array($isPremiumUser,$fb_mail, $response['data'][0]['user_id']);
            $responseUpdate = $this->_DAL->sqlQuery($sql, $bindParams);
            
            $login_data = $this->getUserInfoFromUserid($response['data'][0]['user_id']);

            $user_data = array("device_token" => $device_token, "access_token" => $login_data['data'][0]['user_access_token'], "longitude" => $longitude, "latitude" => $latitude);
            $this->updateUserDeviceTokenLoginTimeLongitudeLatitudeFromAccessToken($user_data);
            $versionUpdate=$this->updateVersionFromUserId($response['data'][0]['user_id'],$appVersion);
            $updateAppPopUp = $this->checkAppVersion($response['data'][0]['user_id']);

            $response1['data'][0]['personal_data'] = $login_data['data'];

            $sessionResult = $this->_playlist->checkSessionForUserId($response['data'][0]['user_id']);

            if($login_data['data'][0]['is_premium']==0)
                        {
                        $secsData=$this->checkBroadcastDataForUserId($response['data'][0]['user_id'],$login_data['data'][0]['broadcast_secs'],$login_data['data'][0]['broadcast_start_time']);
                        list($secs,$broadcastTime)=$secsData;
                        $current_date = date("Y-m-d  H:i:s");


                        $outputdate = $this->_funcObj->getDateDifference($current_date, $broadcastTime);


                        list($days, $hours, $minuts, $seconds) = $outputdate;
                        $timeStart = $hours . ":" . $minuts . ":" . $seconds;
                        $timeEnd = '23:60:60';

                        $tResult = $this->_funcObj->timeDifference($timeEnd, $timeStart);
                        
                        }
                        else
                        {
                           $secs=600; 
                           $tResult='23:59:59';
                        }
                        $response1['data'][0]['personal_data'][0]['broadcast_secs'] = $secs;
                        $response1['data'][0]['personal_data'][0]['expires_in'] = $tResult;
                        $response1['data'][0]['personal_data'][0]['session_exist'] = $sessionResult;
                        $response1['data'][0]['personal_data'][0]['popup'] = $updateAppPopUp;
                        $response1['data'][0]['personal_data'][0]['popup_status'] = $this->_DAL->popupStatus;

            //$user_data = array("user_id" => $response['data'][0]['user_id'], "lon" => $longitude, "lat" => $latitude);
            //  $response1['data'][0]['near_dj'] = $this->nearByDjFromUserId($user_data);
//                        $userData=array("user_id"=>$response['data'][0]['user_id']);
//                        $result=$this->_playlist->setSessionIdFromUserId($userData);
//                        $response1['data'][0]['personal_data'][0]['session_id'] = $result;

            return $response1;
        }
    }

    /*
     * ------------------------------------------------------
     *  Get  user data from access token assigned to user
     * ------------------------------------------------------
     */

    public function getUserAccess($data = array()) {
        $access_token = $data['access_token'];
        $device_token = $data['device_token'];
        $longitude = $data['long'];
        $latitude = $data['lat'];
        $action = $data['action'];
        $appVersion = $data['app_version'];

        //echo $action;



        $retResult = $this->_funcObj->checkBlank($checkData = array($access_token, $longitude, $latitude));
        if ($retResult == 1) {
            $response = array("error" => "Some Parameter Missing!");
            return $response;
        }

        $authenticate = $this->_funcObj->authenticateAccessToken($access_token);


        if (count($authenticate) <= 0) {
            $response = array("error" => "Invalid Access Token!");
            return $response;
        } else {
            $userId = $authenticate['data'][0]['user_id'];
            
           

            $user_data = array("device_token" => $device_token, "access_token" => $access_token, "longitude" => $longitude, "latitude" => $latitude);

            $this->updateUserDeviceTokenLoginTimeLongitudeLatitudeFromAccessToken($user_data);
            $versionUpdate=$this->updateVersionFromUserId($userId,$appVersion);
            $updateAppPopUp = $this->checkAppVersion($userId);


            $login_data = $this->getUserInfoFromUserid($userId);
            
            if($login_data['data'][0]['is_premium']==0)
                        {
                        $secsData=$this->checkBroadcastDataForUserId($userId,$login_data['data'][0]['broadcast_secs'],$login_data['data'][0]['broadcast_start_time']);
                        list($secs,$broadcastTime)=$secsData;
                        $current_date = date("Y-m-d  H:i:s");


                        $outputdate = $this->_funcObj->getDateDifference($current_date, $broadcastTime);


                        list($days, $hours, $minuts, $seconds) = $outputdate;
                        $timeStart = $hours . ":" . $minuts . ":" . $seconds;
                        $timeEnd = '23:60:60';

                        $tResult = $this->_funcObj->timeDifference($timeEnd, $timeStart);
                        
                        }
                        else
                        {
                           $secs=600; 
                           $tResult='23:60:60';
                        }
                        
            $response1['data'][0]['personal_data'][0]['session_exist'] = $sessionResult;
            

            $response1['data'][0]['personal_data'] = $login_data['data'];
            $response1['data'][0]['personal_data'][0]['broadcast_secs'] = $secs;
            $response1['data'][0]['personal_data'][0]['expires_in'] = $tResult;
            $response1['data'][0]['personal_data'][0]['popup'] = $updateAppPopUp;
            $response1['data'][0]['personal_data'][0]['popup_status'] = $this->_DAL->popupStatus;
            
            if($action==null)
            {
            $sessionResult = $this->_playlist->checkSessionForUserId($userId);


            $response1['data'][0]['personal_data'][0]['session_exist'] = $sessionResult;
            }
            
            
            
            
            
            


            if ($action != null && $action == 0) {
                // if want to destory previous session playlist and all playlist sharing data
                $user_data = array("access_token" => $access_token);
                $responseLogout = $this->logoutFromUserId($user_data);
            }










            // $user_data = array("user_id" => $userId, "lon" => $longitude, "lat" => $latitude);
            //$response1['data'][0]['near_dj'] = $this->nearByDjFromUserId($user_data);
//            $response1['data'][0]['user_Dj_Playlist'] = $this->_playlist->getDjPlayListFromUserId($user_data);
//            
//            $response1['data'][0]['personal_data'][0]['session_id'] = $response1['data'][0]['user_Dj_Playlist'][0]['session_id'];
//            if($response1['data'][0]['user_Dj_Playlist'][0]['song_name']=="")
//            {
//             $response1['data'][0]['user_Dj_Playlist']=  array();   
//            }

            return $response1;
        }
    }
    
    
    public function checkAppVersion($userId)
  {
        
      $curAppVersion=103;
        $sql = "SELECT app_version FROM tb_users Where user_id=? LIMIT 1";
        $bindParams = array($userId);
        $responseUser = $this->_DAL->sqlQuery($sql, $bindParams);
        $popup=0;
        if($responseUser['data'][0]['app_version']<$curAppVersion)
        {
            $popup=array("title"=>'Update Version',"text"=>'Update app with new version!',"cur_version"=>$curAppVersion);
        }
        return $popup;
  }

    /*
     * ------------------------------------------------------
     *  register user data 
     * ------------------------------------------------------
     */

    public function registerUserFromEmailPasswordUsernameLongitudeLatitudeAndDeviceToken($useremail, $device_token, $longitude, $latitude, $flag,$version) {


        $access_token = base64_encode($useremail);
        $username = explode("@", $useremail);
        $username = $username[0];
        $imgname = 'user.png';


        if ($flag == 1) {
            $password_db = $this->passwordGenerator($useremail);
            $active = 1;
        } else {
            $password_db = "";
            $active = 0;
        }
        
        $isPremiumUser = $this->checkPremiumUserOrNot($useremail);


        $reg_date = date("Y-m-d : H:i:s");
        $sql = "INSERT into tb_users(user_email,user_name,password,user_device_token,user_access_token,reg_date,user_image,longitude,latitude,user_active,broadcast_secs,broadcast_start_time,app_version,is_premium) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $bindParams = array($useremail, $username, $password_db, $device_token, $access_token, $reg_date, $imgname, $longitude, $latitude, $active,600,$reg_date,$version,$isPremiumUser);
        $response = $this->_DAL->sqlQuery($sql, $bindParams);

        $response1['data'][0]['personal_data'][0]['user_id'] = $response;
        $response1['data'][0]['personal_data'][0]['user_name'] = $username;
        $response1['data'][0]['personal_data'][0]['user_access_token'] = $access_token;
        $response1['data'][0]['personal_data'][0]['user_image'] = $this->_DAL->ImageBasePath . $this->_DAL->UserImageFolder . $imgname;
        $response1['data'][0]['personal_data'][0]['is_premium'] = $isPremiumUser;
        $response1['data'][0]['personal_data'][0]['broadcast_secs'] = 600;
        $response1['data'][0]['personal_data'][0]['expires_in'] = '23:59:59';
        $response1['data'][0]['personal_data'][0]['new_reg'] = 1;
        $response1['data'][0]['personal_data'][0]['popup'] = 0;
        $response1['data'][0]['personal_data'][0]['popup_status'] = $this->_DAL->popupStatus;
        // $userData=array("user_id"=>$response);
        // $result=$this->_playlist->setSessionIdFromUserId($userData);
        //  $response1['data'][0]['personal_data'][0]['session_id'] = $result;
        // if ($flag == 1) {
        // $user_data = array("user_id" => $response, "lon" => $longitude, "lat" => $latitude);
        // $response1['data'][0]['near_dj'] = $this->nearByDjFromUserId($user_data);
        //}

        return $response1;
    }
    
    /*
     * ------------------------------------------------------
     *  check if user is in premium version table
     * ------------------------------------------------------
     */
    
     public function checkPremiumUserOrNot($useremail) {
     
        $sql = "SELECT id FROM tb_premium_users Where email=? LIMIT 1";
        $bindParams = array($useremail);
        $response = $this->_DAL->sqlQuery($sql, $bindParams);
        
        if(count($response)>0)
        {
            return 1;
        }
        else
        {
           return 0; 
        }
         
     }
    /*
     * ------------------------------------------------------
     *  register fb user data 
     * ------------------------------------------------------
     */

    public function registerFbUserFromFbidFbnameFbusernameLongitudeLatitudeAndDeviceToken($fb_id, $fbname, $device_token, $longitude, $latitude, $flag,$version,$useremail) {

        $picture = "https://graph.facebook.com/" . $fb_id . "/picture?width=168&height=168";
        $access_token = base64_encode($fb_id);
        $reg_date = date("Y-m-d : H:i:s");

        if ($flag == 1) {

            $active = 1;
        } else {

            $active = 0;
        }
        $isPremiumUser = 0;
        if($useremail != '')
        {
        $isPremiumUser = $this->checkPremiumUserOrNot($useremail);
        }
        $sql = "INSERT into tb_users(user_fb_id,user_name,user_device_token,user_access_token,reg_date,user_image,longitude,latitude,user_active,broadcast_secs,broadcast_start_time,app_version,is_premium,user_fb_email) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $bindParams = array($fb_id, $fbname, $device_token, $access_token, $reg_date, $picture, $longitude, $latitude, $active,600,$reg_date,$version,$isPremiumUser,$useremail);
        $response = $this->_DAL->sqlQuery($sql, $bindParams);

        $response1['data'][0]['personal_data'][0]['user_id'] = $response;
        $response1['data'][0]['personal_data'][0]['user_name'] = $fbname;
        $response1['data'][0]['personal_data'][0]['user_access_token'] = $access_token;
        $response1['data'][0]['personal_data'][0]['user_image'] = $picture;
        $response1['data'][0]['personal_data'][0]['is_premium'] = $isPremiumUser;
        $response1['data'][0]['personal_data'][0]['broadcast_secs'] = 600;
        $response1['data'][0]['personal_data'][0]['expires_in'] = '23:59:59';
        $response1['data'][0]['personal_data'][0]['new_reg'] = 1;
        $response1['data'][0]['personal_data'][0]['popup'] = 0;
        $response1['data'][0]['personal_data'][0]['popup_status'] = $this->_DAL->popupStatus;
        //   $userData=array("user_id"=>$response);
        //  $result=$this->_playlist->setSessionIdFromUserId($userData);
        //  $response1['data'][0]['personal_data'][0]['session_id'] = $result;
        // if ($flag == 1) {
        //    $user_data = array("user_id" => $response, "lon" => $longitude, "lat" => $latitude);
        //    $response1['data'][0]['near_dj'] = $this->nearByDjFromUserId($user_data);
        // }

        return $response1;
    }

    /*
     * ------------------------------------------------------
     *  get user data from user id 
     * ------------------------------------------------------
     */

    public function getUserInfoFromUserid($userid) {

        $sql = "SELECT user_id,user_name,user_access_token,user_image,user_fb_id,user_email,is_premium,broadcast_secs,broadcast_start_time FROM tb_users Where user_id=? LIMIT 1";
        $bindParams = array($userid);
        $response = $this->_DAL->sqlQuery($sql, $bindParams);



        if ($response['data'][0]['user_fb_id'] != 0) {
            $image = $response['data'][0]['user_image'];
        } else {
            $image = $this->_DAL->ImageBasePath . $this->_DAL->UserImageFolder . $response['data'][0]['user_image'];
        }

        $response['data'][0]['user_image'] = $image;
        unset($response['data'][0]['user_fb_id']);

        $response['data'][0]['new_reg'] = 0;
        return $response;
    }

    /*
     * ------------------------------------------------------
     *  update user data as login time device token etc
     * ------------------------------------------------------
     */

    public function updateUserDeviceTokenLoginTimeLongitudeLatitudeFromAccessToken($user_data) {

        // print_r($user_data);
        $login_date = date("Y-m-d : H:i:s");

        $sql = "UPDATE tb_users set user_device_token=?,last_login_date=?,longitude=?,latitude=? Where user_access_token=? LIMIT 1";
        $bindParams = array($user_data['device_token'], $login_date, $user_data['longitude'], $user_data['latitude'], $user_data['access_token']);



        $response = $this->_DAL->sqlQuery($sql, $bindParams);

        return $response;
    }

    /*
     * ------------------------------------------------------
     *  Get friends of facebook
     * ------------------------------------------------------
     */

    public function getFbFriendsFromFbidAndFbAccessToken($data = array()) {
        $fb_access_token = $data['fb_access_token'];
        $access_token = $data['access_token'];
        $fb_id = $data['fb_id'];



        $retResult = $this->_funcObj->checkBlank($data);
        if ($retResult == 1) {
            $response = array("error" => "Some Parameter Missing!");
            return $response;
        }

        $authenticate = $this->_funcObj->authenticateAccessToken($access_token);


        if (count($authenticate) <= 0) {
            $response = array("error" => "Invalid Access Token!");
            return $response;
        } else {


            $json_url = "https://graph.facebook.com/" . $fb_id . "/friends?access_token=" . $fb_access_token;

            $ch = curl_init($json_url);

            // Configuring curl options
            $options = array(CURLOPT_RETURNTRANSFER => true, CURLOPT_HTTPHEADER => array('Content-type: application/json'));

            // Setting curl options
            curl_setopt_array($ch, $options);

            // Getting results
            $result = curl_exec($ch); // Getting jSON result string

            $obj = json_decode($result, true);


            $sql = "SELECT user_fb_id from tb_users WHERE user_fb_id!=''";

            $bindParams = array();
            $response1 = $this->_DAL->sqlQuery($sql, $bindParams);

            $i = 0;
            foreach ($response1['data'] as $row_fb_id) {
                $res[$i] = $row_fb_id['user_fb_id'];

                $i++;
            }


            //getting user fb friends who are registered
            foreach ($obj['data'] as $key => $value) {

                foreach ($value as $key1 => $value1) {
                    if ($key1 == 'name') {

                        $name = $value1;
                    }
                    if ($key1 == 'id') {

                        if (in_array($value1, $res)) {

                            if (count($json_array) <= 0) {

                                $json_array = array(array("name" => $name, "id" => $value1));
                            } else {

                                $array = array(array("name" => $name, "id" => $value1));
                                $json_array = array_merge($json_array, $array);
                            }
                        } else {
                            if (count($json_array1) <= 0) {

                                $json_array1 = array(array("name" => $name, "id" => $value1));
                            } else {

                                $array = array(array("name" => $name, "id" => $value1));
                                $json_array1 = array_merge($json_array1, $array);
                            }
                        }
                    }
                }
            }
            if (count($json_array) == 0)
                $json_array = array();
            if (count($json_array1) == 0)
                $json_array1 = array();
            $final_array = array("registered" => $json_array, "not registered" => $json_array1);
            return $final_array;
        }

        return $response;
    }

    /*
     * ------------------------------------------------------
     *  find near by user based on distance calculated 
     * ------------------------------------------------------
     */

    public function getNearByDjFromUserId($data = array()) {

        $access_token = $data['access_token'];
        $latitude = $data['lat'];
        $longitude = $data['lon'];


        $retResult = $this->_funcObj->checkBlank($checkData = array($access_token, $longitude, $latitude));
        if ($retResult == 1) {
            $response = array("error" => "Some Parameter Missing!");
            return $response;
        }

        $authenticate = $this->_funcObj->authenticateAccessToken($access_token);


        if (count($authenticate) <= 0) {
            $response = array("error" => "Invalid Access Token!");
            return $response;
        } else {
            $userId = $authenticate['data'][0]['user_id'];
        }

        $data['user_id'] = $userId;
        $sql = "UPDATE tb_users set longitude=?,latitude=? Where user_id=? LIMIT 1";
        $bindParams = array($longitude, $latitude, $userId );



        $response1 = $this->_DAL->sqlQuery($sql, $bindParams);

        $response['data'][0]['near_dj'] = $this->nearByDjFromUserId($data);

        return $response;
    }

    public function nearByDjFromUserId($data = array()) {
        $userid = $data['user_id'];
        $lat1 = $data['lat'];
        $lon1 = $data['lon'];
        
        
        $uniqueDjIds=$this->_playlist->getUniqueDjIdsFromUserId($userid);
       
	   if($uniqueDjIds==0)
	   {
		   return array();
		   }
        for($i=0;$i<count($uniqueDjIds);$i++)
        {
          $userIds[$i]=$uniqueDjIds[$i]['dj_user_id'];  
        }
        
        
       // print_r($uniqueDjIds);
       
        $placeholders = rtrim(str_repeat('?, ', count($userIds)), ', ');
        
      
        $sql = "SELECT user_id,user_name,user_image,user_fb_id,longitude,latitude FROM tb_users WHERE user_id in ($placeholders) "; //and datediff(CURDATE(), DATE(last_login_date)) <= 30";
        $bindParams = $userIds;
        $response = $this->_DAL->sqlQuery($sql, $bindParams);
        
       
		
        if (count($response) == 0) {
            return array();
        }



        $i = 0;
        foreach ($response['data'] as $row) {
            
           /* if($row['longitude']=='0.000000' && $row['latitude']=='0.000000')
            {
                $toDeleteRows[]=$i;
            }*/

            
            $image = '';
            if ($row['user_fb_id'] != 0) {
                $image = $row['user_image'];
            } else {
                $image = $this->_DAL->ImageBasePath . $this->_DAL->UserImageFolder . $row['user_image'];
            }

            $response['data'][$i]['user_image'] = $image;
            
           // $key=array_search($row['user_id'], $userIds);
           // echo $key;
            $response['data'][$i]['share_status']=$uniqueDjIds[$i]['share_status'];
           

            $response['data'][$i]['distance'] = $this->_funcObj->toMiles($lat1, $lon1, $row['latitude'], $row['longitude']);
            unset($response['data'][$i]['longitude']);
            unset($response['data'][$i]['latitude']);
            unset($response['data'][$i]['user_fb_id']);
            $i++;
        }
		
        
        
        foreach ($response['data'] as $key => $row) {
            $volume[$key] = $row['distance'];
            $edition[$key] = $row['share_status'];
        }


        array_multisort($edition,SORT_DESC,$volume, SORT_ASC, $response['data']);
        
        


        return $response['data'];
    }

    /*
     * ------------------------------------------------------
     *  Forgot Password (Password send to mail)
     * ------------------------------------------------------
     */

    public function forgotPasswordFromEmail($data = array()) {

        $email = $data['email'];

        $retResult = $this->_funcObj->checkBlank($data);
        if ($retResult == 1) {
            $response = array("error" => "Some Parameter Missing!");
            return $response;
        }


        //email id valid
        $sql = "SELECT user_id,password FROM tb_users where user_email=? limit 1";
        $bindParams = array($email);
        $response_id = $this->_DAL->sqlQuery($sql, $bindParams);



        if (count($response_id) == 0) {

            $json_error = array("error" => "Email address not found");
            return $json_error;
        }


        //password generation
        $length = 6;
        $alphabets = range('A', 'Z');
        $numbers = range('0', '9');
        //	$additional_characters = array('_','.');
        $final_array = array_merge($alphabets, $numbers);

        $password = '';

        while ($length--) {
            $key = array_rand($final_array);
            $password .= $final_array[$key];
            $password_db = md5($password);
        }
        //password generation

        $sql = "UPDATE tb_users set password=? WHERE user_id=? LIMIT 1";
        $bindParams = array($password_db, $response_id['data'][0]['user_id']);
        $response = $this->_DAL->sqlQuery($sql, $bindParams);
        if ($response == 0) {
            $array = array("error" => "Something went wrong");
            return $array;
        } else {



            $game_name = "Boombotix";
            $to = $email;
            $sub = "Password Mail";
            $msg = "Your password for " . $game_name . " profile.<br><br>" .
                    "Email: " . $email . "<br>Password: " . $password .
                    "<br><br> Thanks <br> BOOMBOTIX Team";


            $result_mail = $this->_funcObj->sendMailFromReceiveridSubjectAndBody($to, $sub, $msg);


            if ($result_mail == 'true') {

                $array = array("log" => "Password sent to mail");
                return $array;
            } else {
                $array = array("log" => "Password  not sent to mail");
                return $array;
            }
        }
    }

    /*
     * ------------------------------------------------------
     *  share dj playlist with friends
     * ------------------------------------------------------
     */

    public function shareDjPlayListFromUserIdToFriendId($data = array()) {

        $accessToken = $data['access_token'];
        if ($data['fb_id']) {
            $inviteId = $data['fb_id'];
            $inviteName = $data['fbname'];
        }

        if ($data['user_email']) {
            $inviteId = $data['user_email'];
        }

        //$sessionId = $data['session_id'];

        $inviteBy = $data['invite_by'];


        if ($inviteBy == 'fb') {
            $checkData = array($accessToken, $inviteId, $inviteBy, $inviteName);
        } else if ($inviteBy == 'email') {
            $checkData = array($accessToken, $inviteId, $inviteBy);
        }



        $retResult = $this->_funcObj->checkBlank($checkData);
        if ($retResult == 1) {
            $response = array("error" => "Some Parameter Missing!");
            return $response;
        }

        $authenticate = $this->_funcObj->authenticateAccessToken($accessToken);


        if (count($authenticate) == 0) {
            $response = array("error" => "Invalid Access Token!");
            return $response;
        } else {
            $userId = $authenticate['data'][0]['user_id'];




            if ($inviteBy == 'fb') {
                $userIdFriend = $this->checkUserFromFbIdOrUserEmail($inviteId, $inviteBy, $inviteName);
            } else if ($inviteBy == 'email') {
                $userIdFriend = $this->checkUserFromFbIdOrUserEmail($inviteId, $inviteBy);
            }

            $sessionId = $this->_playlist->setSessionIdFromUserId($userId);

            $response = $this->_playlist->saveUserPlayList($sessionId, $userId, $userIdFriend,0);

            if ($inviteBy == 'email') {
                $game_name = "Boombotix";
                $to = $inviteId;
                $sub = "Invitation Mail";
                $msg = "Some one shared playlist with you in " . $game_name . "<br><br>";



                $result_mail = $this->_funcObj->sendMailFromReceiveridSubjectAndBody($to, $sub, $msg);
            }




            return $response;
        }
    }

    /*
     * ------------------------------------------------------
     *  check weather a user is registered or not
     * ------------------------------------------------------
     */

    public function checkUserFromFbIdOrUserEmail($inviteId, $inviteBy, $inviteName) {
        if ($inviteBy == 'fb') {
            $sql = "SELECT user_id FROM tb_users WHERE user_fb_id=? LIMIT 1";
        } else if ($inviteBy == 'email') {
            $sql = "SELECT user_id FROM tb_users WHERE user_email=? LIMIT 1";
        }
        $bindParams = array($inviteId);
        $responseCheckId = $this->_DAL->sqlQuery($sql, $bindParams);

        if (count($responseCheckId) == 0) {
            if ($inviteBy == 'email') {
                $responseId = $this->registerUserFromEmailPasswordUsernameLongitudeLatitudeAndDeviceToken($inviteId, "", "", "", 0,0);
                return $responseId['data'][0]['personal_data'][0]['user_id'];
            } else {
                $responseId = $this->registerFbUserFromFbidFbnameFbusernameLongitudeLatitudeAndDeviceToken($inviteId, $inviteName, "", "", "", 0,0);
                return $responseId['data'][0]['personal_data'][0]['user_id'];
            }
        } else {
            return $responseCheckId['data'][0]['user_id'];
        }
    }

    /*
     * ------------------------------------------------------
     *  Generating Password (Password send to mail)
     * ------------------------------------------------------
     */

    public function passwordGenerator($useremail) {
        $length = 6;
        $alphabets = range('A', 'Z');
        $numbers = range('0', '9');
        //	$additional_characters = array('_','.');
        $final_array = array_merge($alphabets, $numbers);

        $password = '';

        while ($length--) {
            $key = array_rand($final_array);
            $password .= $final_array[$key];
        }
        //password generation


        $password_db = md5($password);


        $game_name = "Boombotix";
        $to = $useremail;
        $sub = "Password Mail";
        $msg = "Your password for " . $game_name . " profile.<br><br>" .
                "Email: " . $useremail . "<br>Password: " . $password .
                "<br><br> Thanks <br> BOOMBOTIX Team";


        $result_mail = $this->_funcObj->sendMailFromReceiveridSubjectAndBody($to, $sub, $msg);
        return $password_db;
    }

    /*
     * ------------------------------------------------------
     *  Getting shared friends list 
     * ------------------------------------------------------
     */

    public function getFriendsSharedWithFromUserId($data = array()) {
        $access_token = $data['access_token'];
        $mode = $data['mode'];


        $retResult = $this->_funcObj->checkBlank($data);
        if ($retResult == 1) {
            $response = array("error" => "Some Parameter Missing!");
            return $response;
        }

        $authenticate = $this->_funcObj->authenticateAccessToken($access_token);


        if (count($authenticate) == 0) {
            $response = array("error" => "Invalid Access Token!");
            return $response;
        } else {
            $userId = $authenticate['data'][0]['user_id'];

            $userIdFriends = $this->_playlist->getSharedFriendsIds($userId, $mode);

            $sharedFriendResponse = array();


            for ($i = 0; $i < count($userIdFriends['data']); $i++) {
                if ($mode == 'dj') {
                    $userData = $this->getUserInfoFromUserid($userIdFriends['data'][$i]['listner_user_id']);
                } else {
                    $online=$this->getOnlineDjFromDjId($userIdFriends['data'][$i]['dj_user_id']);
                    $userData = $this->getUserInfoFromUserid($userIdFriends['data'][$i]['dj_user_id']);
                }

                $sharedFriendResponse['data'][$i] = $userData['data'][0];
                if ($mode == 'listen') {
                    if ($userIdFriends['data'][$i]['status'] == 0) {
                        $sharedFriendResponse['data'][$i]['share_status'] = 1;
                    } else {
                        $sharedFriendResponse['data'][$i]['share_status'] = $userIdFriends['data'][$i]['status'];
                    }
                    $sharedFriendResponse['data'][$i]['online'] = $online;
                } else {
                    if ($userIdFriends['data'][$i]['status'] == 3) {
                        $sharedFriendResponse['data'][$i]['share_status'] = 1;
                    }  else {
                        $sharedFriendResponse['data'][$i]['share_status'] = $userIdFriends['data'][$i]['status'];
                    }
                    
                }

                unset($sharedFriendResponse['data'][$i]['new_reg']);
            }


            return $sharedFriendResponse;
        }
    }

    /*
     * ------------------------------------------------------
     *  Accepting friend request and getting playlist
     * ------------------------------------------------------
     */

    public function acceptRequestFromUserId($data = array()) {
        $access_token = $data['access_token'];
        $dj_id = $data['dj_id'];


        $retResult = $this->_funcObj->checkBlank($data);
        if ($retResult == 1) {
            $response = array("error" => "Some Parameter Missing!");
            return $response;
        }

        $authenticate = $this->_funcObj->authenticateAccessToken($access_token);


        if (count($authenticate) == 0) {
            $response = array("error" => "Invalid Access Token!");
            return $response;
        } else {
            $userId = $authenticate['data'][0]['user_id'];

            $sql = "SELECT dj_user_id FROM tb_playlist_share WHERE listner_user_id=? && status=? LIMIT 1";
            $bindParams = array($userId, 2);
            $responsePreviousDj = $this->_DAL->sqlQuery($sql, $bindParams);
            if (count($responsePreviousDj) > 0) {
                $sql = "UPDATE tb_playlist_share set status=? Where dj_user_id=? && listner_user_id=? LIMIT 1";
                $bindParams = array(0, $responsePreviousDj['data'][0]['dj_user_id'], $userId);
                $responseUserUpdate = $this->_DAL->sqlQuery($sql, $bindParams);
            }

            $sql = "UPDATE tb_playlist_share set status=? Where dj_user_id=? && listner_user_id=? LIMIT 1";
            $bindParams = array(2, $dj_id, $userId);
            $responseUser = $this->_DAL->sqlQuery($sql, $bindParams);

            $data = array("user_id" => $dj_id);
            $djPlayList = $this->_playlist->getDjPlayListFromUserId($data);
            $response['data'] = $djPlayList;
            return $response;
        }
    }

    /*
     * ------------------------------------------------------
     *  User logout from current session ,archiving the user current session list and shared friends ids
     * ------------------------------------------------------
     */

    public function logoutFromUserId($data = array()) {

        //  print_r($data);
        $access_token = $data['access_token'];

        // echo $access_token;

        $retResult = $this->_funcObj->checkBlank($data);
        if ($retResult == 1) {
            $response = array("error" => "Some Parameter Missing!");
            return $response;
        }

        $authenticate = $this->_funcObj->authenticateAccessToken($access_token);


        if (count($authenticate) == 0) {
            $response = array("error" => "Invalid Access Token!");
            return $response;
        } else {
            $userId = $authenticate['data'][0]['user_id'];

            //check if user has not shared its playlist with anyone then not store its playlist for previous session




            $res1 = $this->transferTableData('tb_playlist_share', $userId, 'dj_user_id');

            //if user have no users in shared list then not archive the playlist just delete
            if ($res1 == 0) {
                $sql = "DELETE from tb_user_playlist where user_id=?";
                $bindParams = array($userId);
                $response = $this->_DAL->sqlQuery($sql, $bindParams);
            } else {



                $res2 = $this->transferTableData('tb_user_playlist', $userId, 'user_id');
            }



            $sql1 = "UPDATE tb_users set playlist_order=? Where user_id=? limit 1";
            $bindParams1 = array('', $userId);
            $response1 = $this->_DAL->sqlQuery($sql1, $bindParams1);


            $response = array("log" => "Logged Out Successfully!");
            return $response;
        }
    }

    /*
     * ------------------------------------------------------
     *  Archiving the user current session list and shared friends ids
     * ------------------------------------------------------
     */

    public function transferTableData($table, $userId, $field) {



        $sql = "SELECT * from " . $table . " where " . $field . "=?";
        $bindParams = array($userId);
        $row = $this->_DAL->sqlQuery($sql, $bindParams);

        if (count($row) == 0) {
            return 0;
        }

        //echo count($row);
        //Call the function to archive the table
        //Function definition is given below
        else if (count($row) != 0) {


            $this->archiveRecord($table . "_archive", $row);
        }

        //Once you archive, delete the record from original table


        if (count($row) != 0 || $table == 'tb_user_playlist') {

            $sql = "DELETE from " . $table . " where " . $field . "=?";
            $bindParams = array($userId);
            $response = $this->_DAL->sqlQuery($sql, $bindParams);
        }
        return 1;
    }

    /*
     * ------------------------------------------------------
     *  Archiving the user current session list and shared friends ids
     * ------------------------------------------------------
     */

    public function archiveRecord($archived_tablename, $row) {


        $curDate = date("Y-m-d H:i:s");
        if ($archived_tablename == 'tb_user_playlist_archive') {
            $sql = "INSERT into " . $archived_tablename . " values(?,?,?,?,?,?,?,?,?,?)";
            for ($i = 0; $i < count($row['data']); $i++) {
                if ($row['data'][$i]['song_name'] == "") {
                    continue;
                }
                $bindParams = array($row['data'][$i]['playlist_id'], $row['data'][$i]['user_id'],
                    $row['data'][$i]['song_name'], $row['data'][$i]['song_artist'], $row['data'][$i]['song_image'],
                    $row['data'][$i]['song_link'], $row['data'][$i]['song_itunes_link'], $row['data'][$i]['session_id'],
                    $curDate, $row['data'][$i]['song_status']);
                $response = $this->_DAL->sqlQuery($sql, $bindParams);
            }
        } elseif ($archived_tablename == 'tb_playlist_share_archive') {
            $sql = "INSERT into " . $archived_tablename . " values(?,?,?,?,?)";
            for ($i = 0; $i < count($row['data']); $i++) {
                $bindParams = array($row['data'][$i]['session_id'], $row['data'][$i]['dj_user_id'],
                    $row['data'][$i]['listner_user_id'], $row['data'][$i]['status'], $curDate);
                $response = $this->_DAL->sqlQuery($sql, $bindParams);
            }
        }
    }

    /*
     * ------------------------------------------------------
     *  Clear the user current playlist without destroy session id and sharing list 
     * ------------------------------------------------------
     */

    public function clearPlaylistFromUserAccessToken($data = array()) {
        $access_token = $data['access_token'];

        $retResult = $this->_funcObj->checkBlank($data);
        if ($retResult == 1) {
            $response = array("error" => "Some Parameter Missing!");
            return $response;
        }

        $authenticate = $this->_funcObj->authenticateAccessToken($access_token);


        if (count($authenticate) == 0) {
            $response = array("error" => "Invalid Access Token!");
            return $response;
        } else {
            $userId = $authenticate['data'][0]['user_id'];

            $sql = "SELECT session_id,song_image,song_link,song_status from tb_user_playlist where user_id=?";
            $bindParams = array($userId);
            $responseSessionId = $this->_DAL->sqlQuery($sql, $bindParams);

            if (count($responseSessionId) != 0) {
                $userSessionId = $responseSessionId['data'][0]['session_id'];

                foreach ($responseSessionId['data'] as $row) {

                    if ($row['song_status'] == 0) {
                        $song_link = $this->_DAL->mp3ImageFolder . $row['song_link'];

                        if (!($this->_s3->deleteObject("boom-botix", $song_link))) {
                            echo json_encode(array("error" => "Unable to clear playlist!"));
                            exit(1);
                        }
						
						$song_image = $this->_DAL->SongImageFolder . $row['song_image'];

                    //$this->_s3->deleteObject("boom-botix", $song_image);

                    if ($responseDataSong['data'][0]['song_image'] != 'default.png') {
                        if (!($this->_s3->deleteObject("boom-botix", $song_image))) {
                            echo json_encode(array("error" => "Unable to clear playlist!"));
                            exit(1);
                        }
                    }
                    }

                    
                }



                $sql = "DELETE from tb_user_playlist where user_id=?";
                $bindParams = array($userId);
                $responseDel = $this->_DAL->sqlQuery($sql, $bindParams);

                if (!$responseDel) {
                    $response = array("error" => "Unable to clear playlist!");
                    return $response;
                }

                $playlistCreatedDatetime = date("Y-m-d : H:i:s");

                $sql = "INSERT into tb_user_playlist(user_id,session_id,playlist_created_datetime) values(?,?,?)";
                $bindParams = array($userId, $userSessionId, $playlistCreatedDatetime);
                $response = $this->_DAL->sqlQuery($sql, $bindParams);
            }

            $sql1 = "UPDATE tb_users set playlist_order=? Where user_id=? limit 1";
            $bindParams1 = array('', $userId);
            $response1 = $this->_DAL->sqlQuery($sql1, $bindParams1);


            $response = array("log" => "Playlist cleared!");
            return $response;
        }
    }

    /*
     * ------------------------------------------------------
     *  Getting previous playlist shared and friends ids
     * ------------------------------------------------------
     */

    public function getPreviousSessionFromUserId($data = array()) {

        $accessToken = $data['access_token'];


        $retResult = $this->_funcObj->checkBlank($data);
        if ($retResult == 1) {
            $response = array("error" => "Some Parameter Missing!");
            return $response;
        }

        $authenticate = $this->_funcObj->authenticateAccessToken($accessToken);


        if (count($authenticate) == 0) {
            $response = array("error" => "Invalid Access Token!");
            return $response;
        } else {
            $userId = $authenticate['data'][0]['user_id'];

            $sql = "SELECT dj_user_id,session_id,share_datetime FROM tb_playlist_share_archive where listner_user_id=?";
            $bindParams = array($userId);
            $responseDjIds = $this->_DAL->sqlQuery($sql, $bindParams);

            if (count($responseDjIds) == 0) {
                return array("log" => "No previous playlist");
            }
	    foreach ($responseDjIds['data'] as $key => $row) {
                $score[$key] = ($row['share_datetime']);
            }

            array_multisort($score, SORT_DESC, $responseDjIds['data']);

            $responseDjIds['data'] = array_slice($responseDjIds['data'], 0, 5);


            $i = 0;
            foreach ($responseDjIds['data'] as $value) {
                $userData = $this->getUserInfoFromUserid($value['dj_user_id']);
                $userData['data'][0]['session_id'] = $value['session_id'];
                // $playlistData = $this->_playlist->getPreviousSessionDjPlayListFromUserIdAndSessionId($value['dj_user_id'], $value['session_id']);
                $response[$i] = $userData['data'][0];


                unset($response[$i]['user_access_token']);
                unset($response[$i]['new_reg']);
                //$response['data'][$i]['dj_playlist'] = $playlistData;
                $i++;
            }
            $responsePre['data'][0]['dj_data'] = $response;
            return $responsePre;
        }
    }

    /*
     * ------------------------------------------------------
     *  Getting connecting friends of current user
     * ------------------------------------------------------
     */

    public function getconnectionFromUserId($data = array()) {

        $accessToken = $data['access_token'];
        $Dj_id = $data['dj_id'];


        $retResult = $this->_funcObj->checkBlank($data);
        if ($retResult == 1) {
            $response = array("error" => "Some Parameter Missing!");
            return $response;
        }

        $authenticate = $this->_funcObj->authenticateAccessToken($accessToken);


        if (count($authenticate) == 0) {
            $response = array("error" => "Invalid Access Token!");
            return $response;
        } else {
            $userId = $authenticate['data'][0]['user_id'];

            $sql = "SELECT dj_user_id FROM tb_playlist_share where listner_user_id=? and status=?  LIMIT 1";
            $bindParams = array($userId, 2);
            $responseDjId = $this->_DAL->sqlQuery($sql, $bindParams);

            if (count($responseDjId) == 0) {
                $response = array("error" => "Not Listening to any Dj");
                return $response;
            } else {
                $sql = "SELECT listner_user_id FROM tb_playlist_share where dj_user_id=?";
                $bindParams = array($responseDjId['data'][0]['dj_user_id']);
                $responseUserIds = $this->_DAL->sqlQuery($sql, $bindParams);

                // print_r($responseUserIds);

                if (count($responseUserIds) == 0) {
                    $response = array("error" => "No Connections");
                    return $response;
                } else {
                    $i = 0;
                    foreach ($responseUserIds['data'] as $value) {
                        $userData = $this->getUserInfoFromUserid($value['listner_user_id']);

                        $response[$i] = $userData['data'][0];
                        // $responseConnection['data'][$i]['connections']=$response[$i];
                        unset($response[$i]['user_access_token']);
                        unset($response[$i]['new_reg']);

                        $i++;
                    }
                    $responseConnection['data'][0]['friends'] = $response;
                }
            }
            // print_r($responseConnection);
            return $responseConnection;
        }
    }
/*
     * ------------------------------------------------------
     *  Userd for shuffling the playlist of user
     * ------------------------------------------------------
     */
    public function setPlaylistOrderForUser($data = array()) {
        $accessToken = $data['access_token'];
        $shuffle_order = $data['order'];



        $retResult = $this->_funcObj->checkBlank($data);
        if ($retResult == 1) {
            $response = array("error" => "Some Parameter Missing!");
            return $response;
        }

        $authenticate = $this->_funcObj->authenticateAccessToken($accessToken);


        if (count($authenticate) == 0) {
            $response = array("error" => "Invalid Access Token!");
            return $response;
        } else {




            $userId = $authenticate['data'][0]['user_id'];


            $sql2 = "SELECT playlist_order from tb_users  Where user_id=? limit 1";
            $bindParams2 = array($userId);
            $response2 = $this->_DAL->sqlQuery($sql2, $bindParams2);




            if ($response2['data'][0]['playlist_order'] != "") {

                $sql1 = "UPDATE tb_users set playlist_order=? Where user_id=? limit 1";
                $bindParams1 = array('', $userId);
                $response1 = $this->_DAL->sqlQuery($sql1, $bindParams1);

                if (!$response1) {
                    $response1 = array("error" => "Unable To Update Shuffle Order!");
                    return $response1;
                }
            }



            $sql = "UPDATE tb_users set playlist_order=? Where user_id=? limit 1";
            $bindParams = array($shuffle_order, $userId);
            $response = $this->_DAL->sqlQuery($sql, $bindParams);
            if (!$response) {
                $response = array("error" => "Unable To Update Shuffle Order!");
                return $response;
            } else {
                $response = array("log" => "Shuffle Order Updated Successfully!");
                return $response;
            }
        }
    }


    /*
     * ------------------------------------------------------
     *  Requesting dj to allow the user to listen his music
     * ------------------------------------------------------
     */
public function requestDjFromUserIdAndDjId($data = array()) {
        $accessToken = $data['access_token'];
        $djId = $data['dj_id'];
         

        $retResult = $this->_funcObj->checkBlank($checkData = array($accessToken, $djId));
        if ($retResult == 1) {
            $response = array("error" => "Some Parameter Missing!");
            return $response;
        }

        $authenticate = $this->_funcObj->authenticateAccessToken($accessToken);


        if (count($authenticate) == 0) {
            $response = array("error" => "Invalid Access Token!");
            return $response;
        } else {

            $userId = $authenticate['data'][0]['user_id'];
            
            $sql = "SELECT id FROM tb_bot_counter WHERE dj_id=? LIMIT 1";
            $bindParams = array($djId);
            $responseBotDj = $this->_DAL->sqlQuery($sql, $bindParams);
            if(count($responseBotDj)>0)
            {
                $canShare = 1;
            }
           else
           {
            $canShare=$this->checkingDjCanSharePlaylistOrNot($djId,$userId);
           }
           
            if($canShare!=0)
            {
            
            
            
            $sql = "SELECT dj_user_id,status FROM tb_playlist_share WHERE listner_user_id=?";
            $bindParams = array($userId);
            $responsePreviousDj = $this->_DAL->sqlQuery($sql, $bindParams);
           
            
            $curDj=0;
            $flag=0;
            foreach($responsePreviousDj['data'] as $val)
            {
               
                if($val['dj_user_id']==$djId && $val['status']==2)
                {
                    
                   $response = array("error" => "Already joined!"); 
                   return $response; 
                }
                if($val['dj_user_id']!=$djId && $val['status']==2)
                {
                     
                    $curDj=$val['dj_user_id'];
                }
                
                if($val['dj_user_id']==$djId && $val['status']!=2)
                {
                    
                    $flag=1;
                }
                
            }
            if ($curDj != 0) {
                
               
                $sql = "UPDATE tb_playlist_share set status=? Where dj_user_id=? && listner_user_id=? LIMIT 1";
                $bindParams = array(0, $curDj, $userId);
                $responseUserUpdate = $this->_DAL->sqlQuery($sql, $bindParams);
               
            }
                
                
            //$response = $this->_playlist->saveUserPlayList($sessionId, $djId, $userId,2);
//            $sql = "UPDATE tb_playlist_share set status=? Where dj_user_id=? && listner_user_id=? LIMIT 1";
//            $bindParams = array(2, $userId, $listenerId);
//            $responseUser = $this->_DAL->sqlQuery($sql, $bindParams);
            
           
            
           
            
             if($flag == 0)
            {
                 $sessionId = $this->_playlist->setSessionIdFromUserId($djId);
                 $playlistShareDatetime = date("Y-m-d  H:i:s");
                $sql = "INSERT into tb_playlist_share(session_id,dj_user_id,listner_user_id,status,share_datetime) values(?,?,?,?,?)";
                $bindParams = array($sessionId, $djId, $userId,2, $playlistShareDatetime);
                $response = $this->_DAL->sqlQuery($sql, $bindParams);
            }
            else if($flag == 1)
            {
               $sql = "UPDATE tb_playlist_share set status=? Where dj_user_id=? && listner_user_id=? LIMIT 1";
                $bindParams = array(2, $djId, $userId);
                $responseUserUpdate = $this->_DAL->sqlQuery($sql, $bindParams); 
            }
            
                
                
                 $response = array("log" => "Dj joined successfully!");
            }
            else
            {
                $response = array("error" => "Can't join!");
                return $response; 
            
            }
            
            
            
            return $response;
        }
}


public function checkingDjCanSharePlaylistOrNot($djId,$listenerId)
{
    
    $sql = "SELECT is_premium FROM tb_users WHERE user_id=? LIMIT 1";
            $bindParams = array($djId);
            $isPremium = $this->_DAL->sqlQuery($sql, $bindParams);
            
            $canshare=0;
           
                    
         $sql = "SELECT listner_user_id FROM tb_playlist_share WHERE dj_user_id=? ";
            $bindParams = array($djId);
            $responseDjListeners = $this->_DAL->sqlQuery($sql, $bindParams); 
            
            foreach($responseDjListeners['data'] as $val)
            {
                
                if($val['listner_user_id']==$listenerId)
                {
                    
                    $canshare=1;
                   return $canshare;
                }
            }
            
            $countListeners=count($responseDjListeners['data']);
            if($isPremium['data'][0]['is_premium']==0)
            {
                if($countListeners<1)
                $canshare=1;
            }
            else
            {
               if($countListeners<10)
                $canshare=1; 
            }
            return $canshare;
            
}
    /*
     * ------------------------------------------------------
     *  Dj accepting request of an user
     * ------------------------------------------------------
     */
public function acceptRequestFromDjId($data = array()) {
        $access_token = $data['access_token'];
        $listenerId = $data['listener_id'];


        $retResult = $this->_funcObj->checkBlank($data);
        if ($retResult == 1) {
            $response = array("error" => "Some Parameter Missing!");
            return $response;
        }

        $authenticate = $this->_funcObj->authenticateAccessToken($access_token);


        if (count($authenticate) == 0) {
            $response = array("error" => "Invalid Access Token!");
            return $response;
        } else {
            $userId = $authenticate['data'][0]['user_id'];

            $sql = "SELECT dj_user_id FROM tb_playlist_share WHERE listner_user_id=? && status=? LIMIT 1";
            $bindParams = array($listenerId, 2);
            $responsePreviousDj = $this->_DAL->sqlQuery($sql, $bindParams);
            if (count($responsePreviousDj) > 0) {
                $sql = "UPDATE tb_playlist_share set status=? Where dj_user_id=? && listner_user_id=? LIMIT 1";
                $bindParams = array(0, $responsePreviousDj['data'][0]['dj_user_id'], $listenerId);
                $responseUserUpdate = $this->_DAL->sqlQuery($sql, $bindParams);
            }

            $sql = "UPDATE tb_playlist_share set status=? Where dj_user_id=? && listner_user_id=? LIMIT 1";
            $bindParams = array(2, $userId, $listenerId);
            $responseUser = $this->_DAL->sqlQuery($sql, $bindParams);

            $response = array("log" => "Request Accepted Successfully!");
            return $response;
        }
    }
     /*
     * ------------------------------------------------------
     *  Dj accepting request of an user
     * ------------------------------------------------------
     */
public function editProfileFromAccessToken($data = array()) {
        $access_token = $data['access_token'];
       
        $retResult = $this->_funcObj->checkBlank($data);
        if ($retResult == 1) {
            $response = array("error" => "Some Parameter Missing!");
            return $response;
        }

        $authenticate = $this->_funcObj->authenticateAccessToken($access_token);


        if (count($authenticate) == 0) {
            $response = array("error" => "Invalid Access Token!");
            return $response;
        } else {
            $userId = $authenticate['data'][0]['user_id'];
            $userImageLink = $this->_funcObj->saveImageFromFile($_FILES['pic'], 'user_profile');
           
            $sql = "UPDATE tb_users set user_image=? Where user_id=? LIMIT 1";
            $bindParams = array($userImageLink, $userId);
            $responseUser = $this->_DAL->sqlQuery($sql, $bindParams);

            $response = array("log" => "User Profile Updated Successfully!");
            return $response;
        }
    }
    function getOnlineDjFromDjId($djId)
    {
        $sql = "SELECT id,ntp_date,bit_rate,song_file_length FROM tb_pubnub_data WHERE dj_id =? LIMIT 1"; 
                  
                        $bindParams = array($djId);
                        $responseOnlineDj = $this->_DAL->sqlQuery($sql, $bindParams);
                        
                        if(count($responseOnlineDj)==0)
                        {
                            return (0);
                        }
                        else
                        {
                            
                           $curDate=date("Y-m-d H:i:s");
                            $songLength=(($responseOnlineDj['data'][0]['song_file_length']*8)/($responseOnlineDj['data'][0]['bit_rate'])); //in seconds
                           // echo ($songLength)."\n";
                            $curSongTime=explode("  ",$responseOnlineDj['data'][0]['ntp_date']);
                            
                            $pop= array_pop($curSongTime);
                            
//                            $date = $curSongTime[0];
//                            echo $date."\n";
//                            $currentDate = strtotime($date);
//                            echo $currentDate."\n";
//                            $futureDate = $currentDate+$songLength;
                         $futureDate= date('Y-m-d H:i:s',strtotime($curSongTime[0]) + $songLength);
                            //echo $futureDate."\n";
                            //$formatDate = date("Y-m-d H:i:s", $futureDate);
                            
                           // echo $curDate."\n";
                           // echo $formatDate;

                            $timeFirst  = strtotime($curDate);
                            $timeSecond = strtotime($futureDate);
                            $differenceInSeconds = $timeFirst - $timeSecond;
                            
                            if($differenceInSeconds>0)
                            {
                                return (0);
                            }
                           else
                           {
                               return (1);
                           }

                        }
                        
                   
    }
    
    
     /*
     * ------------------------------------------------------
     *  updating user account to premium
     * ------------------------------------------------------
     */
public function updateUserToPremiumFromAccessToken($data = array()) {
        $access_token = $data['access_token'];
       
        $retResult = $this->_funcObj->checkBlank($data);
        if ($retResult == 1) {
            $response = array("error" => "Some Parameter Missing!");
            return $response;
        }

        $authenticate = $this->_funcObj->authenticateAccessToken($access_token);


        if (count($authenticate) == 0) {
            $response = array("error" => "Invalid Access Token!");
            return $response;
        } else {
            $userId = $authenticate['data'][0]['user_id'];
           
            $sql = "UPDATE tb_users set is_premium=? Where user_id=? LIMIT 1";
            $bindParams = array(1, $userId);
            $responseUser = $this->_DAL->sqlQuery($sql, $bindParams);

            $response = array("log" => "User Profile Updated Successfully!");
            return $response;
        }
    }
    
    public function updateVersionFromUserId($userId,$version) {
        
        $sql = "SELECT app_version FROM tb_users Where user_id=? LIMIT 1";
        $bindParams = array($userId);
        $responseUser = $this->_DAL->sqlQuery($sql, $bindParams);
        
        if($responseUser['data'][0]['app_version']!=$version)
        {
            $sql = "UPDATE tb_users SET app_version=? WHERE user_id=? limit 1";
            $bindParams = array($version, $userId);
            $result = $this->_DAL->sqlQuery($sql, $bindParams);

        }
       
              
                return $result;
            
        
    }
    
    public function updatePremiumFromEmail($data = array()) {
        $email = $data['email'];
        $flag = $data['flag'];
       
        $retResult = $this->_funcObj->checkBlank($data);
        if ($retResult == 1) {
            $response = array("error" => "Some Parameter Missing!");
            return $response;
        }
            
            $sql = "SELECT user_id FROM tb_users Where user_email=? LIMIT 1";
            $bindParams = array($email);
            $responseUser = $this->_DAL->sqlQuery($sql, $bindParams);
            
            if(count($responseUser)>0)
            {
            $sql = "UPDATE tb_users SET is_premium=? WHERE user_email=? limit 1";
            $bindParams = array($flag, $email);
            $result = $this->_DAL->sqlQuery($sql, $bindParams);
            }
            
            else
            {
                if($flag==1)
                {
             $sql = "INSERT into tb_premium_users(email) values(?)";
             $bindParams = array($email);
             $response = $this->_DAL->sqlQuery($sql, $bindParams);  
                }
            }
              
             $response = array("log" => "Premium status changed");
            return $response;
            
        
    }
    
    public function showPremiumUsers() {
        
      
            $sql = "SELECT user_email FROM tb_users Where is_premium=?";
            $bindParams = array(1);
            $responseUser = $this->_DAL->sqlQuery($sql, $bindParams);

            
            return $responseUser;
            
        
    }
    
}

?>


