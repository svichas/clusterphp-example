<?php 


use Cluster\Core\Model\Model;


class Host extends Model {


	public function setup() {
		$this->createTable("host_table", [
			"host_title" => "VARCHAR(555)",
			"host_content" => "VARCHAR(10000)",
			"host_creator" => "INT(11)",
			"host_date" => "INT(22)"
		]);
	}

	public function createHost($host_title="", $host_content="", $host_creator=0) {

		//check if host_title already exists

		$query = $this->prepare("SELECT * FROM host_table WHERE host_title=:host_title LIMIT 1;");
		$query->execute([
			":host_title" => $host_title
		]);

		if ($query->rowCount()) {
			return false;
		} else {
			$this->insert("host_table", [
				"host_title" => $host_title,
				"host_content" => $host_content,
				"host_creator" => $host_creator,
				"host_date" => time()
			]);

			return true;
		}

	}

	public function updateHost($host_url="", $content="") {
		$query = $this->prepare("UPDATE `host_table` SET `host_content`=:content WHERE `host_title`=:url LIMIT 1;");
		$query->execute([
			":content" => $content,
			":url" => $host_url
		]);
		return true;
	}

	public function getLoggedInUserHost($host_url="", $uid=0) {
		$query = $this->prepare("SELECT * FROM `host_table` WHERE `host_title`=:host_url AND `host_creator`='$uid' LIMIT 1;");
		$query->execute([
			":host_url" => $host_url
		]);

		if ($query->rowCount()) {
			return $query->fetch();
		} else return null;

	}

	public function deleteHost($host_url="", $uid=0) {
		$query = $this->prepare("DELETE FROM `host_table` WHERE `host_title`=:url AND `host_creator`='$uid' LIMIT 1;");
		$query->execute([
			":url" => $host_url
		]);
		return true;
	}


	public function getHostHtml($host_url="") {
		$query = $this->prepare("SELECT * FROM `host_table` WHERE `host_title`=:host_url LIMIT 1;");
		$query->execute([
			":host_url" => $host_url
		]);
		$html = "<b>[Hoster]</b> Host not found.";
		if ($query->rowCount()) {
			$row = $query->fetch();
			$html = $row['host_content'];
		}

		return $html;
	}
	
	public function getUserHosts($user_id=0) {
		
		$query = $this->query("SELECT * FROM `host_table` WHERE `host_creator`='$user_id' ORDER BY `host_date` DESC LIMIT 100;");

		return $query->fetchAll();

	}


}