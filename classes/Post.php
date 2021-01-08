<?php
if (!defined('Access')) {
	die('Silence is gold');
}

require_once CLASSES_DIR . '/DbConnection.php';

class Post {
	private $mysqli;
	/** @var DbConnection */
	private $conn;


	public function __construct() {
		$this->conn = new DbConnection();
		$this->mysqli = $this->conn->connect();
	}

	/**
	 * Adds a user to the database
	 * @param Post post object with userId,title and body properties.
	 *
	 * @return bool true if succeed, else false.
	 */
	public function create($post){
		// check all data exist
		if(!(isset($post->userId) && isset($post->title) && isset($post->body))){
			return false;
		}

		//validate data
		$userId = filter_var($post->userId,FILTER_VALIDATE_INT);
		$title  = filter_var($post->title, FILTER_SANITIZE_STRING);
		$body  = filter_var($post->body, FILTER_SANITIZE_STRING);
		$now = date('Y-m-d H:i:s');

		// if the post already exist don't try to push it again
		if(!isset($post->id)){
			$id = null;
		}
		else{
			$id = filter_var($post->id,FILTER_VALIDATE_INT);
			if($this->isExist($post->id)){
				return false;
			}
		}

		// insert
		$stmt = $this->mysqli->prepare("INSERT INTO posts (`id`,`user_id`,`title`,`body`,`updated_at`,`created_at`) VALUES (?, ?, ?, ?, '$now','$now')");
		$stmt->bind_param('iiss',$id, $userId,$title,$body);
		$isSucceed = $stmt->execute();
		$stmt->close();

		if($isSucceed){
			return $this->mysqli->insert_id;
	   }

	   return false;
	}


	/**
	 * return the post by id if exists in your database
	 * @param postId 
	 *
	 * @return Post post object or false
	 */
	public function searchById($postId){
		$postId = filter_var($postId,FILTER_VALIDATE_INT);

		if($stmt = $this->mysqli->prepare("SELECT * FROM posts WHERE id=?")){
			$stmt->bind_param("i", $postId);
			$stmt->execute();
			$result = $stmt->get_result();

			if($result->num_rows == 0){
				return false;
			}
			
			$myrow = $result->fetch_assoc();

			// create post object
			$post = new stdClass();
			$post->id = $myrow["id"];
			$post->userId = $myrow["user_id"];
			$post->title = $myrow["title"];
			$post->body = $myrow["body"];
			$post->createdAt = $myrow["created_at"];
			$post->updatedAt = $myrow["updated_at"];
			return $post;
		}
		return false;
	}

	
	/**
	 * return all posts that belong to the user if exists in your database
	 * @param userId 
	 *
	 * @return Array array of post objects
	 */
	public function searchByUserId($userId){
		$userId = filter_var($userId,FILTER_VALIDATE_INT);

		if($stmt = $this->mysqli->prepare("SELECT * FROM posts WHERE `user_id`=?")){
			$stmt->bind_param("i", $userId);
			$stmt->execute();
			$result = $stmt->get_result();
			$posts = [];
			while ($myrow = $result->fetch_assoc()) {
				$post = new stdClass();
				$post->id = $myrow["id"];
				$post->userId = $myrow["user_id"];
				$post->title = $myrow["title"];
				$post->body = $myrow["body"];
				$post->createdAt = $myrow["created_at"];
				$post->updatedAt = $myrow["updated_at"];
				$posts[] = $post;
			}
			return $posts;
		}
		return false;
	
	}

	/**
	 * return all the matching posts that contain the given string in the post body or title in your database
	 * @param String content 
	 *
	 * @return Array array of post objects
	 */
	public function searchByContent($content){
		$content = filter_var($content,FILTER_SANITIZE_STRING);

		if($stmt = $this->mysqli->prepare("SELECT * FROM posts WHERE `body` like ?")){
			$stmt->bind_param("s", "%".$content."%");
			$stmt->execute();
			$result = $stmt->get_result();
			$posts = [];
			while ($myrow = $result->fetch_assoc()) {
				$post = new stdClass();
				$post->id = $myrow["id"];
				$post->userId = $myrow["user_id"];
				$post->title = $myrow["title"];
				$post->body = $myrow["body"];
				$post->createdAt = $myrow["created_at"];
				$post->updatedAt = $myrow["updated_at"];
				$posts[] = $post;
			}
			return $posts;
		}
		return false;
	}

	
	/**
	 *
	 * @return Array of post objects or false
	 */
	public function getAllPosts(){
		if($stmt = $this->mysqli->prepare("SELECT * FROM posts")){
			$stmt->execute();
			$result = $stmt->get_result();
			$posts = [];
			while ($myrow = $result->fetch_assoc()) {
				$post = new stdClass();
				$post->id = $myrow["id"];
				$post->userId = $myrow["user_id"];
				$post->title = $myrow["title"];
				$post->body = $myrow["body"];
				$post->createdAt = $myrow["created_at"];
				$post->updatedAt = $myrow["updated_at"];
				$posts[] = $post;
			}
			return $posts;
		}
		return false;
	}
	
	/**
	 *
	 * @return Array of post objects or false
	 */
	public function getAvereageMonthlyAndWeeklyPostsByUser(){
		if($stmt = $this->mysqli->prepare("SELECT user_id,
		count(*) / count(distinct yearweek(created_at)) as weekly_average,
		count(*) / count(distinct year(created_at), month(created_at)) as monthly_average
		from posts t
		group by user_id")){
			$stmt->execute();
			$result = $stmt->get_result();
			$posts = [];
			while ($myrow = $result->fetch_assoc()) {
				$post = new stdClass();
				$post->userId = $myrow["user_id"];
				$post->weeklyAverage = $myrow["weekly_average"];
				$post->MonthlyAverage = $myrow["monthly_average"];
				$posts[] = $post;
			}
			return $posts;
		}
		return false;
	}

	/**
	 * return all the matching posts that contain the given string in the post body or title in your database
	 * @param Integer post id 
	 *
	 * @return Boolean true if post exist in the database
	 */
	private function isExist($postId){
		$postId = filter_var($postId,FILTER_VALIDATE_INT);

		if($stmt = $this->mysqli->prepare("SELECT count(id) as count FROM posts WHERE id=?")){
			$stmt->bind_param("i", $postId);
			$stmt->execute();
			$result = $stmt->get_result();
			$myrow = $result->fetch_assoc();

			if($myrow['count'] != 0){
				return true;
			} 
		}

		return false;
	}

	
}
