<?php

/**
 * @ Description : This file will receive the action in GET method.
 * And then the corresponding class will be called according to $_GET['action'].
 * @ Copyright : Founder Cave Portal
 * @ Developed by : Click Labs Pvt. Ltd.
 */
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
    }
    switch (isset($_GET['action']) ? ($_GET['action']) : 'Wrong') {


        // Login Process.
        case 'login':
            $getLogin = new Functions;
            $username = isset($_POST['email']) ? trim($_POST['email']) : "";
            $password = isset($_POST['password']) ? trim($_POST['password']) : "";
            $response = $getLogin->getLoginIntoFromUsernameAndPassword($username, $password);
            break;


        /*
         * All Cases related to Apps in Admin panel are written in this block.
         * ===================================================================
         * Block Name : Bots
         * ===================================================================
         */

       
        case 'getusers':
            $appObj = new adminUser;
            $userName = isset($_POST['user_name']) ? trim($_POST['user_name']) : "";
            $response = $appObj->getAllUsers($userName);
            break;
        
        
        
        case 'create_user':
            $appObj = new adminUser;           
            $response = $appObj->createUser($postInput);
            break;
        
        case 'bot_status':
            $appObj = new adminUser;           
            $response = $appObj->getBotsStatus($postInput);
            break;
        case 'play_bot_playlist':
            $playListObj = new adminUser;
            $response = $playListObj->playBotPlaylist($postInput);
            break;
        case 'edit_user':
            $appObj = new adminUser;           
            $response = $appObj->editUser($postInput);
            break;
        case 'stop_bot':
            $appObj = new adminUser;           
            $response = $appObj->stopBot($postInput);
            break;

       


        /*
         * ===================================================================
         * Block Name : End Apps
         * ===================================================================
         * /
         */


        // For any other or default action.
        default:
            throw new Exception('Wrong action !');
    }

    // Returns json output.
    echo str_replace("\/", "/", json_encode($response, JSON_PRETTY_PRINT));
}
// If any exception occurs then send the error in json.
catch (Exception $e) {
    die(json_encode(array('error' => $e->getMessage())));
}

// function to check whether we are getting data in post or not. If POST is empty then show error in json format.
function inputPostCheck($postInput) {
    if (empty($postInput)) {
        die(json_encode(array('error' => 'Some parameters missing')));
    }
}

/* End of file index.php */
/* Location: index.php */
?>