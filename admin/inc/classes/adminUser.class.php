<?php

/**
 * @Description : This class includes all the functions related to products in Admin Panel.
 *  All function will be written in this file.
 * @Copyright : Founder Cave Portal
 * @Developed by : Click Labs Pvt. Ltd.
 */
header('Content-type: application/json');

class adminUser {

    private $_dalObj;
    private $_s3;

    public function __construct() {

        if (!defined('awsAccessKey'))
            define('awsAccessKey', 'AKIAJTIGKXNQVTRBU45A');
        if (!defined('awsSecretKey'))
            define('awsSecretKey', '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');

        $this->_s3 = new S3(awsAccessKey, awsSecretKey);

        $this->_dalObj = new DAL;
        $this->_funcObj = new Functions;
    }

    function __autoload($class_name) {
        require $class_name . '.class.php';
    }

    /*
     * ------------------------------------------------------------------
     * To get all the Apps from the database.
     * ------------------------------------------------------------------
     */

    public function getAllUsers($userName) {
        $extraData = array(
        );

        $sql = "SELECT `user_id`,`user_name`,`user_email`,`user_image`,`user_fb_id` FROM `tb_users`";
        $bindParams = array();
        $response = $this->_dalObj->sqlQuery($sql, $bindParams);
        
        $i = 0;
        $sql2 = "SELECT dj_id from tb_bot_counter where dj_id=? LIMIT 1";
        foreach ($response['data'] as $value) {
         $bindParams2 = array($value['user_id']);
         $response1 = $this->_dalObj->sqlQuery($sql2, $bindParams2);  
         if (count($response1) > 0) {
           
            $response['data'][$i]['is_playing'] = 1;
        }
        else
        {
         $response['data'][$i]['is_playing'] = 0;   
        }
        
        if($value['user_fb_id'] == 0)
        {
          $response['data'][$i]['user_image'] =  'http://boom-botix.s3.amazonaws.com/user_profile/'.$value['user_image'];
        }
        $i++;
        }
        
       
        $log = 0;
        if (count($response) > 0) {
            $log = 1;
        }
        $response['log'] = $log;
        return $response;
    }

    /*
     * ------------------------------------------------------------------
     * To Create a App.
     * ------------------------------------------------------------------
     */

