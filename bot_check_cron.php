<?php
$dbh = new PDO('mysql:host=localhost;dbname=boombotix_dev', 'root', 'Cz3bjPQQRb7dtCjD');
$sql = $dbh->prepare('SELECT dj_id,datetime from tb_bot_counter');
$sql->execute();
$allDjs = $sql->fetchAll(PDO::FETCH_ASSOC);

$djLength = $sql->rowCount();
$cur_date = date("Y-m-d H:i:s");
for($l=0; $l<$djLength; $l++)
{
    $timeElapsed = strtotime($cur_date) - strtotime($allDjs[$l]['datetime']);
    if($timeElapsed>5)
    {
      stopDj($allDjs[$l]['dj_id']);
      playDj($allDjs[$l]['dj_id']);
    }
}


 function stopBot($djId) {
        
       
        $fileContents = file_get_contents('../../upload/botfile_'.$djId.'.txt');
        $fileData = explode(',', $fileContents);
        
        $sql3 = "DELETE FROM `tb_bot_playlist` WHERE session_id = ?";
        $q1 = $dbh->prepare($sql3);
        $q1->execute(array($fileData[1]));
        
        $sql3 = "DELETE FROM `tb_bot_counter` WHERE dj_id = ? LIMIT 1";
        $q1 = $dbh->prepare($sql3);
        $q1->execute(array($djId));
       
        
        unlink('../../upload/botfile_'.$djId.'.txt');
        return;
    }
    
    
    
     function playBotPlaylist($djId) {
 
       
        
        
        
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
    
     function startCron()
    {
      
    $jsonUrl = "http://boombotix.clicklabs.in/dev/songs_cron.php";
    
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
    
     function sendPubnubPush($sessionId,$cur_date,$songLink,$songName)
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
    
     function parseJson($url)
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
?>