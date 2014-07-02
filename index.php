<?php

header('Content-type: application/json');
require 'conf/DAL.class.php';

function __autoload($class_name) {
    include 'inc/classes/' . $class_name . '.class.php';
}

try {
    $postInput = array();
    $response = array();

    // Handling the supported actions:
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $postInput = $_POST;

        $postInput = array_map('trim', $postInput);
    }
    switch (isset($_GET['action']) ? ($_GET['action']) : 'Wrong') {
        /*
         * All Cases related to Registration & Login Process are written in this block.
         * ===================================================================
         * Block Name : Registration Process
         * ===================================================================
         */

        case 'email_login':
            $userLoginObj = new UserLogin;
            $response = $userLoginObj->getUserData($postInput);
            break;
        case 'fblogin':
            $fbLoginObj = new UserLogin;
            $response = $fbLoginObj->getFbUser($postInput);
            break;
        case 'login':
            $userLoginObj = new UserLogin;
            $response = $userLoginObj->getUserAccess($postInput);
            break;
         case 'edit_profile':
            $userLoginObj = new UserLogin;
            $response = $userLoginObj->editProfileFromAccessToken($postInput);
            break;
        case 'update_to_premium':
            $userLoginObj = new UserLogin;
            $response = $userLoginObj->updateUserToPremiumFromAccessToken($postInput);
            break;
        case 'change_premium':
            $userLoginObj = new UserLogin;
            $response = $userLoginObj->updatePremiumFromEmail($postInput);
            break;
        case 'premium_users':
            $userLoginObj = new UserLogin;
            $response = $userLoginObj->showPremiumUsers($postInput);
            break;
        
        

        /*
         * ===================================================================
         * Block Name : End Registration Process
         * ===================================================================
         * /
         */

        /*
         * All Cases related to other Playlist and sharing Friends Process in this block.
         * ===================================================================
         * Block Name : Playlist and sharing Friends Process
         * ===================================================================
         */

        case 'fb_friends':
            $userLoginObj = new UserLogin;
            $response = $userLoginObj->getFbFriendsFromFbidAndFbAccessToken($postInput);
            break;
        case 'near_dj':
            $userLoginObj = new UserLogin;
            $response = $userLoginObj->getNearByDjFromUserId($postInput);
            break;
        case 'forgot_password':
            $userLoginObj = new UserLogin;
            $response = $userLoginObj->forgotPasswordFromEmail($postInput);
            break;

        case 'share_djplaylist':
            $userLoginObj = new UserLogin;
            $response = $userLoginObj->shareDjPlayListFromUserIdToFriendId($postInput);
            break;
        case 'friends_sharedwith':
            $userLoginObj = new UserLogin;
            $response = $userLoginObj->getFriendsSharedWithFromUserId($postInput);
            break;
        case 'accept_request':
            $userLoginObj = new UserLogin;
            $response = $userLoginObj->acceptRequestFromUserId($postInput);
            break;
        case 'logout':
            $userLoginObj = new UserLogin;
            $response = $userLoginObj->logoutFromUserId($postInput);
            break;
        case 'clear_playlist':
            $userLoginObj = new UserLogin;
            $response = $userLoginObj->clearPlaylistFromUserAccessToken($postInput);
            break;
        case 'request_dj':
            $userLoginObj = new UserLogin;
            $response = $userLoginObj->requestDjFromUserIdAndDjId($postInput);
            break;
         case 'accept_listener_request':
            $userLoginObj = new UserLogin;
            $response = $userLoginObj->acceptRequestFromDjId($postInput);
            break;
        
        case 'previous_session':
            $userLoginObj = new UserLogin;
            $response = $userLoginObj->getPreviousSessionFromUserId($postInput);
            break;

        case 'connection':
            $userLoginObj = new UserLogin;
            $response = $userLoginObj->getconnectionFromUserId($postInput);
            break;
         case 'shuffle_playlist':
            $userLoginObj = new UserLogin;
            $response = $userLoginObj->setPlaylistOrderForUser($postInput);
            break;
        
        

        /*
         * ===================================================================
         * Block Name : End Playlist and sharing Friends Process
         * ===================================================================
         * /
         */


        /*
         * All Cases related to Playlist creation Process are written in this block.
         * ===================================================================
         * Block Name : Playlist Process
         * ===================================================================
         */

        case 'add_to_playlist':
            $playListObj = new PlayList;
            $response = $playListObj->addPlayListSongFromUserIdSessionIdChannelId($postInput);
            break;
        case 'get_djplaylist':
            $playListObj = new PlayList;
            $response = $playListObj->getDjPlayListFromUserAccessToken($postInput);
            break;

        case 'del_fromdjplaylist':
            $playListObj = new PlayList;
            $response = $playListObj->deleteUserPlaylistSongFromUserAccessTokenAndSongId($postInput);
            break;
        case 'get_previous_session_djplaylist':
            $playListObj = new PlayList;
            $response = $playListObj->getPreviousSessionDjPlayListFromUserIdAndSessionId($postInput);
            break;

        case 'soundcloud':
            $playListObj = new PlayList;
            $response = $playListObj->getLocation1($postInput);
            break;
        
        case 'pubnub_data':
            $playListObj = new PlayList;
            $response = $playListObj->getPubnubDataFromDjId($postInput);
            break;

        case 'delete_listener':
            $playListObj = new PlayList;
            $response = $playListObj->deleteListenerFromAccessTokenAndListenerId($postInput);
            break;
        
        case 'disconnect_from_dj':
            $playListObj = new PlayList;
            $response = $playListObj->disconnectDjFromAccessTokenAndDjId($postInput);
            break;
        case 'play_bot_playlist':
            $playListObj = new PlayList;
            $response = $playListObj->playBotPlaylist($postInput);
            break;
        case 'bot_check_cron':
            $playListObj = new PlayList;
            $response = $playListObj->botCheckCron($postInput);
            break;


        /*
         * ===================================================================
         * Block Name : End Playlist Process
         * ===================================================================
         * /
         */
        
        
          /*
         * All Cases related to soundcloud are written in this block.
         * ===================================================================
         * Block Name : Soundcloud
         * ===================================================================
         */

        case 'playlist_add':
            $SoundcloudObj = new SoundcloudPlaylist;
            $response = $SoundcloudObj->addPlaylistToSoundCloud($postInput);
            break;
       
         case 'track_search':
            $SoundcloudObj = new SoundcloudPlaylist;
            $response = $SoundcloudObj->searchTrackFromSoundCloud($postInput);
            break;
        
         case 'add_track':
            $SoundcloudObj = new SoundcloudPlaylist;
            $response = $SoundcloudObj->addTrackSoundCloudPlayList($postInput);
            break;
        
         case 'delete_playlist':
            $SoundcloudObj = new SoundcloudPlaylist;
            $response = $SoundcloudObj->deletePlaylistToSoundCloud($postInput);
            break;
        
        case 'delete_track':
            $SoundcloudObj = new SoundcloudPlaylist;
            $response = $SoundcloudObj->deleteTrackSoundCloudPlayList($postInput);
            break;

        /*
         * ===================================================================
         * Block Name : Soundcloud Process
         * ===================================================================
         * /
         */
        

        default:
            throw new Exception('Wrong action !');
    }
   

    echo str_replace("\/", "/", json_encode($response, JSON_PRETTY_PRINT));
}
// If any exception occurs then send the error in json.
catch (Exception $e) {
    die(json_encode(array('error' => $e->getMessage())));
}



/* End of file index.php */
/* Location: index.php */
?>