    public function createUser($data = array()) {
        $extraData = array(
        );
//          $response1['log'] = 1;
//        return $response1;
        
        

        $userName = $data['user_name'];
        $userEmail = $data['user_email'];
        $latitude = $data['latitude'];
        $longitude = $data['longitude'];

        $checkData = array($userName, $userEmail, $latitude, $longitude);
        $retResult = $this->_funcObj->checkBlank($checkData);


        if (isset($_FILES["icon_url"]["name"])) {

            $icon = $this->_funcObj->saveImageFromFile($_FILES["icon_url"], 'user_profile');
        } else {

            $icon = 'user.png';
           // die(json_encode(array('log' => 0)));
        }


        $accessToken = base64_encode($userEmail);
        $password = 123;
        $encryptpass = md5($password);
        $date = date('Y-m-d H:i:s');
        $downloads = 0;

        $sql = "INSERT INTO `tb_users`(`user_name`, `password`, `user_access_token`, `user_email`, `user_image`, `longitude`, `latitude`) VALUES (?,?,?,?,?,?,?)";
        $bindParams = array($userName, $encryptpass, $accessToken, $userEmail, $icon, $longitude, $latitude);
        $response = $this->_dalObj->sqlQuery($sql, $bindParams);


        $log = 0;
        if (count($response) > 0) {
            $log = 1;
        }
        $response1['log'] = $log;
        return $response1;
    }
    // Get running bots
    public function getBotsStatus($userName) {
        $extraData = array(
        );

        $sql2 = "SELECT dj_id from tb_bot_counter";
        $bindParams2 = array();
        $response = $this->_dalObj->sqlQuery($sql2, $bindParams2);
       
        
        if (count($response) > 0) {
             $sql = "SELECT `user_name`,`user_email` FROM `tb_users` WHERE `user_id`=? LIMIT 1";
            foreach ($response['data'] as $value) {
             $bindParams2 = array($value['dj_id']);
             $response1 = $this->_dalObj->sqlQuery($sql, $bindParams2);  
             
             $result[] = $response1['data'][0];
            }
            $response2 = $result;
        }
        else
        {
            $response2 = 0;
        }
        
        return $response2;
    }
    //play bot
public function playBotPlaylist($data = array()) {
 
        $djId = $data['bot_id'];
        
        $retResult = $this->_funcObj->checkBlank($checkData = array($djId));
        if ($retResult == 1) {
            $response = array("error" => "Some Parameter Missing!");
            return $response;
        }
        
            $sql = "SELECT dj_id from tb_bot_counter where dj_id=? LIMIT 1";
            $bindParams = array($djId);
            $row1 = $this->_dalObj->sqlQuery($sql, $bindParams);
            if(count($row1)==0)
            {
              $response = array("error" => "Bot already running!");
              return $response;  
            }
        
            $sql = "SELECT song_link,session_id from tb_user_playlist where user_id=?";
            $bindParams = array($djId);
            $row = $this->_dalObj->sqlQuery($sql, $bindParams);
            
            $errorSong =array();
            $songLinks =array();
            $songLengths =array();
            $i=0;
            $sql1 = "SELECT bit_rate,song_file_length from tb_bot_pubnub where song_link=? LIMIT 1";
            foreach ($row['data'] as $value) {
              $bindParams = array($value['song_link']);
              $responseSong = $this->_dalObj->sqlQuery($sql1, $bindParams);  
              if(count($responseSong)==0 || $responseSong['data'][0]['bit_rate'] == '')  
              {
                $errorSong[$i]=  $value['song_link'];
                $i++;
              }
              else
              {
                  $songLinks[]= $value['song_link'];
                  $songLength=(($responseSong['data'][0]['song_file_length']*8)/($responseSong['data'][0]['bit_rate'])); //in seconds
                  $songLengths[] = $songLength;
                 
              }
            }
            $errorSongUrls = array();
            if($i>0)
            {
                $countErrorSongs= count($errorSong);
                //$errorSong = implode(',',$errorSong);
                
               // https://api.soundcloud.com/tracks/93321366.json?client_id=b45b1aa10f1ac2941910a7f0d10f8e28
                
                for($j=0;$j<$countErrorSongs; $j++)
                {
                    echo $errorSong[$j];
                $url = 'https://api.soundcloud.com/tracks/'.$errorSong[$j].'.json?client_id=b45b1aa10f1ac2941910a7f0d10f8e28';
                $songInfo = $this->parseJson($url);   
                echo $songInfo;
                $errorSongUrls[] = $songInfo;
                
                }
                $errorSongUrls = implode(',',$errorSongUrls);
                $response = array("error" => "These songs need to be played: ".$errorSongUrls);
                return $response;
            }
            else
            {
                // create text file
                $my_file = '../../upload/botfile_'.$djId.'.txt';
                $handle = fopen($my_file, 'w') or die('Cannot open file:  '.$my_file);
                
                $cur_date = date("Y-m-d H:i:s");
                $sessionId = $row['data'][0]['session_id'];
                $songsCount = count($songLinks);
                
                $sql = "INSERT into tb_bot_playlist(session_id,song_link,song_duration,flag) values(?,?,?,?)";
                for($i=0; $i<$songsCount; $i++)
                {
                  $flag=0;
                if($i==0)
                {
                    $flag=1;
                }
                $bindParams = array($sessionId, $songLinks[$i], $songLengths[$i],$flag);
                $response = $this->_dalObj->sqlQuery($sql, $bindParams);
                }
                
                $sql = "INSERT into tb_bot_counter(dj_id,counter) values(?,?)";             
                $bindParams = array($djId, 0);
                $response = $this->_dalObj->sqlQuery($sql, $bindParams);
                
                $sql1 = "SELECT  selected_song, bit_rate, npackets, num_bytes, data_offset, audio_bytes, song_file_length from tb_bot_pubnub where song_link=? LIMIT 1";           
                $bindParams = array($songLinks[0]);
                $songDetails = $this->_dalObj->sqlQuery($sql1, $bindParams);
                
            $sql2 = "SELECT id from tb_pubnub_data  Where dj_id=? limit 1";
            $bindParams2 = array($djId);
            $response2 = $this->_dalObj->sqlQuery($sql2, $bindParams2);
            
            $ntpDate = $cur_date.'  0000';
            
            $songUrl = 'https://api.soundcloud.com/tracks/'.$songLinks[0].'/stream?client_id=b45b1aa10f1ac2941910a7f0d10f8e28';
            if(count($response2)==1)
            {
            $sql = "UPDATE tb_pubnub_data set ntp_date=?,song_status=?,selected_index=?,selected_song=?,song_url=?,message=?,bit_rate=?, npackets=?, num_bytes=?, audio_bytes=?, data_offset=?, song_file_length=? Where id=? limit 1";
            $bindParams = array($ntpDate,'play',0,$songDetails['data'][0]['selected_song'],$songUrl,'ankush',$songDetails['data'][0]['bit_rate'],$songDetails['data'][0]['npackets'],$songDetails['data'][0]['num_bytes'],$songDetails['data'][0]['audio_bytes'],$songDetails['data'][0]['data_offset'],$songDetails['data'][0]['song_file_length'],$response2['data'][0]['id']);
            $response = $this->_dalObj->sqlQuery($sql, $bindParams);
            }
            else
            {
                
                
                $sql = "INSERT INTO `tb_pubnub_data`( `ntp_date`, `song_status`, `selected_index`, `selected_song`, `song_url`,`message`, `dj_id`,`bit_rate`, `npackets`, `num_bytes`, `audio_bytes`, `data_offset`, `song_file_length`) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)";
                $bindParams = array($ntpDate,'play',0,$songDetails['data'][0]['selected_song'],$songUrl,'ankush',$djId,$songDetails['data'][0]['bit_rate'],$songDetails['data'][0]['npackets'],$songDetails['data'][0]['num_bytes'],$songDetails['data'][0]['audio_bytes'],$songDetails['data'][0]['data_offset'],$songDetails['data'][0]['song_file_length']);
                $response = $this->_dalObj->sqlQuery($sql, $bindParams);
            }
                //$songLinks = implode(',', $songLinks);
                
                $data = $cur_date.','.$sessionId.','.$djId;
                fwrite($handle, $data);
                $this->sendPubnubPush($sessionId,$cur_date,$songLinks[0],$songDetails['data'][0]['selected_song']);
                $this->startCron();
                $response = array("log" => "File created");
                return $response;
            }
          
    }
    
