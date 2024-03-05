<?php

/**
 * A base model for handling the database connections
 */
class MySQLModel extends Model
{
	protected $_dbh = null;
	protected $_table = "";
	
	//PDO class php
	// https://www.php.net/manual/en/class.pdo.php
	
	public function __construct()
	{
		// parses the settings file
		$settings = parse_ini_file(ROOT_PATH . '/config/settingsMySQL.ini', true);
		
		// starts the connection to the database
		$this->_dbh = new PDO(
			sprintf(
				"%s:host=%s;dbname=%s",
				$settings['database']['driver'],
				$settings['database']['host'],
				$settings['database']['dbname']
			),
			$settings['database']['user'],
			$settings['database']['password'],
			array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
		);
		
		$this->init();
	}
	
	public function init()
	{
		//I decided to set the table here.... from config file....
		// parses the settings file
		$settings = parse_ini_file(ROOT_PATH . '/config/settingsMySQL.ini', true);
		$this->_setTable($settings['database']['table_name']);
	}
	
	/**
	 * Sets the database table the model is using
	 * @param string $table the table the model is using
	 */
	protected function _setTable($table)
	{
		$this->_table = $table;
	}
	
	public function fetchOne($id)
	{
		$sql = 'select * from ' . $this->_table;
		$sql .= ' where task_id = ?';
		
		//PDO::prepare — Prepares a statement for execution and returns a statement object 
		$statement = $this->_dbh->prepare($sql);
		// The statement template can contain zero or more named (:name) or question mark (?) 
		//parameter markers for which real values will be substituted when the statement is executed. 
		//Both named and question mark parameter markers cannot be used within the same statement template;
		// only one or the other parameter style. Use these parameters to bind any user-input, 
		//do not include the user-input directly in the query.
		
		//Source: https://www.php.net/manual/en/pdostatement.execute.php
		$statement->execute(array($id));
		
		//Source: https://www.php.net/manual/en/pdostatement.fetch.php
		return $statement->fetch(PDO::FETCH_OBJ);
	}
	
	
	//Use to fetch all the occurrences of the database table
	//Dude's Function
	public function fetchAll(){
		$sql = 'select * from ' . $this->_table;		
		//PDO::prepare — Prepares a statement for execution and returns a statement object 
		$statement = $this->_dbh->prepare($sql);
		// The statement template can contain zero or more named (:name) or question mark (?) 
		//parameter markers for which real values will be substituted when the statement is executed. 
		//Both named and question mark parameter markers cannot be used within the same statement template;
		// only one or the other parameter style. Use these parameters to bind any user-input, 
		//do not include the user-input directly in the query.
		
		//Source: https://www.php.net/manual/en/pdostatement.execute.php
		$statement->execute(array());
		
		//Source: https://www.php.net/manual/en/pdostatement.fetch.php
		//return $statement->fetch(PDO::FETCH_OBJ);
		
		//Yup workaround to work as JSON MODEL
		//$databaseArray = $statement->fetch(PDO::FETCH_OBJ); 
		$databaseStdObj = $statement->fetchAll(PDO::FETCH_OBJ);
		
		//Convert standard object to associative array:
		$outputArray = json_decode(json_encode($databaseStdObj), true);
		
		$outputArray = array(
			'tasks' => (array) $outputArray
		);

		return $outputArray;
		
	}
	
	
	/**
	 * Saves the current data to the database. If an key named "id" is given,
	 * an update will be issued.
	 * @param array $data the data to save
	 * @return int the id the data was saved under
	 */
	public function save($data = array())
	{
		$sql = '';
		
		$values = array();
		
		//Workaround for setting null in none existing parameters:
		$data = setNullIfNotExisting($data);

		if (array_key_exists('task_id', $data) and $data['task_id'] != 0) {
			
			//If task_type changes from something that is not finished to finished update finalization date:
			//For version 2... after "finishing" Srpint 4 ... Tired of this...
			//Okay done...  
			$storedItem = $this->fetchOne($data['task_id']);
			if(isset($storedItem) and !empty($storedItem)){
				$storedElArray = json_decode(json_encode($storedItem), true);
				if(isset($storedElArray) and !empty($storedElArray)){
					if($storedElArray['task_type'] != "Finished" and $data['task_type'] == "Finished"){
						//Update finalization date:
						$data['finalization_date'] =date("Y-m-d H:i:s");
					}
				}
			}
			
			
			$sql = 'update ' . $this->_table . ' set ';
			
			$first = true;
			foreach($data as $key => $value) {
				if ($key != 'task_id') {
					$sql .= ($first == false ? ',' : '') . ' ' . $key . ' = ?';
					$values[] = $value;
					$first = false;
				}
			}
			
			// adds the id as well
			$values[] = $data['task_id'];
			$sql .= ' where task_id = ?';// . $data['id'];
			$statement = $this->_dbh->prepare($sql);
			return $statement->execute($values);
		}
		else {
			$keys = array_keys($data);
			$sql = 'insert into ' . $this->_table . '(';
			$sql .= implode(',', $keys);
			$sql .= ')';
			$sql .= ' values (';
			
			$dataValues = array_values($data);
			$first = true;
			foreach($dataValues as $value) {
				$sql .= ($first == false ? ',?' : '?');
				
				$values[] = $value;
				
				$first = false;
			}
			$sql .= ')';
			
			$statement = $this->_dbh->prepare($sql);
			if ($statement->execute($values)) {
				return $this->_dbh->lastInsertId();
			}
		}
		
		return false;
	}
	
	/**
	 * Deletes a single entry
	 * @param int $id the id of the entry to delete
	 * @return boolean true if all went well, else false.
	 */
	public function delete($id)
	{
		$statement = $this->_dbh->prepare("delete from " . $this->_table . " where task_id = ?");
		return $statement->execute(array($id));
	}
}
