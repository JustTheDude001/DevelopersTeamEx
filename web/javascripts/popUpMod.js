function modPopUpFunction(){
	console.log("Hiiiii!!");
	//var taskId = event.currentTarget.parentElement.parentElement.firstElementChild.innerText;
	
	//var parentElem = event.currentTarget.parentElement.parentElement;
	var taskId = event.currentTarget.parentElement.parentElement.getElementsByClassName("taskId")[0].innerText;
	var creatorTr = event.currentTarget.parentElement.parentElement.getElementsByClassName("taskCreator");
	var taskCreator = event.currentTarget.parentElement.parentElement.getElementsByClassName("taskCreator")[0].innerText;
	var taskType = event.currentTarget.parentElement.parentElement.getElementsByClassName("taskType")[0].innerText;
	var taskDescription = event.currentTarget.parentElement.parentElement.getElementsByClassName("taskDescription")[0].innerText;
	var taskCreationDate = event.currentTarget.parentElement.parentElement.getElementsByClassName("taskCreationDate")[0].innerText;
	var taskFinalizationDate = event.currentTarget.parentElement.parentElement.getElementsByClassName("taskFinalizationDate")[0].innerText;
	
	//console.log(event.currentTarget.parentElement.parentElement.firstElementChild.innerText);
	console.log("taskId = ", taskId);
	//console.log("parentElem = ", parentElem);
	console.log("creatorTr = ", creatorTr);
	console.log("taskCreator = ", taskCreator);
	console.log("taskType = ", taskType);
	console.log("taskDescription = ", taskDescription);
	console.log("taskCreationDate = ", taskCreationDate);
	console.log("taskFinalizationDate = ", taskFinalizationDate);
	
	
	//
	
	
	//Modify input values:
	document.getElementById("modTaskId").value = taskId;
	document.getElementById("modTaskCreator").value = taskCreator;
	document.getElementById("modTaskType").value = taskType;
	document.getElementById("modTaskDescription").value = taskDescription;
	document.getElementById("modCreationDate").value = taskCreationDate;
	document.getElementById("modFinalizationDate").value = taskFinalizationDate;
	
	document.getElementById("modFormDiv").style.display = "block";
	
	
	console.log( JSON.stringify({
			task_id: taskId,
			submit: delTask,
		}));
	
}

function closeFormPopUpFunction(){
	document.getElementById("modFormDiv").style.display = "none";
}

function delTaskFunctionJavascriptOnly(){
	var taskId = event.currentTarget.parentElement.parentElement.getElementsByClassName("taskId")[0].innerText;
	//Possible deployment ERROR - TODO Change second line below for first line:
	//var myWebURL = window.location.href;
	var myWebURL = window.location.host + window.location.pathname;
	console.log("Delete taskId = ", taskId);
	console.log(window.location.href);
	console.log(myWebURL);
	console.log( JSON.stringify({
			task_id: taskId,
			submit: "delTask",
		}));
	//Send post request:
	
	
	fetch(myWebURL, {
		method: "POST",
		body: JSON.stringify({
			task_id: taskId,
			submit: "delTask",
		})
		
	}).then(() => {
		window.location.reload();
	});
	
	
}


function delTaskFunction(){
	var taskId = event.currentTarget.parentElement.parentElement.getElementsByClassName("taskId")[0].innerText;
	//Modify input values:
	document.getElementById("delTaskId").value = taskId;
	let form = document.getElementById("delForm");
	//form.submit();
	var textConfirm = "Are you sure you want to delete the task with id = " + taskId + " ? ";
	if(confirm(textConfirm)){
		document.getElementById("delForm").submit();
	}else{
		return;
	}

}



function addTaskFunction(){
	
	//Add creation_date as system date

	var date = new Date();
	var dateStr = date.getFullYear().toString() + '-' + (date.getMonth() + 1).toString().padStart(2,0)
		+ '-' + date.getDate().toString().padStart(2,0);
	var timeStr = date.getHours().toString() + ':' + date.getMinutes().toString()
	
	document.getElementById("addCreationDate").value = dateStr + 'T' + timeStr;
	
	document.getElementById("addFormDiv").style.display = "block";
}

function closeAddFormPopUpFunction(){
	document.getElementById("addFormDiv").style.display = "none";
}


function filtersFunction(){
	document.getElementById("filtersFormDiv").style.display = "block";
}

function filtersHideFunction(){
	document.getElementById("filtersFormDiv").style.display = "none";
}



if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }

