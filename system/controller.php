<?php 

	class Controller {

		public $params;
		public $view;

		public function __construct(){
			if (!isset($_SESSION['uid']) && $_SERVER['REQUEST_URI'] != '/mightycms/' && $_SERVER['REQUEST_URI'] != '/mightycms/forgotten-password'){				
				header("Location: /mightycms");
				die();
			}
			
			$this->params = Router::$route->params;
			$this->view = new View;
		}

	}

?>