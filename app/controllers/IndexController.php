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
		
	$taskToDo = $actRequest->getParam("taskForm");
	if(isset($taskToDo)){
		switch($taskToDo){
			case 'delete':
			$taskID = $actRequest->getParam('delTaskId');
			
			$newTask = array(
				"task_id" => $taskID,
				);
			break;
		}
		return $newTask;
	}
	

}


class IndexController extends ApplicationController{

	//The action is executed before calling the script to display phtml.
	public function indexAction(){
		
		//No need of calling anything in the index
		//It just needs to display the entire list of tasks
		//$this->view->message = "hello from test Index index::index";
		
		//Okay... need to do some stuff....
		//If method is post:
			//If submit = modTask --> Modify task and store in "database"
			//If submit = addTask --> Add a new task
			//If submit = delTask --> Delete task with proper ID and update database
			
		//Afterwards --> Show again list of tasks
		
		//IF _POST is null just retrieving from javascripts...
		
		//if(!isset($_POST)){
		if(empty($_POST)){
			//Thought about doing below but too many changes must be done...
			//$_SESSION['POST_JV'] = json_decode(file_get_contents('php://input', true);
			//NOT RECOMMENDED - But modifying variable $_POST:
			//debug_to_console("Setting Post...");
			//$_POST = file_get_contents('php://input');
			
			//Problems.... always probleeemmss...
			//Check if next line needed:
			//ini_set("allow_url_fopen", true);
			
			$_POST = json_decode(file_get_contents('php://input'), true);
			
		}
		
		//This is the ONLY part of the code that needs to change in all files but model files
		//JSON Persistency
		$appModel = new JSONModel();
		//mySQL Persistency
		//$appModel = new MySQLModel();
		//MongoDB Persistency
		//$appModel = new MongoDBModel();
		
		$actRequest = new Request();
		$actionDone = False;
		if($actRequest->isPost()){
			$submit = $actRequest->getParam("submit");
			
			if(isset($submit)){
			
			$actionDone = True;
			$newTask = getArrayNewTask();
			
			
			switch($submit){
				case 'addTask':
					$appModel->save($newTask);
					debug_to_console("Adding task...");
					break;
				case 'modTask':
				//Yeah the same as addTask.. i am so original... (can reduce a few lines code, not done because of lazyness)
					$appModel->save($newTask);
					debug_to_console("Modifying task...");
					break;
				case 'delTask':
					$appModel->delete($newTask['task_id']);
					debug_to_console("Deleting task...");
					break;
				default:
					echo "Alarm somewhere... An intrusion Try!!!\n";
					debug_to_console("Alarming task...");
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
					debug_to_console("Deleting task...");
					break;
			}
			
		}
		
		
		//Okay while using javascript _POST is not updated... therefore must use something as:
		//$_POST = json_decode(file_get_contents('php://input'), true);
		
		/*
		//Okay done above easier
		if($actionDone == False){
			//Thought about doing below but too many changes must be done...
			//$_SESSION['POST_JV'] = json_decode(file_get_contents('php://input', true);
			//NOT RECOMMENDED - But modifying variable $_POST:
			$_POST = json_decode(file_get_contents('php://input'), true);
			
		}*/
		
		//Show task list
		//$actView = new View();
		//$actView-->__setAssociativeArray($appModel->fetchAll());
		//$actView->
		//$_SESSION['tasks'] = $appModel->fetchAll();
		//debug_to_console_3($_SESSION['tasks'] );
		$this->view->__setAssociativeArray($appModel->fetchAll());
	}

	public function checkAction(){
		
		$this->view->message = "hello from test Index index::index";
		
	}

}


