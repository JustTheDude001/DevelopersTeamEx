<?php 


function getParamSessionAssociativeArray($array, $counter, $ass_id, $obj){
	$task = $obj->__getAssociativeArray()['tasks'];
	
	if(isset(${$array}[$counter][$ass_id])){
		return ${$array}[$counter][$ass_id];
	}else{
		return Null;
	}
	
}

function addListTaskIntoHTML($obj){
	
	$tasks = $obj->__getAssociativeArray();
	
	if(!isset($tasks)){
		return;
	}
	
	$counter = 0;
	foreach($tasks as $task){
		for($i=1;$i<=sizeof($task);$i++){
			
			//Variables to print:
			$taskId = '$task[' . $counter .  "][\'task_id\']";
			debug_to_console($taskId);
			$array = 'task';
			
			//ToDo Associative array and reduce to a loop each row but the two at the end.
			echo "<tr class=\"rowTasksList\">
				<td class=\"taskId\">
					" . getParamSessionAssociativeArray($array, $counter, 'task_id', $obj) .  "
				</td>
				<td class=\"taskCreator\">
					" . getParamSessionAssociativeArray($array, $counter, 'user', $obj) . "
				</td>
				<td class=\"taskType\">
					" . getParamSessionAssociativeArray($array, $counter, 'task_type', $obj)  . "
				</td>
				<td class=\"taskDescription\">
					" . getParamSessionAssociativeArray($array, $counter, 'description', $obj) . "
				</td>
				<td class=\"taskCreationDate\">
					" .  getParamSessionAssociativeArray($array, $counter, 'creation_date', $obj) . "
				</td>
				<td class=\"taskFinalizationDate\">
					" . getParamSessionAssociativeArray($array, $counter, 'finalization_date', $obj) . "
				</td>
				<td class=\"divMod\">
					<button class=\"buttonMod\" onclick=\"modPopUpFunction()\">
					<a href=\"javascript:;\"><i class=\"iconMod\" style=\"" . "background-image: url('" . get_png("modify-i.png") . "')\" ?>\"></i></a>
					</button>
				</td>
				<td class=\"divDel\" >
					<button class=\"buttonDel\" onclick=\"delTaskFunction()\">
					<a href=\"javascript:;\"><i class=\"iconMod\" style=\"" . "background-image: url('" . get_png("delete-i.png") . "')\" ?>\"></i></a>
					</button>
				</td>				
			</tr>";
			
			$counter++;
		}
	} 
}

function addEmptyStateTable($obj){
	
	//$tasks = $_SESSION['tasks'];
	$tasks = $obj->__getAssociativeArray();
	if(!isset($tasks)){
		return;
	}
	
	if(isset($tasks['tasks'])){
		$countElements = count($tasks['tasks']);
		//If there are no elements to print -- No tasks stored add/echo the empty state:
		if($countElements == 0){
			//echo "I am an empty state";
			
			echo'
			<div id="tasksEmptyState"> 
				<text> Ooouuuhhh.... <br>
				Sadly there are not tasks stored to show....<br>
				Or maybe the connection with the persistency is not working properly... <br>
				Who knows...<br>
				Check adding a new task and you will! ^^ <br></text>
				
				<button id="addTaskButtonEmptyState" onclick="addTaskFunction()">
				<a href="javascript:;"><i class="iconMod" style= "background-image: url(' ."'". get_png("addTaskPlus.png") ."'".') "></i></a>
				</button>
				
			</div>';
			
			
		}
	}
	

}
