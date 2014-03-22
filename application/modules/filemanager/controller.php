<?php 

	class Filemanager_Controller extends Controller {

		public function __construct(){
			parent::__construct();
		}

		public function filemanager() {

			$data['title']       = 'mightyCMS - File Manager';
			$data['description'] = '';
			$data['keywords']    = '';

			$data['permissions'] = Mighty::Users()->getPermissions();

			if ($_POST){
				switch ($_POST['action']){
					case 'delete':
						$data['response'] = Mighty::Filemanager()->delete();
					break;
					case 'upload':
						$data['response'] = Mighty::Filemanager()->upload();
					break;
				}
			}

			$data['files'] = Mighty::Filemanager()->getAll();
			
			$this->view->set('data', $data);
			$this->view->set('view', 'filemanager.php');
			$this->view->load();

		}


	}

?>