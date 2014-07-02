<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
//merging two files
//print_r($_FILES);
//die();

$op=$_POST['operation'];
$fileName=$_POST['filename'];


if ($_FILES["file"]["error"] > 0)
    {
    echo "Return Code: " . $_FILES["file"]["error"] . "<br>";
    }
  else
    {
		
		
     
    	 if($op=='new'){
    	 			move_uploaded_file($_FILES["file"]["tmp_name"],"upload/" . "tempfile.mp3");
      				$first_file = file_get_contents("upload/" . "tempfile.mp3",NULL,NULL,4);
       				file_put_contents('upload/'.$fileName,$first_file);
      				
    	 }
    	 
    	 else if($op=='append'){
    	 	
    	 	$first_file = file_get_contents('upload/'.$fileName);
    	 	 move_uploaded_file($_FILES["file"]["tmp_name"],"upload/" . "tempfile.mp3");
			$second_file = file_get_contents('upload/tempfile.mp3',NULL,NULL,4);
			file_put_contents('upload/new_file.mp3',$first_file . $second_file);
			rename ('upload/new_file.mp3','upload/'.$fileName);



    	 }
      
      
      
    
  }

/*
$file_path='upload/' . $_FILES["file"]["name"];


 if($op=='new')
{
  $file_path='upload/' . $_FILES["file"]["name"];     
}
else if($op=='append')
{
 
	
$first_file = file_get_contents('upload/'.$oldfileName);
 
$second_file = file_get_contents($file_path);



file_put_contents('upload/new_file.mp3',$first_file . $second_file);	
	
unlink('upload/'.$oldfileName);
rename ('upload/new_file.mp3','upload/'.$oldfileName);
	
 unlink($file_path);  	
$file_path='upload/'.$oldfileName;   

    



}

echo json_encode(array("updated file"=>$file_path));




*/



?>
