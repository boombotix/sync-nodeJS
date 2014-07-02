<?php
include('pubnub_files/Pubnub.php');
$pubnub = new Pubnub("demo", "demo");

//$songUrlFromSoundCloud = 'https://ec-media.soundcloud.com/4YqUW5ou7yVp.128.mp3?ff61182e3c2ecefa438cd02102d0e385713f0c1faf3b033959566bfd0c01ee17fd9c3d617d2c7adb4ac4277b30ac0a1bc4071b46ad47030441f92d3c31a3dd1e3a4df47531&AWSAccessKeyId=AKIAJ4IAZE5EOI7PA7VQ&Expires=1387345397&Signature=bGzhQqVcwoJeq2PqWXddDE7FSNI%3D';
//$songTitle = 'ANTS Podcast #012: Trapicana High Pulp';

//$pubnubMessageToSend =array($songTitle,$songUrlFromSoundCloud);
$info = $pubnub->publish(array(
    'channel' => '22671387349985', // REQUIRED Channel to Send
    'message' => '2013-12-18 06:45:25+0000$streamsong$Give Me Everything$https://ec-media.soundcloud.com/sQwcTyf3FA0H.128.mp3?ff61182e3c2ecefa438cd02102d0e385713f0c1faf3b033959566bfd0c01e2111862c05e14f49d18cae6e9507908d3be9896f2cf89700d2d0d56cb1695579af0827275c88b&AWSAccessKeyId=AKIAJ4IAZE5EOI7PA7VQ&Expires=1387349511&Signature=6QmOcSFX5ZpKmiBEEvFk9I5l36E%3D$2'   // REQUIRED Message String/Array
));

print_r($info);
?>