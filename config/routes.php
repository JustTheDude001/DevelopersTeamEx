<?php 

/**
 * Used to define the routes in the system.
 * 
 * A route should be defined with a key matching the URL and an
 * controller#action-to-call method. E.g.:
 * 
 * '/' => 'index#index',
 * '/calendar' => 'calendar#index'
 */
$routes = array(
	'/test' => 'test#index',
	'/' => 'index#index',
	'/web' =>'index#index',
	'/web/' =>'index#index'
	// Example for paramaters (if I am not wrong, but I am)
	//'/test:paramOne:paramTwo:paramThree' => 'test#index'
);
