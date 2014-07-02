<?php

/**
 * @ Description : This class Functions includes all the functions to be used related to playlist addition/updation in  the app.

 * @ Copyright : Boombotix
 * @ Developed by : Click Labs Pvt. Ltd.
 */
header('Content-type: application/json');

class PlayList {

    private $_s3;
    private $_funcObj;
    private $_DAL;

    public function __construct() {

        if (!defined('awsAccessKey'))
            define('awsAccessKey', 'AKIAJTIGKXNQVTRBU45A');
        if (!defined('awsSecretKey'))
            define('awsSecretKey', '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');

        $this->_s3 = new S3(awsAccessKey, awsSecretKey);
        $this->_DAL = new DAL;
        $this->_funcObj = new Commonfunction;
    }

    function __autoload($class_name) {
        require $class_name . '.class.php';
    }

    /*
     * ------------------------------------------------------
     *  To set session for the user
     * ------------------------------------------------------
     */

    public function setSessionIdFromUserId($userId) {


        $sql = "SELECT session_id FROM tb_user_playlist where user_id=? limit 1";
        $bindParams = array($userId);

        $responseSessionId = $this->_DAL->sqlQuery($sql, $bindParams);


        if (count($responseSessionId) == 0) {



            $sessionId = $this->generateSessionId();




            $playlistCreatedDatetime = date("Y-m-d : H:i:s");

            $sql = "INSERT into tb_user_playlist(user_id,session_id,playlist_created_datetime) values(?,?,?)";
            $bindParams = array($userId, $sessionId, $playlistCreatedDatetime);
            $response = $this->_DAL->sqlQuery($sql, $bindParams);

            return $sessionId;
        } else {
            return $responseSessionId['data'][0]['session_id'];
        }
    }

    /*
     * ------------------------------------------------------
     *  To add songs in the DJ playlist of the user
     * ------------------------------------------------------
     */

