<?php

/**
 * Used for setting up the routing in the system
 */
class Router
{
	/**
	 * Executes the system routing
	 * @throws Exception
	 */
	public function execute($routes)
	{
		// tries to find the route and run the given action on the controller
		try {
			// the controller and action to execute
			$controller = null;
			$action = null;
			
			// tries to find a simple route
			$routeFound = $this->_getSimpleRoute($routes, $controller, $action);
			
			if (!$routeFound) {
				// tries to find the a matching "parameter route"
				$routeFound = $this->_getParameterRoute($routes, $controller, $action);
			}
			
			// no route found, throw an exception to run the error controller
			if (!$routeFound || $controller == null || $action == null) {
				throw new Exception('no route added for ' . $_SERVER['REQUEST_URI']);
			}
			else {
				// executes the action on the controller
				$controller->execute($action);
			}
		}
		catch(Exception $exception) {
			// runs the error controller
			$controller = new ErrorController();
			//Original above
			
			//Start workaround...
			//$controller = new Controller();
			//$controller = $this->_initializeController("error");
			// tries to find a simple route
			//$routeFound = $this->_getSimpleRoute($routes, "error", "");
			//End workaround
			
			$controller->setException($exception);
			$controller->execute('error');
		}
	}
	
	/**
	 * Tests if a route has parameters
	 * @param string $route the route (uri) to test
	 * @return boolean
	 */
	public function hasParameters($route)
	{
		// preg_match performs a regular expression match
		// It returns 1 iff the pattern matches a given subject and 0 if it does not (returns a "boolean")
		// the pattern matches first a char "/" with a followed char ":" and letters from a to z from one to infinite times
		// The parentesis are in order to create a group
		// Therefore (Correct me if I am wrong):
		// given a route it has parameters if the string "/:" is somewhere in the route name followed by the parameter name (without numbers)
		// 
		return preg_match('/(\/:[a-z]+)/', $route);
	}
	
	/**
	 * Fetches the current URI called
	 * @return string the URI called
	 */
	// URI -> Uniform Resource Identifier -> A unique sequence of characters that identifies a logical or physical resource used by web technologies.
	
	protected function _getUri()
	{
		$uri = explode('?',$_SERVER['REQUEST_URI']);
		$uri = $uri[0];
		$uri = substr($uri, strlen(WEB_ROOT));
		
		return $uri;
	}
	
