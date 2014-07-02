<?php

/**
 * @ Description : This class Functions includes all the functions to be used related to playlist addition/updation in  the app.

 * @ Copyright : Boombotix
 * @ Developed by : Click Labs Pvt. Ltd.
 */
header('Content-type: application/json');
require_once 'Soundcloud.php';

class SoundcloudPlaylist {

    private $_funcObj;
    private $_DAL;

    public function __construct() {

        $this->_DAL = new DAL;
        $this->_funcObj = new Commonfunction;
    }

    function __autoload($class_name) {
        require $class_name . '.class.php';
    }

    /*
     * ------------------------------------------------------
     *  To add playlist in user SoundCloud account
     * ------------------------------------------------------
     */

    public function addPlaylistToSoundCloud($data = array()) {

        $Token = $data['access_token'];
        $name = $data['name'];

        $checkarray = array($Token, $name);

        $retResult = $this->_funcObj->checkBlank($checkarray);
        if ($retResult == 1) {
            $response = array("error" => "Some Parameter Missing!");
            return $response;
        } else {

            /* create a client object with access token */
            $client = new Services_Soundcloud('75f4857e89c62292aef8253e55c13324', '04452c1122d2bedf19842ba378d3592a');

            //$accessToken = $client->accessTokenRefresh($Token);
            $accessToken = $Token;

            $client->setAccessToken($accessToken);

            //create an array of track ids
//        $playlist = 'playlist[title]=MyPlaylist&playlist[tracks][][id]=21778201';
//        $playlist .= '&playlist[tracks][][id]=22448500';

            $playlist = 'playlist[title]=' . $name;


            /* create the playlist */
            $response = json_decode($client->post('playlists', $playlist), true);


            $id = $response['id'];

            if ($response['error']) {
                $response = array("error" => "Playlist not added, Try Again.");
            } else {
                $response = array("playlist_id" => $id);
            }


            return $response;
        }

        /* get playlist */
//        $playlist = json_decode($client->get('playlists/myplaylist'));
//
//        return $playlist;
    }

    /*
     * ------------------------------------------------------
     *  To search tracks in SoundCloud 
     * ------------------------------------------------------
     */

    public function searchTrackFromSoundCloud($data = array()) {

        $term = $data['search_term'];

        $checkarray = array($term);

        $retResult = $this->_funcObj->checkBlank($checkarray);
        if ($retResult == 1) {
            $response = array("error" => "Some Parameter Missing!");
            return $response;
        } else {

            /* create a client object with access token */
            $client = new Services_Soundcloud('75f4857e89c62292aef8253e55c13324');

// find all sounds of buskers licensed under 'creative commons share alike'
            $tracks = $client->get('tracks', array('q' => $term));


            $tracks = json_decode($tracks, true);

            $searchData = array();

            $number = count($tracks);

            for ($i = 0; $i < $number; $i++) {

                $searchData[$i]['id'] = $tracks[$i]['id'];
                $searchData[$i]['title'] = $tracks[$i]['title'];
                $searchData[$i]['image'] = $tracks[$i]['user']['avatar_url'];
            }



//         print_r($searchData);
//         print_r($tracks[0]);
//          print_r($tracks);
//        
//         
//         die();

            return $searchData;
        }
    }

    /*
     * ------------------------------------------------------
     *  To search tracks in SoundCloud 
     * ------------------------------------------------------
     */

    public function addTrackSoundCloudPlayList($data = array()) {

        $accessToken = $data['access_token'];
        $playListID = $data['playlist_id'];
        $trackID = $data['track_id'];

        $checkarray = array($accessToken, $playListID, $trackID);

        $retResult = $this->_funcObj->checkBlank($checkarray);
        if ($retResult == 1) {
            $response = array("error" => "Some Parameter Missing!");
            return $response;
        } else {

// create a client object with access token
            $client = new Services_Soundcloud('75f4857e89c62292aef8253e55c13324');
            $client->setAccessToken($accessToken);

// create an array of track ids
            $tracks = array();

// get playlist
            $playlist = json_decode($client->get('me/playlists/' . $playListID));


// list tracks in playlist
            foreach ($playlist->tracks as $track) {
                $temp = $track->id;
                $tracks[] = $temp;
            }
            $tracks[] = $trackID;

// add tracks to playlist
            $response = $client->updatePlaylist($playListID, $tracks);
            $response1 = json_decode($response, true);

            if ($response1) {
                $response = array("log" => "Track Added Successfully!");
            } else {
                $response = array("error" => "Track not added, Try Again.");
            }


            return $response;
        }
    }

    /*
     * ------------------------------------------------------
     *  To Delete playlist in user SoundCloud account
     * ------------------------------------------------------
     */

    public function deletePlaylistToSoundCloud($data = array()) {

        $Token = $data['access_token'];
        $id = $data['playlist_id'];

        $checkarray = array($Token, $id);

        $retResult = $this->_funcObj->checkBlank($checkarray);
        if ($retResult == 1) {
            $response = array("error" => "Some Parameter Missing!");
            return $response;
        } else {

            /* create a client object with access token */
            $client = new Services_Soundcloud('75f4857e89c62292aef8253e55c13324', '04452c1122d2bedf19842ba378d3592a');

            //$accessToken = $client->accessTokenRefresh($Token);
            $accessToken = $Token;

            $client->setAccessToken($accessToken);

            /* create the playlist */
            $response = json_decode($client->delete('playlists/' . $id), true);


            if ($response['error']) {
                $response = array("error" => "Playlist not deleted, Try Again.");
            } else {
                $response = array("log" => "playlist deleted successfully");
            }


            return $response;
        }
    }

    /*
     * ------------------------------------------------------
     *  To Delete playlist in user SoundCloud account
     * ------------------------------------------------------
     */

    public function deleteTrackSoundCloudPlayList($data = array()) {

        $accessToken = $data['access_token'];
        $playListID = $data['playlist_id'];
        $trackID = $data['track_id'];

        $checkarray = array($accessToken, $playListID, $trackID);

        $retResult = $this->_funcObj->checkBlank($checkarray);
        if ($retResult == 1) {
            $response = array("error" => "Some Parameter Missing!");
            return $response;
        } else {

// create a client object with access token
            $client = new Services_Soundcloud('75f4857e89c62292aef8253e55c13324');
            $client->setAccessToken($accessToken);

// create an array of track ids
            $tracks = array();

// get playlist
            $playlist = json_decode($client->get('me/playlists/' . $playListID));


// list tracks in playlist
            foreach ($playlist->tracks as $track) {

                $temp = $track->id;
                if ($temp != $trackID) {
                    $tracks[] = $temp;
                }
            }


            if (count($tracks) == 0) {

                $name = $playlist->permalink;

                $response12 = json_decode($client->delete('playlists/' . $playListID), true);

                $playlist = 'playlist[title]=' . $name;


                /* create the playlist */
                $response = json_decode($client->post('playlists', $playlist), true);

                if ($response['error']) {
                    $response = array("error" => "Track not deleted, try again.");
                } else {
                    $response = array("log" => "Track Deleted Successfully!");
                }
            } else {
                $response = $client->updatePlaylist($playListID, $tracks);

                $response1 = json_decode($response, true);

                if ($response1) {
                    $response = array("log" => "Track Deleted Successfully!");
                } else {
                    $response = array("error" => "Track not deleted, try again.");
                }
            }





            return $response;
        }
    }

}

