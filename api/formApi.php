<?php
/**
 * adds new user and post 
 */
require_once '../includes/config.php';
require_once CLASSES_DIR . '/User.php';
require_once CLASSES_DIR . '/Post.php';

$response = new stdClass();

$userName = filter_input(INPUT_POST, 'userName', FILTER_SANITIZE_STRING);
$userEmail = filter_input(INPUT_POST, 'userEmail', FILTER_VALIDATE_EMAIL);
$postTitle = filter_input(INPUT_POST, 'postTitle', FILTER_SANITIZE_STRING);
$postBody = filter_input(INPUT_POST, 'postBody', FILTER_SANITIZE_STRING);

if (!(isset($userName) && isset($userEmail) && isset($postTitle) && isset($postBody))
||  $userName=="" || $userEmail=="" || $postTitle=="" || $postBody=="") {
    $response->status = RESULT_ERROR;
    $response->error = 'Insert all valid required fields.';
} else {
    $userClass = new User();

    $user = new stdClass();
    $user->name = $userName;
    $user->email = $userEmail;

    $userId = $userClass->create($user);
    
    $postClass = new Post();
    $post = new stdClass();
    $post->userId = $userId;
    $post->title = $postTitle;
    $post->body = $postBody;
    $isSucceed = $postClass->create($post);

    if(!$isSucceed){
        $response->status = RESULT_ERROR;
        $response->error = "Database error";
    }
    
    $response->status = RESULT_SUCCESS;
}

sendResponse($response);

function sendResponse($response) {
	header("Content-type:application/json");
	echo json_encode($response, JSON_UNESCAPED_UNICODE);
	die;
}
