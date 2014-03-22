<?php 

	class Dashboard_Controller extends Controller {

		public function __construct(){
			parent::__construct();
		}

		public function dashboard(){

			$data['title']       = 'mightyCMS - Dashboard';
			$data['description'] = '';
			$data['keywords']    = '';
			
			$this->view->set('data', $data);
			$this->view->set('view', 'dashboard.php');
			$this->view->load();

		}


	}

?>