    public function addPlayListSongFromUserIdSessionIdChannelId($data = array()) {

        $accessToken = $data['access_token'];
        $songName = $data['song_name'];
        $songArtist = $data['song_artist'];
        $songLink = $data['song_link'];
        $songItunesLink = $data['song_itunes_link'];
		$songImageUrl = $data['song_image_url'];
        $songStatus = $data['song_status'];

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

            $flag = 1;
            $sql = "SELECT user_id,session_id,song_name FROM tb_user_playlist WHERE user_id=?";
            $bindParams = array($userId);

            $responseSessionId = $this->_DAL->sqlQuery($sql, $bindParams);

            //print_r($responseSessionId);

            if (count($responseSessionId) > 0) {
                //if user exist in the table tb_user_playlist,find its session id



                if (count($responseSessionId) >= 1 && $responseSessionId['data'][0]['song_name'] == "") {
                    $flag = 0;
                }


                $sessionId = $responseSessionId['data'][0]['session_id'];
            } else {



                $sessionId = $this->generateSessionId();
            }
            //save the song info in tb_user_playlist with session id and user id


			if ($_FILES['file']['error'] != 4 && $_FILES['file']['name'] != '') {
            $songImagePath = $this->_funcObj->saveImageFromFile($_FILES['file'], 'song_image');
			}
            //            

            if ($_FILES['mp3_file']['error'] != 4 && $_FILES['mp3_file']['name'] != '') {
                $songLink = $this->_funcObj->saveImageFromFile($_FILES['mp3_file'], 'song');
            }
			if($songStatus==2)
			{
				$songImagePath=$songImageUrl;
				}

            $playlistCreatedDatetime = date("Y-m-d : H:i:s");
            if ($flag == 1) {
                $sql = "INSERT into tb_user_playlist(user_id,song_name,song_artist,song_image,song_link,song_itunes_link,session_id,playlist_created_datetime,song_status) values(?,?,?,?,?,?,?,?,?)";
                $bindParams = array($userId, $songName, $songArtist, $songImagePath, $songLink, $songItunesLink, $sessionId, $playlistCreatedDatetime, $songStatus);
                $response = $this->_DAL->sqlQuery($sql, $bindParams);
                $songId = $response;
            } else {
                $sql = "UPDATE tb_user_playlist SET song_name=?,song_artist=?,song_image=?,song_link=?,song_itunes_link=?,playlist_created_datetime=?,song_status=? where user_id=?";
                $bindParams = array($songName, $songArtist, $songImagePath, $songLink, $songItunesLink, $playlistCreatedDatetime, $songStatus, $userId);
                $response = $this->_DAL->sqlQuery($sql, $bindParams);

                $sql = "SELECT playlist_id FROM tb_user_playlist WHERE user_id=? LIMIT 1";
                $bindParams = array($userId);

                $responsePlaylistId = $this->_DAL->sqlQuery($sql, $bindParams);

                $songId = $responsePlaylistId['data'][0]['playlist_id'];
            }

            



            $this->updateUserPlaylistOrderFromUserId($userId, $songId);
			$response1 = array("log" => "Song added to playlist successfully", "session_id" => $sessionId,"song_id"=>$songId);


            return $response1;
        }
    }

    
    
    /*
     * ------------------------------------------------------
     *  To add songs in the DJ playlist of the user
     * ------------------------------------------------------
     */
    
    
    
    public function updateUserPlaylistOrderFromUserId($userId, $songId) {



        $sql = "SELECT playlist_order FROM tb_users WHERE user_id=? LIMIT 1";
        $bindParams = array($userId);

        $responsePlaylistOrder = $this->_DAL->sqlQuery($sql, $bindParams);

        $playlistOrder = $responsePlaylistOrder['data'][0]['playlist_order'];


        if ($playlistOrder != "") {

            $playlistOrder = $songId . "," . $playlistOrder;



            $sql = "UPDATE tb_users set playlist_order=? Where user_id=? limit 1";
            $bindParams = array($playlistOrder, $userId);
            $response = $this->_DAL->sqlQuery($sql, $bindParams);



            if (!$response) {
                $response = array("error" => "Unable To Update Playlist Order!");
                echo json_encode($response);
                exit(1);
            }
        }
    }

    
    /*
     * ------------------------------------------------------
     *  To add songs in the DJ playlist of the user
     * ------------------------------------------------------
     */
    
    
    
    public function generateSessionId() {


        $length = 4;

        $numbers = range('1', '9');
        //	$additional_characters = array('_','.');
        $final_array = $numbers;

        $randno = '';

        while ($length--) {
            $key = array_rand($final_array);
            $randno .= $final_array[$key];
        }

        $curdate = date("Y-m-d H:i:s");
        $sessionId = strtotime($curdate);



        $sessionId = $randno . $sessionId;
        return $sessionId;
    }

    /*
     * ------------------------------------------------------
     *  To get songs from the DJ playlist of the user by access token
     * ------------------------------------------------------
     */

    public function getDjPlayListFromUserAccessToken($data) {
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


            $userData = array("user_id" => $userId);

            $response['data'][0]['user_Dj_Playlist'] = $this->getDjPlayListFromUserId($userData);

            return $response;
        }
    }

    /*
     * ------------------------------------------------------
     *  To get songs from the DJ playlist of the user 
     * ------------------------------------------------------
     */

    public function getDjPlayListFromUserId($data) {



        $userId = $data['user_id'];

        $sql = "SELECT playlist_order FROM tb_users WHERE user_id=? LIMIT 1";
        $bindParams = array($userId);

        $responsePlaylistOrder = $this->_DAL->sqlQuery($sql, $bindParams);

        $playlistOrder = $responsePlaylistOrder['data'][0]['playlist_order'];

	$lastWord = substr($playlistOrder, -1);

	if($lastWord == ',')
	{
	
	$playlistOrder = substr($playlistOrder, 0, -1);
	}



        if ($playlistOrder == "") {
            $sql = "SELECT playlist_id,song_name,song_artist,song_image,song_link,song_itunes_link,session_id,song_status FROM tb_user_playlist WHERE user_id=? and song_name!=? ORDER BY playlist_created_datetime desc";
            $bindParams = array($userId, '');
        } else {
            // echo $playlistOrder;
            $sql = "SELECT playlist_id,song_name,song_artist,song_image,song_link,song_itunes_link,session_id,song_status FROM tb_user_playlist WHERE user_id=? and song_name!=? ORDER BY FIELD(playlist_id,$playlistOrder)";
            $bindParams = array($userId, '');
        }



        $responseDj = $this->_DAL->sqlQuery($sql, $bindParams);
        // print_r($responseDj);


        if (count($responseDj['data']) == 0) {
            $response = array();
            return $response;
        } else if (count($responseDj['data']) == 1 && $responseDj['data'][0]['song_name'] == "") {
            $response = array();

            return $response;
        }
        $i = 0;





        foreach ($responseDj['data'] as $row) {



            $responseDj['data'][$i]['song_id'] = $row['playlist_id'];




            if ($row['song_status'] == 0) {
                $responseDj['data'][$i]['song_link'] = $this->_DAL->ImageBasePath . $this->_DAL->mp3ImageFolder . $row['song_link'];
				$responseDj['data'][$i]['song_image'] = $this->_DAL->ImageBasePath . $this->_DAL->SongImageFolder . $row['song_image'];
            }
            
            unset($responseDj['data'][$i]['playlist_id']);

            if ($row['song_name'] == '') {
                unset($responseDj['data'][$i]);
            }
            $i++;
        }
        return $responseDj['data'];
    }

    /*
     * ------------------------------------------------------
     *  To delete song from user playlist current session
     * ------------------------------------------------------
     */

    public function deleteUserPlaylistSongFromUserAccessTokenAndSongId($data = array()) {
        $accessToken = $data['access_token'];
        $songId = $data['song_id'];


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

            //check the song and delete song image link and song mp3 from the s3 folder also

            $sql = "SELECT song_image,song_link,song_status from tb_user_playlist where user_id=? && playlist_id=? limit 1";
            $bindParams = array($userId, $songId);
            $responseDataSong = $this->_DAL->sqlQuery($sql, $bindParams);




            if ($responseDataSong['data'][0]['song_status'] == 0) {

                $song_link = $this->_DAL->mp3ImageFolder . $responseDataSong['data'][0]['song_link'];

                if (!($this->_s3->deleteObject("boom-botix", $song_link))) {
                    echo json_encode(array("error" => "Unable to delete song from playlist!"));
                    exit(1);
                }
				$song_image = $this->_DAL->SongImageFolder . $responseDataSong['data'][0]['song_image'];

            if ($responseDataSong['data'][0]['song_image'] != 'default.png') {

                if (!($this->_s3->deleteObject("boom-botix", $song_image))) {
                    echo json_encode(array("error" => "Unable to delete song image from playlist!"));
                    exit(1);
                }
            }
            }


            


            //check the song and delete song image link and song mp3 from the s3 folder also

            $sql = "SELECT session_id from tb_user_playlist where user_id=?";
            $bindParams = array($userId);
            $responseDataTotalSong = $this->_DAL->sqlQuery($sql, $bindParams);
            $userSessionId = 0;
            if (count($responseDataTotalSong['data']) == 1) {
                $userSessionId = $responseDataTotalSong['data'][0]['session_id'];
            }




            $sql = "DELETE FROM tb_user_playlist WHERE user_id=? && playlist_id=? limit 1";
            $bindParams = array($userId, $songId);

            $responseDel = $this->_DAL->sqlQuery($sql, $bindParams);

            if (!$responseDel) {
                $response = array("error" => "Unable to delete song from playlist!");
                return $response;
            } else {


                if ($userSessionId != 0) {
                    $playlistCreatedDatetime = date("Y-m-d : H:i:s");

                    $sql = "INSERT into tb_user_playlist(user_id,session_id,playlist_created_datetime) values(?,?,?)";
                    $bindParams = array($userId, $userSessionId, $playlistCreatedDatetime);
                    $response = $this->_DAL->sqlQuery($sql, $bindParams);
                }


                $response = array("log" => "Song deleted successfully!");
                return $response;
            }
        }
    }

    /*
     * ------------------------------------------------------
     *  To get previous session shared with login user from perticular dj
     * ------------------------------------------------------
     */

    public function getPreviousSessionDjPlayListFromUserIdAndSessionId($data = array()) {


        $accessToken = $data['access_token'];
        $userId = $data['user_id'];
        $sessionId = $data['session_id'];



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
            //$userId = $authenticate['data'][0]['user_id'];



            $sql = "SELECT song_name,song_artist,song_image,song_link,song_itunes_link,session_id,song_status FROM tb_user_playlist_archive WHERE user_id=? && session_id=?";
            $bindParams = array($userId, $sessionId);

            $responseDj = $this->_DAL->sqlQuery($sql, $bindParams);
            if (count($responseDj) == 0) {
                $response = array();
                return $response;
            }
            $i = 0;
            foreach ($responseDj['data'] as $row) {
                if ($row['song_status'] == 0) {
                    $responseDj['data'][$i]['song_link'] = $this->_DAL->ImageBasePath . $this->_DAL->mp3ImageFolder . $row['song_link'];
                }
                $responseDj['data'][$i]['song_image'] = $this->_DAL->ImageBasePath . $this->_DAL->SongImageFolder . $row['song_image'];
                $i++;
            }
        }
        return $responseDj;
    }
     /*
     * ----------------------------------------------------------------------
     *  Saving user playlist
     * ----------------------------------------------------------------------
     */
    public function saveUserPlayList($session_id, $userId, $userIdFriend,$status) {


        $sql = "SELECT dj_user_id,listner_user_id FROM  tb_playlist_share WHERE dj_user_id=? && listner_user_id=? LIMIT 1";
        $bindParams = array($userId, $userIdFriend);

        $responseSharedIds = $this->_DAL->sqlQuery($sql, $bindParams);
        if (count($responseSharedIds) != 0) {
            $response = array("log" => "Playlist already shared with this user");

            return $response;
        }

        $playlistShareDatetime = date("Y-m-d : H:i:s");



        $sql = "INSERT into tb_playlist_share(session_id,dj_user_id,listner_user_id,status,share_datetime) values(?,?,?,?,?)";
        $bindParams = array($session_id, $userId, $userIdFriend,$status, $playlistShareDatetime);
        $response = $this->_DAL->sqlQuery($sql, $bindParams);

        $response = array("log" => "Song playlist shared successfully", "session_id" => $session_id);

        return $response;
    }
    /*
     * ----------------------------------------------------------------------
     *  Getting the list of user whom with the dj has shared his playlist
     * ----------------------------------------------------------------------
     */
    public function getSharedFriendsIds($userId, $mode) {
        if ($mode == 'dj') {
            $sql = "SELECT listner_user_id,status FROM  tb_playlist_share WHERE dj_user_id=?";
        } else {
            $sql = "SELECT dj_user_id,status FROM  tb_playlist_share WHERE listner_user_id=?";
        }
        $bindParams = array($userId);

        $responseSharedIds = $this->_DAL->sqlQuery($sql, $bindParams);
        if (count($responseSharedIds) == 0) {
            $response = array("log" => "No user found");

            echo json_encode($response);
            exit(1);
        }
        return $responseSharedIds;
    }
    /*
     * ----------------------------------------------------------------------
     *  Checking the session of the user(if it exists or not)
     * ----------------------------------------------------------------------
     */
    public function checkSessionForUserId($userId) {
        $sql = "SELECT session_id FROM tb_user_playlist WHERE user_id=? LIMIT 1";
        $bindParams = array($userId);

        $responseSessionIds = $this->_DAL->sqlQuery($sql, $bindParams);
        if (count($responseSessionIds) == 0) {
            return 0;
        } else {
            return 1;
        }
    }
    
    /*
     * ----------------------------------------------------------------------------------------------
     *  Getting the list unique djs(who have shared their playlist and lat-long are not 0.00000)
     * ----------------------------------------------------------------------------------------------
     */
    public function getUniqueDjIdsFromUserId($userId)
    {
        
//        $sql = "SELECT distinct(dj_user_id) FROM tb_playlist_share";
//        $bindParams = array();
//
//        $response = $this->_DAL->sqlQuery($sql, $bindParams);
//        
//        //print_r($responseResult);
//		foreach($response['data'] as $val)
//        {
//          $userIds[]=$val['dj_user_id'];  
//        }
//        
//        
//        
//        $placeholders = rtrim(str_repeat('?, ', count($userIds)), ', ');
		
		$sql = "SELECT user_id FROM tb_users WHERE longitude!='0.000000' AND latitude!='0.000000'"; //and datediff(CURDATE(), DATE(last_login_date)) <= 30";
        $bindParams = array();
        $responseResult = $this->_DAL->sqlQuery($sql, $bindParams);
        $onlineDj=array();
		if(count($responseResult)==0)
		{
			return (0);
			}
               else
               {
                    $sql = "SELECT id,ntp_date,bit_rate,song_file_length FROM tb_pubnub_data WHERE dj_id =? LIMIT 1"; 
                   foreach($responseResult['data'] as $row)
                   {
                       
                      
                        $bindParams = array($row['user_id']);
                        $responseOnlineDj = $this->_DAL->sqlQuery($sql, $bindParams);
                        
                        if(count($responseOnlineDj)==0)
                        {
                            continue;
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
                                continue;
                            }
                           else
                           {
                               $onlineDj[]=$row['user_id'];
                           }

                        }
                        
                   }
                   
               }
               
        $sql = "SELECT status FROM tb_playlist_share where dj_user_id=? and listner_user_id=? limit 1";
        $i=0;
        foreach($onlineDj as $rowDj)
        {
            
            if($rowDj==$userId)
            {
                continue;
            }
            $bindParams = array($rowDj,$userId);

            $responseResultStatus = $this->_DAL->sqlQuery($sql, $bindParams);
           
             
           if(count($responseResultStatus)==0)
           {
               $status=0;
           }
           else
           {
               if($responseResultStatus['data'][0]['status']==0)
               {
               $status=1;
               }
              else {
                $status= $responseResultStatus['data'][0]['status'];  
              }
             
             
           }
            $responseNearDjIdsWithStatus[$i]=array("dj_user_id"=>$rowDj,"share_status"=>$status);
            $i++;
            
            
        }
        
        return $responseNearDjIdsWithStatus;
        
    }

    
     /*
     * -------------------------------------------
     *  Getting the song url from sound cloud
     * ------------------------------------------
     */

    public function getLocation($Url) {
        

        return $Url;
    }
    /*
     * -------------------------------------------
     *  Getting the song url from sound cloud
     * ------------------------------------------
     */
	public function getLocation1($data=array()) {
        $Url = $data['url'];

       

        return $Url;
    }
    /*
     * ---------------------------------------------
     *  Storing pubnub data into db and fetching it
     * ---------------------------------------------
     */
   public function getPubnubDataFromDjId($data = array()) {


        $accessToken = $data['access_token'];
        $djId = $data['dj_id'];
        $flag = $data['flag'];                  //0 means inserting, 1 means updating, 2 means fetching, 3 means delete
        $songStatus = $data['song_status'];
        $selectedIndex = $data['selected_index'];
        $selectedSong = $data['selected_song'];
        $ntpDate = $data['ntp_date'];
		$message = $data['message'];
		$bitRate = $data['bit_rate'];
        $npackets = $data['npackets'];
         $numBytes = $data['num_bytes'];
		 $songUrl = $data['song_url'];
		 $audioBytes = $data['audio_bytes'];
		 $dataOffset = $data['data_offset'];
                 $songFilelength = $data['song_file_length'];
                 $status = $data['status'];
                 $remainTime = $data['remain_time'];
		
        
        $retResult = $this->_funcObj->checkBlank($checkData = array($accessToken,$flag));
        if ($retResult == 1) {
            $response = array("error" => "Some Parameter Missing!");
            return $response;
        }

        $authenticate = $this->_funcObj->authenticateAccessToken($accessToken);


        if (count($authenticate) == 0) {
            $response = array("error" => "Invalid Access Token!");
            return $response;
        } else {
            //$userId = $authenticate['data'][0]['user_id'];


            if($flag==0)
            {
                
            $sql2 = "SELECT id from tb_pubnub_data  Where dj_id=? limit 1";
            $bindParams2 = array($djId);
            $response2 = $this->_DAL->sqlQuery($sql2, $bindParams2);
            
            if(count($response2)==1)
            {
             $sql = "UPDATE tb_pubnub_data set ntp_date=?,song_status=?,selected_index=?,selected_song=?,song_url=?,message=? Where id=? limit 1";
            $bindParams = array($ntpDate,$songStatus,$selectedIndex,$selectedSong,$songUrl,$message,$response2['data'][0]['id']);
            $response = $this->_DAL->sqlQuery($sql, $bindParams);
            }
            else
            {
                $sql = "INSERT INTO `tb_pubnub_data`( `ntp_date`, `song_status`, `selected_index`, `selected_song`, `song_url`,`message`, `dj_id`) VALUES(?,?,?,?,?,?,?)";
                $bindParams = array($ntpDate,$songStatus,$selectedIndex,$selectedSong,$songUrl,$message,$djId);
                $response = $this->_DAL->sqlQuery($sql, $bindParams);
            }
            //$songUrl= 'https://api.soundcloud.com/tracks/93321366/stream?client_id=b45b1aa10f1ac2941910a7f0d10f8e28';
            $songLink= explode('/', $songUrl);
            $songLink = $songLink[4];
            //echo $songLink;
            $sql2 = "SELECT id from tb_bot_pubnub  Where song_link=? limit 1";
            $bindParams2 = array($songLink);
            $responseSong = $this->_DAL->sqlQuery($sql2, $bindParams2);
            if(count($responseSong)==0)
            {
                $sql = "INSERT INTO `tb_bot_pubnub`(`selected_song`, `song_link`) VALUES(?,?)";
                $bindParams = array($selectedSong,$songLink);
                $response = $this->_DAL->sqlQuery($sql, $bindParams);
            }

            if (!$response) {
                $response = array("error" => "Unable To Update Pubnub Data!");

                return $response;
            } else {
                if($status==1)
                {
                    $reg_date = date("Y-m-d : H:i:s");
                    $sql = "UPDATE tb_users set broadcast_start_time=? Where user_id=? limit 1";
                    $bindParams = array($reg_date,$djId);
                    $response = $this->_DAL->sqlQuery($sql, $bindParams);
                }
                $response = array("log" => "Pubnub Data Updated!");
                return $response;
            }
            }
            else if($flag==1)
            {
            $sql2 = "SELECT song_url from tb_pubnub_data  Where dj_id=? limit 1";
            $bindParams2 = array($djId);
            $response2 = $this->_DAL->sqlQuery($sql2, $bindParams2);    
             
            $songLink= explode('/', $response2['data'][0]['song_url']);
            $songLink = $songLink[4];
            
	    $sql = "UPDATE tb_bot_pubnub set bit_rate=?,npackets=?,num_bytes=?,audio_bytes=?,data_offset=?,song_file_length=? Where song_link=? limit 1";
            $bindParams = array($bitRate,$npackets,$numBytes,$audioBytes,$dataOffset,$songFilelength,$songLink);
            $response = $this->_DAL->sqlQuery($sql, $bindParams);
		
            $sql = "UPDATE tb_pubnub_data set bit_rate=?,npackets=?,num_bytes=?,audio_bytes=?,data_offset=?,song_file_length=? Where dj_id=? limit 1";
            $bindParams = array($bitRate,$npackets,$numBytes,$audioBytes,$dataOffset,$songFilelength,$djId);
            $response = $this->_DAL->sqlQuery($sql, $bindParams);
            
            $response = array("log" => "Pubnub Data Updated!");
            return $response;
            
	   }
            else if($flag==2)
            {
                $sql2 = "SELECT ntp_date, song_status, selected_index, selected_song,message,bit_rate,npackets,num_bytes,song_url,audio_bytes,data_offset,song_file_length from tb_pubnub_data  Where dj_id=? limit 1";
            $bindParams2 = array($djId);
            $response2 = $this->_DAL->sqlQuery($sql2, $bindParams2);
			if(count($response2)==0)
			{
				$response = array("error" => "Dj Don't Exists!");
                return $response;
				}
            $response2['data'][0]['song_url']=$this->getLocation($response2['data'][0]['song_url']);
            return $response2;
            }
            else if($flag==3)
            {
                 if($status==1)
                {
                    $reg_date = date("Y-m-d : H:i:s");
                    $sql = "UPDATE tb_users set broadcast_secs=? Where user_id=? limit 1";
                    $bindParams = array($remainTime,$djId);
                    $response = $this->_DAL->sqlQuery($sql, $bindParams);
                }
            $sql2 = "DELETE FROM tb_pubnub_data WHERE dj_id=? limit 1";
            $bindParams2 = array($djId);
            $response2 = $this->_DAL->sqlQuery($sql2, $bindParams2);
            $response = array("log" => "Pubnub Data Deleted!");
                return $response;
            }
        }
       
    } 
    /*
     * ------------------------------------------------------
     *  Deleting listener from dj's listening list
     * ------------------------------------------------------
     */
    public function deleteListenerFromAccessTokenAndListenerId($data = array()) {


        $accessToken = $data['access_token'];
        $listenerId = $data['listener_id'];
        
        
        $retResult = $this->_funcObj->checkBlank($checkData = array($accessToken,$listenerId));
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
            
            $sql = "SELECT * from tb_playlist_share where dj_user_id=? AND listner_user_id=? LIMIT 1";
            $bindParams = array($userId,$listenerId);
            $row = $this->_DAL->sqlQuery($sql, $bindParams);
            
            
                $curDate = date("Y-m-d H:i:s");
                $sql = "INSERT into tb_playlist_share_archive values(?,?,?,?,?)";
                $bindParams = array($row['data'][0]['session_id'], $row['data'][0]['dj_user_id'],
                                    $row['data'][0]['listner_user_id'], $row['data'][0]['status'], $curDate);
                $response = $this->_DAL->sqlQuery($sql, $bindParams);
            
                
                
                $sql = "DELETE from tb_playlist_share where dj_user_id=? AND listner_user_id=? LIMIT 1";
                $bindParams = array($userId,$listenerId);
                $response = $this->_DAL->sqlQuery($sql, $bindParams);
                
                
            
                $response = array("log" => "Listener Deleted Successfully!");
                return $response;
            
            
        }
    }
    
    /*
     * ------------------------------------------------------
     *  Deleting listener from dj's listening list
     * ------------------------------------------------------
     */
    public function disconnectDjFromAccessTokenAndDjId($data = array()) {


        $accessToken = $data['access_token'];
        $djId = $data['dj_id'];
        
        
        $retResult = $this->_funcObj->checkBlank($checkData = array($accessToken,$djId));
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
            
            $sql = "UPDATE tb_playlist_share set status=? where dj_user_id=? AND listner_user_id=? LIMIT 1";
            $bindParams = array(0,$djId,$userId);
            $row = $this->_DAL->sqlQuery($sql, $bindParams);
            
            
                $response = array("log" => "Dj Disconnected Successfully!");
                return $response;
            
            
        }
    }
    
    public function playBotPlaylist($data = array()) {
 
        $djId = $data['bot_id'];
        
        $retResult = $this->_funcObj->checkBlank($checkData = array($djId));
        if ($retResult == 1) {
            $response = array("error" => "Some Parameter Missing!");
            return $response;
        }

        
            $sql = "SELECT song_link,session_id from tb_user_playlist where user_id=? AND song_name!=''";
            $bindParams = array($djId);
            $row = $this->_DAL->sqlQuery($sql, $bindParams);
            
            $errorSong =array();
            $songLinks =array();
            $songLengths =array();
            $i=0;
            $sql1 = "SELECT bit_rate,song_file_length from tb_bot_pubnub where song_link=? LIMIT 1";
            foreach ($row['data'] as $value) {
              $bindParams = array($value['song_link']);
              $responseSong = $this->_DAL->sqlQuery($sql1, $bindParams);  
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
                    
                $url = 'https://api.soundcloud.com/tracks/'.$errorSong[$j].'.json?client_id=b45b1aa10f1ac2941910a7f0d10f8e28';
                $songInfo = $this->parseJson($url);   
            
                $errorSongUrls[] = $songInfo;
                
                }
                $errorSongUrls = implode(',',$errorSongUrls);
                $response = array("error" => "These songs need to be played: ".$errorSongUrls);
                return $response;
            }
            else
            {
                // create text file
                $my_file = 'upload/botfile_'.$djId.'.txt';
                $handle = fopen($my_file, 'w') ;
                
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
                $response = $this->_DAL->sqlQuery($sql, $bindParams);
                }
                
                $sql = "INSERT into tb_bot_counter(dj_id,counter) values(?,?)";             
                $bindParams = array($djId, 0);
                $response = $this->_DAL->sqlQuery($sql, $bindParams);
                
                $sql1 = "SELECT  selected_song, bit_rate, npackets, num_bytes, data_offset, audio_bytes, song_file_length from tb_bot_pubnub where song_link=? LIMIT 1";           
                $bindParams = array($songLinks[0]);
                $songDetails = $this->_DAL->sqlQuery($sql1, $bindParams);
                
            $sql2 = "SELECT id from tb_pubnub_data  Where dj_id=? limit 1";
            $bindParams2 = array($djId);
            $response2 = $this->_DAL->sqlQuery($sql2, $bindParams2);
            
            $ntpDate = $cur_date.'  0000';
            
            $songUrl = 'https://api.soundcloud.com/tracks/'.$songLinks[0].'/stream?client_id=b45b1aa10f1ac2941910a7f0d10f8e28';
            if(count($response2)==1)
            {
            $sql = "UPDATE tb_pubnub_data set ntp_date=?,song_status=?,selected_index=?,selected_song=?,song_url=?,message=?,bit_rate=?, npackets=?, num_bytes=?, audio_bytes=?, data_offset=?, song_file_length=? Where id=? limit 1";
            $bindParams = array($ntpDate,'play',0,$songDetails['data'][0]['selected_song'],$songUrl,'ankush',$songDetails['data'][0]['bit_rate'],$songDetails['data'][0]['npackets'],$songDetails['data'][0]['num_bytes'],$songDetails['data'][0]['audio_bytes'],$songDetails['data'][0]['data_offset'],$songDetails['data'][0]['song_file_length'],$response2['data'][0]['id']);
            $response = $this->_DAL->sqlQuery($sql, $bindParams);
            }
            else
            {
                
                
                $sql = "INSERT INTO `tb_pubnub_data`( `ntp_date`, `song_status`, `selected_index`, `selected_song`, `song_url`,`message`, `dj_id`,`bit_rate`, `npackets`, `num_bytes`, `audio_bytes`, `data_offset`, `song_file_length`) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)";
                $bindParams = array($ntpDate,'play',0,$songDetails['data'][0]['selected_song'],$songUrl,'ankush',$djId,$songDetails['data'][0]['bit_rate'],$songDetails['data'][0]['npackets'],$songDetails['data'][0]['num_bytes'],$songDetails['data'][0]['audio_bytes'],$songDetails['data'][0]['data_offset'],$songDetails['data'][0]['song_file_length']);
                $response = $this->_DAL->sqlQuery($sql, $bindParams);
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
      
    $jsonUrl = "http://boombotix.clicklabs.in/songs_cron.php";
    
    $sql2 = "SELECT dj_id from tb_bot_counter ORDER BY id DESC LIMIT 1";
    $bindParams2 = array();
    $response2 = $this->_DAL->sqlQuery($sql2, $bindParams2);
                               
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
        
        include('admin/pubnub_files/Pubnub.php');
        
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
    public function botCheckCron()
    {
        $sql2 = "SELECT dj_id,datetime from tb_bot_counter";
    $bindParams2 = array();
    $response2 = $this->_DAL->sqlQuery($sql2, $bindParams2);
    $djLength = count($response2['data']);
        
    $cur_date = date("Y-m-d H:i:s");
for($l=0; $l<$djLength; $l++)
{
    $timeElapsed = strtotime($cur_date) - strtotime($response2['data'][$l]['datetime']);
    if($timeElapsed>5)
    {
       
      $this->stopDj($response2['data'][$l]['dj_id']);
     
      $this->againPlayBot($response2['data'][$l]['dj_id']);
    }
}
$response = array("log" => "Done");
                return $response;
    }
    
    
    public function stopDj($djId) {
        
        $fileContents = file_get_contents('../upload/botfile_'.$djId.'.txt');
        $fileData = explode(',', $fileContents);
        
        $sql = "DELETE FROM `tb_bot_playlist` WHERE `session_id` = ?";
        $bindParams = array($fileData[1]);
        $response = $this->_DAL->sqlQuery($sql, $bindParams);
        
        $sql = "DELETE FROM `tb_bot_counter` WHERE dj_id = ? LIMIT 1";
        $bindParams = array($djId);
        $response = $this->_DAL->sqlQuery($sql, $bindParams);
        
        unlink('../upload/botfile_'.$djId.'.txt');
        return;
    }
    public function againPlayBot($djId)
    {
        
        
            $sql = "SELECT song_link,session_id from tb_user_playlist where user_id=? AND song_name!=''";
            $bindParams = array($djId);
            $row = $this->_DAL->sqlQuery($sql, $bindParams);
            
            $errorSong =array();
            $songLinks =array();
            $songLengths =array();
            $i=0;
            $sql1 = "SELECT bit_rate,song_file_length from tb_bot_pubnub where song_link=? LIMIT 1";
            foreach ($row['data'] as $value) {
              $bindParams = array($value['song_link']);
              $responseSong = $this->_DAL->sqlQuery($sql1, $bindParams);  
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
                    
                $url = 'https://api.soundcloud.com/tracks/'.$errorSong[$j].'.json?client_id=b45b1aa10f1ac2941910a7f0d10f8e28';
                $songInfo = $this->parseJson($url);   
            
                $errorSongUrls[] = $songInfo;
                
                }
                $errorSongUrls = implode(',',$errorSongUrls);
                //$response = array("error" => "These songs need to be played: ".$errorSongUrls);
                return ;
            }
            else
            {
                // create text file
                $my_file = '../upload/botfile_'.$djId.'.txt';
                $handle = fopen($my_file, 'w') ;
                
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
                $response = $this->_DAL->sqlQuery($sql, $bindParams);
                }
                
                $sql = "INSERT into tb_bot_counter(dj_id,counter) values(?,?)";             
                $bindParams = array($djId, 0);
                $response = $this->_DAL->sqlQuery($sql, $bindParams);
                
                $sql1 = "SELECT  selected_song, bit_rate, npackets, num_bytes, data_offset, audio_bytes, song_file_length from tb_bot_pubnub where song_link=? LIMIT 1";           
                $bindParams = array($songLinks[0]);
                $songDetails = $this->_DAL->sqlQuery($sql1, $bindParams);
                
            $sql2 = "SELECT id from tb_pubnub_data  Where dj_id=? limit 1";
            $bindParams2 = array($djId);
            $response2 = $this->_DAL->sqlQuery($sql2, $bindParams2);
            
            $ntpDate = $cur_date.'  0000';
            
            $songUrl = 'https://api.soundcloud.com/tracks/'.$songLinks[0].'/stream?client_id=b45b1aa10f1ac2941910a7f0d10f8e28';
            if(count($response2)==1)
            {
            $sql = "UPDATE tb_pubnub_data set ntp_date=?,song_status=?,selected_index=?,selected_song=?,song_url=?,message=?,bit_rate=?, npackets=?, num_bytes=?, audio_bytes=?, data_offset=?, song_file_length=? Where id=? limit 1";
            $bindParams = array($ntpDate,'play',0,$songDetails['data'][0]['selected_song'],$songUrl,'ankush',$songDetails['data'][0]['bit_rate'],$songDetails['data'][0]['npackets'],$songDetails['data'][0]['num_bytes'],$songDetails['data'][0]['audio_bytes'],$songDetails['data'][0]['data_offset'],$songDetails['data'][0]['song_file_length'],$response2['data'][0]['id']);
            $response = $this->_DAL->sqlQuery($sql, $bindParams);
            }
            else
            {
                
                
                $sql = "INSERT INTO `tb_pubnub_data`( `ntp_date`, `song_status`, `selected_index`, `selected_song`, `song_url`,`message`, `dj_id`,`bit_rate`, `npackets`, `num_bytes`, `audio_bytes`, `data_offset`, `song_file_length`) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)";
                $bindParams = array($ntpDate,'play',0,$songDetails['data'][0]['selected_song'],$songUrl,'ankush',$djId,$songDetails['data'][0]['bit_rate'],$songDetails['data'][0]['npackets'],$songDetails['data'][0]['num_bytes'],$songDetails['data'][0]['audio_bytes'],$songDetails['data'][0]['data_offset'],$songDetails['data'][0]['song_file_length']);
                $response = $this->_DAL->sqlQuery($sql, $bindParams);
            }
                //$songLinks = implode(',', $songLinks);
                
                $data = $cur_date.','.$sessionId.','.$djId;
                fwrite($handle, $data);
                $this->sendPubnubPush($sessionId,$cur_date,$songLinks[0],$songDetails['data'][0]['selected_song']);
                $this->startCron();
                return;
                //$response = array("log" => "File created");
                //return $response;
            }
    }
}



