<?php
if (!defined('Access')) {
	die('Silence is gold');
}

require_once CLASSES_DIR . '/DbConnection.php';

class User {
	private $mysqli;
	/** @var DbConnection */
	private $conn;


	public function __construct() {
		$this->conn = new DbConnection();
		$this->mysqli = $this->conn->connect();
	}

	/**
	 * Adds a user to the database
	 * @param User user object with email and name properties.
	 *
	 * @return bool true if succeed, else false.
	 */
	public function create($user){
		// check all data exist
		if(!(isset($user->email) && isset($user->name))){
			return false;
		}

		$name = filter_var($user->name,FILTER_SANITIZE_STRING);
		$email  = filter_var($user->email, FILTER_VALIDATE_EMAIL);
		$now = date('Y-m-d H:i:s');

	
		// if the user already exist don't try to push it again
		if(!isset($user->id)){
			$id = null;
		}
		else{
			$id = filter_var($user->id,FILTER_VALIDATE_INT);
			if($this->isExist($user->id)){
				return false;
			}
		}

		$stmt = $this->mysqli->prepare("INSERT INTO users (`id`,`name`,`email`,`updated_at`,`created_at`) VALUES (?,?, ?, '$now','$now')");
		$stmt->bind_param('iss', $id,$name,$email);
		$isSucceed = $stmt->execute();
		$stmt->close();
		if($isSucceed){
			 return $this->mysqli->insert_id;
		}

		return false;
	}

	
	private function isExist($userId){
		$userId = filter_var($userId,FILTER_VALIDATE_INT);

		if($stmt = $this->mysqli->prepare("SELECT count(id) as count FROM users WHERE id=?")){
			$stmt->bind_param("i", $userId);
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
