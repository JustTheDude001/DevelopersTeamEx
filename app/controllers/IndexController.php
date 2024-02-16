<?php

function getArrayNewTask(){
	$actRequest = new Request();
	$submit = $actRequest->getParam("submit");
	
	//Parameters that will hold task's information:
	$taskID = NUll;
	$taskCreator = Null;
	$taskType = Null;
	$taskDescription = Null;
	$taskCreationDate = Null;
	$taskFinalizationDate = Null;
	
	if(isset($submit)){
		switch($submit){
			case 'addTask':
			
				$taskID = $actRequest->getParam("addTaskId");
				$taskCreator = $actRequest->getParam("addTaskCreator");
				$taskType = $actRequest->getParam("addTaskType");
				$taskDescription = $actRequest->getParam("addTaskDescription");
				$taskCreationDate = $actRequest->getParam("addCreationDate");
				$taskFinalizationDate = $actRequest->getParam("addFinalizationDate");
				
				$newTask = array(
					"task_id" => $taskID,
					"user" => $taskCreator,
					"task_type" => $taskType,
					"description" => $taskDescription,
					"creation_date" => $taskCreationDate,
					"finalization_date" => $taskFinalizationDate
					);
				break;
				
			case 'modTask':
				$taskID = $actRequest->getParam("modTaskId");
				$taskCreator = $actRequest->getParam("modTaskCreator");
				$taskType = $actRequest->getParam("modTaskType");
				$taskDescription = $actRequest->getParam("modTaskDescription");
				$taskCreationDate = $actRequest->getParam("modCreationDate");
				$taskFinalizationDate = $actRequest->getParam("modFinalizationDate");
			
				$newTask = array(
					"task_id" => $taskID,
					"user" => $taskCreator,
					"task_type" => $taskType,
					"description" => $taskDescription,
					"creation_date" => $taskCreationDate,
					"finalization_date" => $taskFinalizationDate
					);
				
				break;
			case 'delTask':
				$taskID = $actRequest->getParam([0]);
				
				$newTask = array(
					"task_id" => $taskID,
					);
			
				break;
			default:
				break;
		}
		return $newTask;
	}
				
	//For workaround
	//For future versions -  DO NOT USE 'submit' parameter, use CUSTOM* parameter!
		
	$taskToDo = $actRequest->getParam("taskForm");
	if(isset($taskToDo)){
		switch($taskToDo){
			case 'delete':
			$taskID = $actRequest->getParam('delTaskId');
			
			$newTask = array(
				"task_id" => $taskID,
				);
			break;
			case 'search':
				$newTask = [];
			break;
			case 'filters':
				$newTask = [];
			break;
		}
		return $newTask;
	}
	

}



function selectTasksByDescription(array $arrayTasks, string $textToSearch): array{
	
	$outputTasksArray = [];
	
	foreach($arrayTasks['tasks'] as $task){
		
		$description = $task["description"];
		$textUpper = strtoupper($textToSearch);
		$descriptionUpper = strtoupper($description);
		
		if(str_contains($descriptionUpper, $textUpper )){
			$outputTasksArray['tasks'] [] = $task;
		}
	}
	
	return $outputTasksArray;

}


function selectTasksByFilters(array $arrayTasks, array $filters): array{
	
	$outputTasksArray ['tasks'] = [];
	$outputTasksArrayUsers ['tasks'] = [];
	
	//First by string
	if( empty($filters['user']) == True){
		$outputTasksArrayUsers['tasks'] = $arrayTasks['tasks'];
	
	}else{
		
		foreach($arrayTasks['tasks'] as $task){
			if($task['user'] == $filters['user']){
				$outputTasksArrayUsers['tasks'] [] = $task;
			}
		}
	}
	
	
	if($filters['task_type']=='All'){
		$outputTasksArray['tasks'] = $outputTasksArrayUsers['tasks'];
	}else{

		foreach($outputTasksArrayUsers['tasks'] as $task){

			if($task['task_type'] == $filters['task_type']){
				$outputTasksArray['tasks'] [] = $task;
			}
		}
	}
	
	return $outputTasksArray;
	
}



