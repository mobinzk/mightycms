<?php 

	class Credits_Controller extends Controller {

		public function __construct(){
			parent::__construct();
		}

		public function credits(){

			$data['title']       = 'mightyCMS - Credits';
			$data['description'] = '';
			$data['keywords']    = '';

			$this->view->set('data', $data);
			$this->view->set('view', 'credits.php');
			$this->view->load();
		}

	}

?>