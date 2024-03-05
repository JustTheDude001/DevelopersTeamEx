<?php

function get_stylesheet(string $stylesheetName){
	$stylesPath = "/stylesheets/";
	
	if(file_exists(ROOT_PATH ."/web". $stylesPath . $stylesheetName)){
		//return ROOT_PATH . $stylesPath . $stylesheetName;
		//return $stylesPath . $stylesheetName;
		return WEB_ROOT . $stylesPath . $stylesheetName;
	}else{
		return "";
	}
}

function get_javascript(string $scriptName){
	$javascriptsPath = "/javascripts/";
	
	if(file_exists(ROOT_PATH  ."/web". $javascriptsPath . $scriptName)){
		//return ROOT_PATH . $stylesPath . $stylesheetName;
		//return $stylesPath . $stylesheetName;
		return WEB_ROOT . $javascriptsPath . $scriptName;
	}else{
		return "";
	}
}

function get_png(string $pngName){
	$imagesPath = "/images/";
	
	if(file_exists(ROOT_PATH  ."/web". $imagesPath . $pngName)){
		//return ROOT_PATH . $stylesPath . $stylesheetName;
		//return $stylesPath . $stylesheetName;
		return WEB_ROOT . $imagesPath . $pngName;
	}else{
		return "";
	}
}

function get_png_ds(string $pngName){
	$imagesPath = "/images/";
	
	if(file_exists(ROOT_PATH  ."/web". $imagesPath . $pngName)){
		//return ROOT_PATH . $stylesPath . $stylesheetName;
		//return $stylesPath . $stylesheetName;
		$outputOriginal = WEB_ROOT . $imagesPath . $pngName;
		$outputStr = str_replace('/', '\/', $outputOriginal);
		return $outputStr;
	}else{
		return "";
	}
}


function debug_to_console($data) {
    $output = $data;
    if (is_array($output))
        $output = implode(',', $output);

    echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
}

function debug_to_console_4($data) {
    $output = $data;
    if (is_array($output))
        $output = implode(',', $output);

    echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
}


function setNullIfNotExisting($data = array()){
	
	if(!isset($data['task_id'])){
		$data['task_id'] = "Null";
	}
	
	if(!isset($data['user'])){
		$data['user'] = "Null";
	}
	if(!isset($data['task_type'])){
		$data['task_type'] = "Pending";
	}
	if(!isset($data['description'])){
		$data['description'] = "Null";
	}
	if(!isset($data['creation_date'])  or  empty($data['creation_date'])){
		//$data['creation_date'] = strtotime(date("Y-m-D H:i:S"));
		$data['creation_date'] = date("Y-m-d H:i:s");
	}
	//$data['creation_date'] = date("Y-m-d H:i:s");
	debug_to_console("Date...");
	debug_to_console($data['creation_date']);
	//$data['creation_date'] = strtotime(date("Y-m-D H:i"));
	//$data['creation_date'] = time();
	if(!isset($data['finalization_date']) or empty($data['finalization_date'])){
		//$data['finalization_date'] = NULL;
		//$data['finalization_date'] = "NULL";
		$data['finalization_date'] = Null;
		//$data['finalization_date'] = date("Y-m-d H:i:s");
		
	}
	
	//ToDo 
	//Because of not allowed NULL date and time in mysql
	//Achieve somehow to store null in date an time in mysql
	
	
	return $data;
}
