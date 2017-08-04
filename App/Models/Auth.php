<?php 


use Cluster\Core\Model\Model;


class Auth extends Model {


	public function setup() {
		$this->createTable("users_table", [
			"username" => "VARCHAR(55)",
			"email" => "VARCHAR(555)",
			"password" => "VARCHAR(555)"
		]);
	}

	public function loginRequest($login="",$password="") {

		$result = [
			"success"       => false,
			"user_username" => "",
			"user_id"       => 0
		];

		$query = $this->prepare("SELECT * FROM users_table WHERE `username`=:login OR `email`=:login LIMIT 1;");
		$query->execute([
			":login" => $login
		]);
		if ($query->rowCount()) {
			//Found matching username or email
			$row = $query->fetch();

			$hash = $row['password'];

			if (password_verify($password, $hash)) {

				$result = [
					"success"       => true,
					"user_username" => $row['username'],
					"user_id"       => $row['id']
				];

				self::setSession($row['id'],$row['username']);
			}

		}

		return $result;

	}

	public static function setSession($user_id, $user_username) {
		$_SESSION['user_id'] = $user_id;
		$_SESSION['user_username'] = $user_username;
		return true;
	}

	public static function isLoggedIn() {

		return isset($_SESSION['user_id'])&&isset($_SESSION['user_username']);

	}

	public function sessionDestroy() {
		unset($_SESSION['user_id']);
		unset($_SESSION['user_username']);
	}


	public function createUser($username="",$email="",$password="") {
		

		$query = $this->prepare("SELECT * FROM users_table WHERE username=:username LIMIT 1");
		$query->execute([
			":username" => $username
		]);

		if (!$query->rowCount()) {
			$this->insert("users_table", [
				"username" => $username,
				"email" => $email,
				"password" => password_hash($password, PASSWORD_DEFAULT)
			]);
			return true;
		} else {
			return false;
		}


	}


}