	/**
	 * Tries to find a matching simple route
	 * @param array $routes the list of routes in the system
	 * @param Controller $controller the controller to use (sent as reference)
	 * @param string $action the action to execute (sent as reference)
	 * @return boolean
	 */
	 // Like C the pointer address is used in order to return the two last input parameters
	protected function _getSimpleRoute($routes, &$controller, &$action)
	{
		// fetches the URI
		$uri = $this->_getUri();
		
		// if the route isn't defined, try to add a trailing slash
		//Try to define the routeFound in routes.php a given
		if (isset($routes[$uri])) {
			$routeFound = $routes[$uri];
		}
		//Try to define the routeFound in routes.php WITH the slash
		else if(isset($routes[$uri . '/'])) {
			$routeFound = $routes[$uri . '/'];
		}
		//Try to define the routeFound in routes.php without the slash
		else {
			$uri = substr($uri, 0, -1);
			// fetches the current route
			$routeFound = isset($routes[$uri]) ? $routes[$uri] : false;
		}
		
		// if a matching route was found
		if ($routeFound) {
			//explode function returns a list of substrings of #routeFound delimited by the separator character "#"
			// As in a route there should be only one "#" the two resulting strings are stored in $name and $action
			list($name, $action) = explode('#', $routeFound);
		
			// initializes the controller
			//By giving the name and the file and classes structures and name definition must be done in order to follow the naming squema:
			//Exemple:
			//$name = SectionOne
			//Controller file Name must be named SectionOneController.php
			//Controller class must be named SectionOneController and must extend from Controller Main Abstract
			$controller = $this->_initializeController($name);
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * Tries to find a matching parameter route
	 * @param array $routes the list of routes in the system
	 * @param Controller $controller the controller to use (sent as reference)
	 * @param string $action the action to execute (sent as reference)
	 * @return boolean
	 */
	// Ask teacher:
	// What is a parameter route
	//There is not a "?" before the parameter starts? - Do not see it anywhere...
	// Okaaaay... the function _getUri already gives just the parameter string part of the url...
	// I do not know if _getUri is an appropiate name for the function looking online...
	// If I am not wrong after a 5 minutes google search... Which could most surely be... The part of an URL or URI that assigns values to
	// specified parameters is called/named "query string" (See wikipedia)
	// Okay I was wrong and the function is called right, as it selects the first split of the string cointaining just the URI, instead of the 
	// "query string"... My bad ^^'
	protected function _getParameterRoute($routes, &$controller, &$action)
	{
		// fetches the URI
		//in case google.com/search?s=blabla
		// uri is google.com/search
		$uri = $this->_getUri();
		
		// testing routes with parameters
		foreach ($routes as $route => $path) {
			// The way below is to loop in an associative array.
			// $route is the first value and $path is the associated value
			// Therefore hasParameters() function used below performs the  test to the firs value
			// That concludes in:
			// If it is needed to create a parameter routes in the routes.php file it must be written so:
			// '/test/:parameterName' => 'test#index'
			
			
			// Remember .= appends the following string (notice dot before equal sign)
			
			if ($this->hasParameters($route)) {
				//Why are we spliting the string by a ":" (two-dots) delimiter
				//ahh yeah... it is the route of in the system... not the URI route or the Web URL...... OMG what a job...
				$uriParts = explode('/:', $route);
					
				// /^ asserts position at the start of the line/string in this case
				$pattern = '/^';
				//$pattern .= '\\'.($uriParts[0] == '' ? '/' : $uriParts[0]);
				
				
				// If there is nothing before  the first ":"				
				if ($uriParts[0] == '') {
					$pattern .= '\\/';
				}
				else {
					//else scape the / for \\/ in order to select really and slash
					$pattern .= str_replace('/', '\\/', $uriParts[0]);
				}
					
				
				foreach (range(1, count($uriParts)-1) as $index) {
					$pattern .= '\/([a-zA-Z0-9]+)';
				}
				
				// now also handles ending slashes!
				$pattern .= '[\/]{0,1}$/';
				//Okay... a most probably wrong explanation...
				//The code here tries to check for the parameters route in the system specified in routes.php
				// Also it will store the parameters using the function addNamedParameter giving the uriParts, with the parameter name
				// and the namedParameter with the parameter value
				//It means it converts something like this:
				// /test/:paramOne/:paramTwo:/paramThree
				// /test:paramOne/paramTwo/paramThree
				
				// pattern = /^\/test\/([a-zA-Z0-9]+)\/([a-zA-Z0-9]+)\/([a-zA-Z0-9]+)[\/]{0,1}$/
				//URI example:
				// google.com/test?paramOne=blabla&paramTwo=bloblo&paramThree=bleble
				
				
				
				$namedParameters = array();
				//Important URI!!! Not PATH neither ROUTE!!!
				//If I am not wrong the Uri does not contine the parameters... Will it match?
				
				$match = preg_match($pattern, $uri, $namedParameters);
				// if the route matches
				if ($match) {
					
					list($name, $action) = explode('#', $path);
					//Therefore the path does not contain the parameters!!
					
					
					// initializes the controller
					$controller = $this->_initializeController($name);
		
					// adds the named parameters to the controller
					foreach (range(1, count($namedParameters)-1) as $index) {
						$controller->addNamedParameter(
								//Stored in an associative array key => value
								
								//key
								$uriParts[$index],
								//value
								$namedParameters[$index]
						);
					}
					
					return true;
				}
			}
		}
		
		return false;
	}
	
	/**
	 * Initializes the given controller
	 * @param string $name the name of the controller
	 * @return mixed null if error, else a controller
	 */
	protected function _initializeController($name)
	{
		// initializes the controller
		// ucfirst function makes a string first character upper case.
		$controller = ucfirst($name) . 'Controller';
		// constructs the controller
		// The controller will have the name of the URI name plus "Controller" appended at the end
		// Therefore the controller file for a /section/ will be named "SectionController.php" and the class must be named SectionController
		return new $controller();
	}
}
