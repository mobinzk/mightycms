<?php 

	class Router {

		public $request;
		public $routes;
		public static $route;
		public $config;

		public function __construct($request, $config){
			$this->config = $config;
			$this->request = $request;
			$this->buildRoutes($this->config->routes);
			$this->executeRequest();
		}

		public function executeRequest(){

			foreach ($this->routes as $key => $route){

				if ($this->request == '' && $route->regex == '/'){
					$this->loadModule($route->options);
					return;
				} else {

					if ($route->regex != '/'){
						if (preg_match('/^'.$route->regex.'$/si', $this->request, $params)){
							$this->loadModule($route->options, $this->tidyParams($params));
							return;
						} 
					}

				}

			} 

			Http_Header::status(404);

			if ($this->config->debug == true){
				throw new Exception('Route not found.');
			}

		}

		public function loadModule($options, $params=''){

			$controller = ucfirst($options->module).'_Controller';

			if (!isset($options->action)){

				$params = (array) $params;

				if (count($params) < 1){
					$params = explode('/',$this->request);
				}

				$options->action = str_replace('-', '_', end($params));

			}

			self::$route = (object) array(
				'module' => $options->module,
				'params' => $params,
				'action' => $options->action
			);

			$class = new $controller;
			$action = self::$route->action;

			if (method_exists($class, $action)){
				
				$class->$action();
			
			} else {
				
				Http_Header::status(404);

				if ($this->config->debug == true){
					throw new Exception('Method not found.');
				}

			}

		}

		public function buildRoutes($routes){

			$this->routes = $routes;

			foreach ($this->routes as $key => $route){

				if ($route->route != '/'){
					$regex = $this->buildRegex($route->route);
					$regex = preg_replace('/\//si', '\/', $regex);
					$regex = substr($regex, 2, strlen($regex) - 2);
				} else {
					$regex = $route->route;
				}

				$this->routes[$key] = (object) array(
					'regex' => $regex,
					'options' => (object) $route->options
				);

			}

		}

		public function buildRegex($route){

			$route = preg_replace(
				array(
					'/<:([0-9a-zA-Z-_]+)>/si',
					'/<#([0-9a-zA-Z-_]+)>/si'
				), 
				array(
					'(?P<$1>[0-9a-zA-Z-_]+)',
					'(?P<$1>[0-9]+)'
				), 
				$route
			);

			$route = preg_replace('/<:([0-9a-zA-Z-_]+)\((.*?)\)>/si', "(?P<$1>$2)", $route);

			return $route;

		}

		public function tidyParams($params){

			foreach ($params as $k => $v){
				if (is_numeric($k)){
					unset($params[$k]);
				}
			}
			
			return (object) $params;
			
		}

	}

?>