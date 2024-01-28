<?php



use MongoDB\Client;
use MongoDB\Driver\Manager;
//use MongoDB\Driver\ServerApi;

/**
 * A base model for handling the database connections
 */
class MongoDBModel extends Model
{
	protected $_dbh = null;
	protected $_table = "";
	
	//MongooooooDB
	protected $myMongo = null;
	protected $coll = null;
	protected $db = null;
	protected $dbAndColl = null;
	//PDO class php
	// https://www.php.net/manual/en/class.pdo.php
	
	public function __construct()
	{
		// parses the settings file
		$settings = parse_ini_file(ROOT_PATH . '/config/settingsMongoDB.ini', true);
		
		//Sets the connection parameters:
		$dbAndColl = $settings['database']['dbname'] . "." . $settings['database']['collection_name'];
		$clientStr = $settings['database']['driver'] . "://" . $settings['database']['host'] . ":" .  $settings['database']['port_number'];
		$this->myMongo = new MongoDB\Client($clientStr);
		//$myMongo = new MongoDB\Driver\Manager($clientStr);
		//$myMongo = new MongoDB($clientStr);
		$databaseName = $settings['database']['dbname'];
		$this->db = $this->myMongo->$databaseName;
		$this->coll = $this->db->selectCollection($settings['database']['collection_name']);
		
		//Example queries:
		//$coll->insertOne(['foo'=>'bar']);
		//$coll->findOne(['foo'=>'bar']);
		$this->init();
	}
	
	public function init()
	{

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
		$document = $this->coll->findOne(['task_id' => strval($id)]);
		return $document;
	}
	
	
	//Use to fetch all the occurrences of the database table
	//Dude's Function
	public function fetchAll(){
		
		$cursor = $this->coll->find();
		$dataArray = $cursor->toArray();
		
		//Experimental:
		$datArrayOut = array(
			"tasks" =>  $dataArray
		);

		return $datArrayOut;
		
	}
	
	
	/**
	 * Saves the current data to the database. If an key named "id" is given,
	 * an update will be issued.
	 * @param array $data the data to save
	 * @return int the id the data was saved under
	 */
	public function save($data = array())
	{
		//If id is in collection update
		//If task_id is set and not empty carry on save or modify:
		//if(isset($data['task_id']) and !empty($data['task_id'])){
		if(True){
			if($data['task_id'] != 0){
				//Modify if existing task with task_id
				$taskID = $data['task_id'] ;
				
				
				//Stored data with the ID fetch:
				$storedData = $this->coll->findOne(['task_id' => strval($data['task_id'])]);
				
				//Update Finalization date if task_type goes from Not "Finished" to "Finished"
				if($data['task_type'] == "Finished" and $storedData['task_type'] != "Finished"){
					$data['finalization_date'] = date("Y-m-d H:i:s");
				}
				
				$filter = [
					'task_id' =>  strval($taskID)
				];

				$dataUpdate = [
					'$set' => $data
				];
				
				
				
				
				$updateResult = $this->coll->updateOne($filter,	$dataUpdate);
				
				//debug_to_console("Task Modified MOngo");
				//debug_to_console($updateResult->getModifiedCount());
				//debug_to_console($updateResult->getMatchedCount());
			}else{
				
				//Search for the maximum task_id:
				$filter = [];
				$options = [
					'sort' => [
						'task_id' => -1
					],
					'limit' => 1
				];
				$maxId = $this->coll->find($filter, $options);
				$maxIdArray =  $maxId->toArray();
				//debug_to_console("MaxIdArray:");
				//debug_to_console(var_dump($maxIdArray));
				
				if(isset($maxIdArray[0]['task_id'])){
					$new_ID = $maxIdArray[0]['task_id'] +1;
				}else{
					$new_ID = 1;
				}
				
				
				//Cursor to datafield task_id
				//HERE!!!
				//Find:
				//db.Tasks.find({"task_id" => $},{},{}).pretty();
				// db.Tasks.find().sort({).limit(1).pretty();
				
				$data['task_id'] = strval($new_ID);
				
				$data['creation_date'] = date("Y-m-d H:i:s");
				
				//Change task_id = 0 to the maximum task_id + 1
				
				//debug_to_console("Task Added MOngo");
				//Add the task to the documents:
				$this->coll->insertOne($data);
			}

		
		}

		//If ID is not in collection add
		//debug_to_console("Task Save MOngo");
		return false;
	}
	
	/**
	 * Deletes a single entry
	 * @param int $id the id of the entry to delete
	 * @return boolean true if all went well, else false.
	 */
	public function delete($id)
	{
		/*
		debug_to_console("Calling deleteeeee!!!!");
		debug_to_console($id);
		* 
		* */

		$filter = [
			"task_id" => strval($id)
		];
		$secondParam = [
			'limit' => 1
		];
		
		$this->coll->deleteOne($filter, $secondParam);

	}
	
	
	
	
}
