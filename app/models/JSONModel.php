<?php

//JSON Model PHP is the php script that defines the model for a JSON file persistency.

class JSONModel extends Model{
	
	protected $pathJSONFile = "";
	
	//Construct must be defined again because original construct is for a connection to an MySQL database
	public function __construct()
	{
		// parses the settings file
		//REMEMBER Create settingsJSON.ini file
		//Source: https://www.php.net/manual/en/function.parse-ini-file.php
		//parse_ini_file() loads in the ini file specified in filename, and returns the settings in it in an associative array. 
		$settings = parse_ini_file(ROOT_PATH . '/config/settingsJSON.ini', true);
		
		//Store the path of the JSON File:
		$this->pathJSONFile = ROOT_PATH . $settings['JSONPersistency']['filePath'];

		//Will do nothing, nonetheless conserverd for the sake of... doing nothing...what an amazing thing to do...
		$this->init();
	}
	
	
	//Use to fetch the first occurrence of the database table with id $id
	public function fetchOne($id){
		$json_data = file_get_contents($this->pathJSONFile);
		$arrayJSONFile = json_decode($json_data, TRUE);
		$outputTask = [];
		foreach( $arrayJSONFile as $task){
			if(isset($task['task_id']) and $task['task_id'] = $id){
				$outputTask = $task;
				break;
			}
		}
		if(isset($outputTask)){
			return $outputTask;
		}else{
			return 0;
		}
		
	}
	
	//Use to fetch all the occurrences of the database table
	public function fetchAll(){
		debug_to_console($this->pathJSONFile);
		$json_data = file_get_contents($this->pathJSONFile);
		$arrayJSONFile = json_decode($json_data, TRUE);
		
		if(isset($arrayJSONFile)){
			return $arrayJSONFile;
		}else{
			return Null;
		}
	}
	
	//Saves the data in the dataset/JSON file
	//The input is an associative array
	//If id param name is give it updates the given id
	//If it is not given it creates a new row
	//Saves just one
	public function save($data = array()){
	//public function save($data = []){
		//Store JSON file in variable:
		$json_data = file_get_contents($this->pathJSONFile);
		$arrayJSONFile = json_decode($json_data, TRUE);
		//debug_to_console("arrayJsonFile:");
		//debug_to_console($arrayJSONFile['tasks'][0]['task_id']);
		$modifiedBool = False;
		$outputArray = [];
		
		//Check if there is is id to save and IF there YES is modify data
		$maxId = 0;
		foreach( $arrayJSONFile['tasks'] as &$task){
			if($task['task_id'] == $data['task_id']){
				$task['user'] = $data['user'];
				//$task['task_type'] = $data['task_type'];
				$task['description'] = $data['description'];
				$task['creation_date'] = $data['creation_date'];
				$task['finalization_date'] = $data['finalization_date'];
				$modifiedBool = True;
				
				//Updating finalization date if was NOT finished and now is Finished:
				if( strtoupper($task['task_type']) != "FINISHED" and (strtoupper($data['task_type']) == "FINISHED") ){
					$task['finalization_date'] = date("Y-m-d H:i");
					$task['task_type'] = $data['task_type'];
				}else{
					$task['task_type'] = $data['task_type'];
				}				
				
				break;
			}
			
			if($task['task_id'] > $maxId){
				$maxId = $task['task_id'];
			}
		}
		
		//Select new task_id if needed - Not modified
		if($modifiedBool == False){
			//modify the values for new added task:
			
			$dataNewTask['task_id'] = $maxId+1;
			$dataNewTask['user'] = $data['user'];
			$dataNewTask['task_type'] = $data['task_type'];
			$dataNewTask['description'] = $data['description'];
			
			//$dataNewTask['creation_date'] = $data['creation_date'];
			$dataNewTask['creation_date'] = date("Y-m-d H:i");
			
			$dataNewTask['finalization_date'] = $data['finalization_date'];
			
			
			
			
		}
			
		
		
		// If there is not task_id or the task does not exist create a new task
		if($modifiedBool == False){
			//Not modified therefore new data must be appended:
			$arrayJSONFile ['tasks'][] = $dataNewTask;
		}
		//Write tha new or modified data into the file
		//$jsonArray = json_encode($arrayJSONFile);
		//I want it pretty!!
		$jsonArray = json_encode($arrayJSONFile, JSON_PRETTY_PRINT);
		file_put_contents($this->pathJSONFile, $jsonArray);		
		
		return 1;		
	}
	
	
	//Deletes an row selected by $id
	public function delete($id){
		//Store JSON file in variable:
		$json_data = file_get_contents($this->pathJSONFile);
		$arrayJSONFile = json_decode($json_data, TRUE);
		
		$index = 0;
		foreach( $arrayJSONFile['tasks'] as $task){
			if(intval($task['task_id']) == intval($id)){
				//CHECK if works for deleting all tasks
				array_splice($arrayJSONFile['tasks'], $index, 1);
				//Write tha new or modified data into the file
				//$jsonArray = json_encode($arrayJSONFile);
				$jsonArray = json_encode($arrayJSONFile, JSON_PRETTY_PRINT);
				file_put_contents($this->pathJSONFile, $jsonArray);
				
				//It returns 1 on success
				return 1;
			}
			$index++;
		}
		//It returs 0 if the element with id $id was not found
		return 0;
		
	}
	
	

}



?>
