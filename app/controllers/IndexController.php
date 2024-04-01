<?php

/* OKAY - SOME ORDER AND REFACTORING
 * 1. Formulars All Names Equal [DONE] ;)
 * 2. No Switch By Submit -- All by field named "taskForm"
 * 
 */


function getArrayNewTask(){
	$actRequest = new Request();
	$taskToDo = $actRequest->getParam("taskForm");
	
	if(isset($taskToDo)){
		switch($taskToDo){
			
			case 'add':
			case 'modify':
				$taskID = $actRequest->getParam("taskId");
				$taskCreator = $actRequest->getParam("taskCreator");
				$taskType = $actRequest->getParam("taskType");
				$taskDescription = $actRequest->getParam("taskDescription");
				$taskCreationDate = $actRequest->getParam("creationDate");
				$taskFinalizationDate = $actRequest->getParam("finalizationDate");
				break;
				
			case 'delete':
				$taskID = $actRequest->getParam('taskId');
			break;
			
			case 'search':
			case 'filters':
				$newTask = [];
			break;
			
			default:
				//Log Into Server Intrussion Try
				return null;
				break;
		}
		//Create return variable newTasks:
		$newTask = array(
			"task_id" => $taskID ?? "",
			"user" => $taskCreator ?? "",
			"task_type" => $taskType ?? "",
			"description" => $taskDescription ?? "",
			"creation_date" => $taskCreationDate ?? "",
			"finalization_date" => $taskFinalizationDate ?? "",
			);

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

		//This is the ONLY part of the code that needs to change in all files but model files
		//JSON Persistency
		$appModel = new JSONModel();
		//mySQL Persistency
		$appModel = new MySQLModel();
		//MongoDB Persistency
		//$appModel = new MongoDBModel();
		
		
		
		$actRequest = new Request();
		if($actRequest->isPost()){
			$taskToDo = $actRequest->getParam("taskForm");
			
			if(isset($taskToDo)){

				$taskData = getArrayNewTask();
				
				switch($taskToDo){
					case 'add':
						//Finalization Date after or equal Creation Date:
						try{
							if(strtotime($taskData['creation_date'])>strtotime($taskData['finalization_date'])){
								$taskData['finalization_date'] = $taskData['creation_date'];
							}
						}catch(Exception $e){
							debug_to_console("exception = ", $e);
						}
					
						$appModel->save($taskData);
						
						//Return the view with all tasks
						$this->view->__setAssociativeArray($appModel->fetchAll());
						break;
						
					case 'modify':
						//Finalization Date after or equal Creation Date:
						try{
							if(strtotime($taskData['creation_date'])>strtotime($taskData['finalization_date'])){
								$taskData['finalization_date'] = $taskData['creation_date'];
							}
						}catch(Exception $e){
							debug_to_console("exception = ", $e);
						}
						
						$appModel->save($taskData);
						
						//Return the view with all tasks
						$this->view->__setAssociativeArray($appModel->fetchAll());
						
						break;
						
					case 'delete':
						$appModel->delete($taskData['task_id']);
						
						//Return the view with all tasks
						$this->view->__setAssociativeArray($appModel->fetchAll());
						
					break;
					
					//For the search bar
					case 'search':
						//Fetch all the tasks from the model
						$allTasks = $appModel->fetchAll();
						//Get the input POST parameter with the search
						$textToSearch = $actRequest->getParam("taskSeacherInput");
						//Select the fitting tasks to the search string
						$selectedTasks = selectTasksByDescription($allTasks, $textToSearch);
						//Set the found task to show in the view
						$this->view->__setAssociativeArray($selectedTasks);
						
						break;
					
					case 'filters':
						//Fetch all the tasks from the model
						$allTasks = $appModel->fetchAll();
						//Get the input POST parameter with the filters
						$filters = array(
							'user' => $actRequest->getParam("filtersUser"),
							'task_type' => $actRequest->getParam("filtersTaskType")
						);
						//Select the fitting tasks to the filters parameters
						$selectedTasks = selectTasksByFilters($allTasks, $filters);
						
						//Set the found task to show in the view
						$this->view->__setAssociativeArray($selectedTasks);
						
						break;
						
					default:
						echo "Alarm somewhere... An intrusion Try!!!\n";
						//$this->view->__setAssociativeArray($appModel->fetchAll());
						break;
					
				}
			}
		
			
		}else{
		
			$this->view->__setAssociativeArray($appModel->fetchAll());
		}
		
		
	}

	public function checkAction(){
		
		$this->view->message = "hello from test Index index::index";
		
	}

}


