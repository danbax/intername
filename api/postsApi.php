<?php
/**
 * adds new user and post 
 */
require_once '../includes/config.php';
require_once CLASSES_DIR . '/User.php';
require_once CLASSES_DIR . '/Post.php';

$response = new stdClass();

$postId = filter_input(INPUT_GET, 'post_id', FILTER_VALIDATE_INT);
$userId = filter_input(INPUT_GET, 'user_id', FILTER_VALIDATE_INT);

$type = 'ALL_POSTS';

if(isset($_GET['user_id'])){
    $type='USER_ID';
}

if(isset($_GET['post_id'])){
    $type='POST_ID';
}

$postClass = new Post();

switch($type){
    case 'ALL_POSTS':
        $posts = $postClass->getAllPosts();
        if($posts === false){
            $response->status = RESULT_ERROR;
            $response->error = "Database error";
        }
        else{
            $response->status = RESULT_SUCCESS;
            $response->data = $posts;
        }
    break;
    case 'POST_ID':
        $posts = $postClass->searchById($postId);
        if($posts === false){
            $response->status = RESULT_ERROR;
            $response->error = "Database error";
        }
        else{
            $response->status = RESULT_SUCCESS;
            $response->data = $posts;
        }
        
    break;
    case 'USER_ID':
        $posts = $postClass->searchByUserId($userId);
        if($posts === false){
            $response->status = RESULT_ERROR;
            $response->error = "Database error";
        }
        else{
            $response->status = RESULT_SUCCESS;
            $response->data = $posts;
        }
    break;
}

sendResponse($response);

function sendResponse($response) {
	header("Content-type:application/json");
	echo json_encode($response, JSON_UNESCAPED_UNICODE);
	die;
}