class IndexController extends ApplicationController{

	//The action is executed before calling the script to display phtml.
	public function indexAction(){
	//The action is executed before calling the script to display phtml.

		//If method is post:
			//If submit = modTask --> Modify task and store in "database"
			//If submit = addTask --> Add a new task
			//If submit = delTask --> Delete task with proper ID and update database
			
			//If taskForm POST Param is delete --> delete the given task
			//If taskForm POST Param is seach --> Search in the tasks
			
		//Afterwards --> Show again list of tasks
		
		//This is the ONLY part of the code that needs to change in all files but model files
		//JSON Persistency
		$appModel = new JSONModel();
		//mySQL Persistency
		//$appModel = new MySQLModel();
		//MongoDB Persistency
		//$appModel = new MongoDBModel();
		
		
		
		$actRequest = new Request();
		if($actRequest->isPost()){
			$submit = $actRequest->getParam("submit");
			
			if(isset($submit)){

				$newTask = getArrayNewTask();
				
				
				switch($submit){
					case 'addTask':
						//Finalization Date after or equal Creation Date:
						try{
							if(strtotime($newTask['creation_date'])>strtotime($newTask['finalization_date'])){
								$newTask['finalization_date'] = $newTask['creation_date'];
							}
						}catch(Exception $e){
							debug_to_console("exception = ", $e);
						}
					
						$appModel->save($newTask);
						break;
						
					case 'modTask':
						//Finalization Date after or equal Creation Date:
						try{
							if(strtotime($newTask['creation_date'])>strtotime($newTask['finalization_date'])){
								$newTask['finalization_date'] = $newTask['creation_date'];
							}
						}catch(Exception $e){
							debug_to_console("exception = ", $e);
						}
						
						$appModel->save($newTask);
						break;
						
					case 'delTask':
						$appModel->delete($newTask['task_id']);
						break;
						
					default:
						echo "Alarm somewhere... An intrusion Try!!!\n";
						break;
					
				}
			}
		}
		
		$taskToDo = $actRequest->getParam("taskForm");
		if(isset($taskToDo)){
			$taskToDo = $actRequest->getParam("taskForm");
			$newTask = getArrayNewTask();
			switch($taskToDo){
				case 'delete':
					$appModel->delete($newTask['task_id']);
					break;
				//For the search bar
				case 'search':
					break;
					
			}
			
		}
		
		//Search with the given input as regex
		if($taskToDo == "search"){
			
			//Fetch all the tasks from the model
			$allTasks = $appModel->fetchAll();
			//Get the input POST parameter with the search
			$textToSearch = $actRequest->getParam("taskSeacherInput");
			//Select the fitting tasks to the search string
			$selectedTasks = selectTasksByDescription($allTasks, $textToSearch);

			//Set the found task to show in the view
			$this->view->__setAssociativeArray($selectedTasks);
		
		
		//Else - normal display - All tasks are displayed
		}else if($taskToDo == "filters"){
		
			//Fetch all the tasks from the model
			$allTasks = $appModel->fetchAll();
			//Get the input POST parameter with the filters
			$filters = array(
				'user' => $actRequest->getParam("filtersUser"),
				'task_type' => $actRequest->getParam("filtersTaskType")
			);
			
			//Select the fitting tasks to the search string
			$selectedTasks = selectTasksByFilters($allTasks, $filters);
			
			//Set the found task to show in the view
			$this->view->__setAssociativeArray($selectedTasks);
			
		}else{
			$this->view->__setAssociativeArray($appModel->fetchAll());
		}
	}

	public function checkAction(){
		
		$this->view->message = "hello from test Index index::index";
		
	}

}


