<?php
$Url=$_GET['url'];


$output_final=array("Location"=>$Url);

 echo str_replace('\/', '/', json_encode($output_final));


?>
