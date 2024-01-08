<?php 


function getParamSession($param){
	if(isset($$param)){
		return $$param;
	}else{
		return Null;
	}
}


function addListTaskIntoHTML(){
	//Okay... how to separate model with view...
	//Storing in some field of view object?
	$tasks = $_SESSION['tasks'];
	
	if(!isset($tasks)){
		return;
	}
	
	$counter = 0;
	foreach($tasks as $task){
		for($i=1;$i<=sizeof($task);$i++){
			echo "<tr class=\"rowTasksList\">
				<td class=\"taskId\">
					" . $task[$counter]['task_id'] . "
				</td>
				<td class=\"taskCreator\">
					" . $task[$counter]['user'] . "
				</td>
				<td class=\"taskType\">
					" . getParamSession('$task[$counter][\'task_type\']') . "
				</td>
				<td class=\"taskDescription\">
					" . $task[$counter]['description'] . "
				</td>
				<td class=\"taskCreationDate\">
					" . $task[$counter]['creation_date'] . "
				</td>
				<td class=\"taskFinalizationDate\">
					" . $task[$counter]['finalization_date'] . "
				</td>
				<td class=\"divMod\">
					<button class=\"buttonMod\" onclick=\"modPopUpFunction()\">
					<a href=\"javascript:;\"><i class=\"iconMod\" style=\"" . "background-image: url('" . get_png("modify-i.png") . "')\" ?>\"></i></a>
					</button>
				</td>
				<td class=\"divDel\" >
					<button class=\"buttonDel\" onclick=\"delTaskFunction()\">
					<a href=\"\"><i class=\"iconMod\" style=\"" . "background-image: url('" . get_png("delete-i.png") . "')\" ?>\"></i></a>
					</button>
				</td>				
			</tr>";
			
			$counter++;
		}
	} 
}
