<?php 
namespace Cluster\Core\Model;

class Model extends \PDO {
	
	public function __construct() {
		
		require __BASE__ ."App".SEP."Config".SEP."Config.php";

		if ($db['DB_DATABASE'] != "") {
			parent::__construct("mysql:host={$db['DB_HOST']};dbname={$db['DB_DATABASE']}",$db['DB_USER'],$db['DB_PASSWORD']);
		}
		
	}


	public function createTable($table_name="", $rows=[]) {


		$rows_sql  = "id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, ";

		$i = 1;
		foreach ($rows as $key => $value) {

			$rows_sql .= "`{$key}` {$value}";

			if ($i != count($rows)) $rows_sql .= ", ";

			$i++;
		}


		$sql_query = "CREATE TABLE `{$table_name}` ({$rows_sql});";

		$this->query($sql_query);

	}

	public function insert($tableName="", $values=[]) {

		$values_placeholder = "";
		$values_data        = [];
		$values_order       = "";

		$i = 1;
		foreach($values as $key => $value) {


			$values_data[":{$key}"] = $value;

			$values_placeholder .= ":{$key}";
			$values_order       .= "{$key}";
			
			if ($i != count($values)) {
				$values_order .= ", ";
				$values_placeholder .= ", ";
			}

			$i+=1;
		}

		$values_order = "({$values_order})";


		$sql_query = "INSERT INTO `{$tableName}` {$values_order} VALUES ({$values_placeholder});";
		
		$q = $this->prepare($sql_query);
		return $q->execute($values_data);

	}

	public static function load($model="") {

		$model_path = __BASE__ ."App".SEP."Models".SEP . $model. ".php";

		if (file_exists($model_path)) {
			require $model_path;
			$model_arr = explode("/", $model);
			$model = end($model_arr);
			return new $model;
		}

	}

}