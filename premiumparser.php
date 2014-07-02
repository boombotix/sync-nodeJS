<?php
header ('Content-type: text/html; charset=utf-8');

   $dbh = new PDO('mysql:host=localhost;dbname=boombotix_dev', root, Cz3bjPQQRb7dtCjD);
         
   
 
    $sql=$dbh->prepare("insert into tb_premium_users(email) values(?)");
    
    
    if (($handle = fopen("8.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $num = count($data);
       
		
        if($row!=0)
	{
			
		         $email = $data[2];
                         
                       // echo $bio."   ".$id."<br>";
                        
                        
	
			$sql->execute(array($email));
			
						
				 }
            
        
            
       $row++;
       
    }
    fclose($handle);
}
         
  
?>