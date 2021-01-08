<?php
/**
 * Fetch users and posts via curl and save them in your database (save only the necessary values).
 */
require_once '../includes/config.php';
require_once CLASSES_DIR . '/User.php';
require_once CLASSES_DIR . '/Post.php';

define("USERS_API_URL","https://jsonplaceholder.typicode.com/users");
define("POSTS_API_URL","https://jsonplaceholder.typicode.com/posts");

/**
 * users
 */

// get data from server via curl
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL,USERS_API_URL);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($curl);

$users = json_decode($response);

// add data to database
$userClass = new User();
foreach($users as $user){
    $userClass->create($user);
}
echo 'users added <br>';

/**
 * posts
 */
curl_setopt($curl, CURLOPT_URL,POSTS_API_URL);
$response = curl_exec($curl);
curl_close ($curl);

$posts = json_decode($response);

// add data to database
$postClass = new Post();
foreach($posts as $post){
    $postClass->create($post);
}
echo 'posts added';