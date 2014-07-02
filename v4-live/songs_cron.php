<?php
$dbh = new PDO('mysql:host=localhost;dbname=boombotix', 'root', 'Cz3bjPQQRb7dtCjD');
$id = $_POST['id'];
while(1)
{
$fileContents = file_get_contents('upload/botfile_'.$id.'.txt');
if (!file_exists('upload/botfile_'.$id.'.txt')) {
    break;
}

//echo $fileContents;
$cur_date = date("Y-m-d H:i:s");
$fileData = explode(',', $fileContents);
$startDate = $fileData[0];
$timeElapsed = strtotime($cur_date) - strtotime($startDate);

$sql = $dbh->prepare('SELECT song_link,song_duration,flag FROM tb_bot_playlist where session_id=?');
$sql->execute(array($fileData[1]));
$all = $sql->fetchAll(PDO::FETCH_ASSOC);
$songsCount = $sql->rowCount();
$totalPlayistTime = 0;
for($i=0;$i<$songsCount;$i++)
{
    $totalPlayistTime = $totalPlayistTime + $all[$i]['song_duration'];
}

$roundNumber = $timeElapsed / $totalPlayistTime;

$remainderSec = $timeElapsed;
//echo '\n'.$remainderSec.'\n';
if($roundNumber >1)
{
    $whole = floor($roundNumber);     
    $remainderSec = ($roundNumber - $whole) * $totalPlayistTime;    
}
$timeTillSongEnd = 0;
for($j=0; $j<$songsCount; $j++)
{
    $timeTillSongEnd = $timeTillSongEnd + $all[$j]['song_duration'];
    if($timeTillSongEnd > $remainderSec)
    {
        break;
    }
}
//echo $remainderSec.'\n';
//echo 'hi';
$songToBePlayed = $all[$j]['song_link'];

if($all[$j]['flag'] != 1)
{
    $sql3 = "Update tb_bot_playlist set flag=? where session_id = ?";
    $q1 = $dbh->prepare($sql3);
    $q1->execute(array(0,$fileData[1]));
    
    $sql2 = "Update tb_bot_playlist set flag=? where song_link=? && session_id = ? LIMIT 1";
    $q1 = $dbh->prepare($sql2);
    $q1->execute(array(1,$songToBePlayed,$fileData[1]));
    
    $sql = $dbh->prepare('SELECT selected_song, bit_rate, npackets, num_bytes, data_offset, audio_bytes, song_file_length FROM tb_bot_pubnub where song_link=? LIMIT 1');
    $sql->execute(array($songToBePlayed));
    $songDetails = $sql->fetchAll(PDO::FETCH_ASSOC);
    
    
    
    $ntpDate = $cur_date.'  0000';
    $songUrl = 'https://api.soundcloud.com/tracks/'.$songToBePlayed.'/stream?client_id=b45b1aa10f1ac2941910a7f0d10f8e28';
    $djId = $fileData[2];
    
    
    
    $sql4 = "UPDATE tb_bot_counter set counter=counter+1 Where dj_id=? limit 1";
    $q4 = $dbh->prepare($sql4);
    $q4->execute(array($djId));
    
    $sql = $dbh->prepare('SELECT counter FROM tb_bot_counter where dj_id=? LIMIT 1');
    $sql->execute(array($djId));
    $djCounter = $sql->fetchAll(PDO::FETCH_ASSOC);
    
    //echo $djCounter[0]['counter'];
    //echo 'hi';
    sendPubnubPush($fileData[1],$cur_date,$songUrl,$songDetails[0]['selected_song'],$djCounter[0]['counter']);
    
    $sql = "UPDATE tb_pubnub_data set ntp_date=?,song_status=?,selected_index=?,selected_song=?,song_url=?,message=?,bit_rate=?, npackets=?, num_bytes=?, audio_bytes=?, data_offset=?, song_file_length=? Where dj_id=? limit 1";
    $q1 = $dbh->prepare($sql);
    $q1->execute(array($ntpDate,'play',0,$songDetails[0]['selected_song'],$songUrl,'ankush',$songDetails[0]['bit_rate'],$songDetails[0]['npackets'],$songDetails[0]['num_bytes'],$songDetails[0]['audio_bytes'],$songDetails[0]['data_offset'],$songDetails[0]['song_file_length'],$djId));
}

sleep(5);
flush();
ob_flush();

}


function sendPubnubPush($sessionId,$cur_date,$songUrl,$songName,$counter)
{
    require_once('admin/pubnub_files/Pubnub.php');
        
        $pubnub = new Pubnub("demo", "demo");
       
        $songUrl = getLocation($songUrl);
        
        $msg = $cur_date.' +0000$streamsong$'.$songName.'$'.$songUrl.'$'.$counter;
        //echo $sessionId;
        $info = $pubnub->publish(array(
            'channel' => 'channel_'.$sessionId, // REQUIRED Channel to Send
            'message' => $msg   // REQUIRED Message String/Array
        ));
        //print_r($info);
}

function getLocation($Url) {
   
        //$Url = $data['url'];

        //$Url = "https://api.soundcloud.com/tracks/101323352/stream?client_id=b45b1aa10f1ac2941910a7f0d10f8e28";

         $ch = curl_init();
 
    // Now set some options (most are optional)
 
    // Set URL to download
    curl_setopt($ch, CURLOPT_URL, $Url);
 
 
    // Include header in result? (0 = yes, 1 = no)
    curl_setopt($ch, CURLOPT_HEADER, true);
 
    // Should cURL return or print out the data? (true = return, false = print)
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 
  
 
    // Download the given URL, and return output
    $output = curl_exec($ch);
 
    // Close the cURL resource, and free system resources
    curl_close($ch);
 
   // echo $output."<br>";


preg_match_all('/^Location:(.*)$/mi', $output, $matches);
$location=$matches[0][0];


$str_arr=explode(":",$location);
$final_string=$str_arr[1].":".$str_arr[2];
$final_string=trim($final_string);
//$output_final=$final_string;

        return $final_string;
    }


?>