    public function startCron()
    {
      
    $jsonUrl = "http://boombotix.clicklabs.in/dev/songs_cron.php";
    
    $sql2 = "SELECT dj_id from tb_bot_counter ORDER BY id DESC LIMIT 1";
    $bindParams2 = array();
    $response2 = $this->_dalObj->sqlQuery($sql2, $bindParams2);
                            
            $postData = "id=".$response2['data'][0]['dj_id']; 
            // Initializing curl
            $ch = curl_init( $jsonUrl );

           // Configuring curl options
            $options = array(
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POSTFIELDS=> $postData,
                    CURLOPT_POST => 1
                    );

            // Setting curl options
            curl_setopt_array( $ch, $options );
           curl_setopt($ch,CURLOPT_TIMEOUT,5);


            // Getting results
            $results =  curl_exec($ch); // Getting jSON result string
                                
                               // print_r($results);

    }
    
    public function sendPubnubPush($sessionId,$cur_date,$songLink,$songName)
    {
        
        include('../pubnub_files/Pubnub.php');
        
        $pubnub = new Pubnub("demo", "demo");
       
        $songUrl = $this->getLocation('https://api.soundcloud.com/tracks/'.$songLink.'/stream?client_id=b45b1aa10f1ac2941910a7f0d10f8e28');
       
        $msg = $cur_date.' +0000$streamsong$'.$songName.'$'.$songUrl.'$0';
       // echo $msg;
        $info = $pubnub->publish(array(
            'channel' => 'channel_'.$sessionId, // REQUIRED Channel to Send
            'message' => $msg   // REQUIRED Message String/Array
        ));
       // print_r($info);
    }
    
    public function parseJson($url)
    {
     
       $ch = curl_init();
 
    // Now set some options (most are optional)
 
    // Set URL to download
    curl_setopt($ch, CURLOPT_URL, $url);
 
 
    // Include header in result? (0 = yes, 1 = no)
    //curl_setopt($ch, CURLOPT_HEADER, true);
 
    // Should cURL return or print out the data? (true = return, false = print)
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 
  
 
    // Download the given URL, and return output
    $output = curl_exec($ch);
    curl_close($ch);
    $obj = json_decode($output,true);
    
    return $obj['permalink_url'];
    //$result= json_decode($output,true);
    //print_r($result);
    
    // Close the cURL resource, and free system resources
    //curl_close($ch);
    }
    public function editUser($data = array()) {
        $userId = $data['user_id'];
        $userName = $data['user_name'];
        
        $sql = "UPDATE tb_users set user_name=? Where user_id=? limit 1";
        $bindParams = array($userName,$userId);
        $response = $this->_dalObj->sqlQuery($sql, $bindParams);
        $response['log'] = 1;
        return $response;
    }
    
     public function stopBot($data = array()) {
        $djId = $data['dj_id'];
       
        $fileContents = file_get_contents('../../upload/botfile_'.$djId.'.txt');
        $fileData = explode(',', $fileContents);
        
        $sql = "DELETE FROM `tb_bot_playlist` WHERE session_id = ?";
        $bindParams = array($fileData[1]);
        $response = $this->_dalObj->sqlQuery($sql, $bindParams);
        
        $sql = "DELETE FROM `tb_bot_counter` WHERE dj_id = ? LIMIT 1";
        $bindParams = array($djId);
        $response = $this->_dalObj->sqlQuery($sql, $bindParams);
        
        unlink('../../upload/botfile_'.$djId.'.txt');
        $response1 = array("log" => "Bot stopped");
        return $response1;
    }
}

/* End of file adminUser.class.php */
/* Location: ./inc/classes/adminUser.class.php */
?>