<?php
/**
 * Write a mysql query that will return the average of posts users created by monthly, and weekly.
 * The columns should be: user_id, monthly_average, weekly_average. 
 */
require_once '../includes/config.php';
require_once CLASSES_DIR . '/User.php';
require_once CLASSES_DIR . '/Post.php';

$post = new Post();
$posts = $post->getAvereageMonthlyAndWeeklyPostsByUser();
$json_string = json_encode($posts, JSON_PRETTY_PRINT);
echo $json_string;