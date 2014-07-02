<?php
$songUrl= 'https://api.soundcloud.com/tracks/93321366/stream?client_id=b45b1aa10f1ac2941910a7f0d10f8e28';
            $songLink= explode('/', $songUrl);
            $songLink = $songLink[4];
            echo $songLink;
?>