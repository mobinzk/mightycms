<?php 

	class Users_Controller extends Controller {

		public function __construct(){
			parent::__construct();
		}

		public function users(){

			$data['title']       = 'mightyCMS - Users';
			$data['description'] = '';
			$data['keywords']    = '';

			$data['permissions'] = Mighty::Users()->getPermissions();

			if ($_POST){
				switch ($_POST['action']){
					case 'delete':
							$response = Mighty::Users()->delete($_POST['id']);
							if ($response->ui_alert){
								$data['response'] = $response;
							}
						break;
				}
			}
			
			$this->view->set('data', $data);
			$this->view->set('view', 'users.php');
			$this->view->load();

		}

		
		public function newuser(){

			$data['title']       = 'mightyCMS - Users / New';
			$data['description'] = '';
			$data['keywords']    = '';

			$data['additional'] = array(
				'js' => array(
					'/mightycms/'.STATIC_DIR.'/js/pages'
				)
			);

			$data['permissions'] = Mighty::Users()->getPermissions();
			$data['UserPermissions'] = Mighty::Users()->getPermissions();

			if($_POST) {
				$response = Mighty::Users()->addNewUser();

				if ($response->ui_alert){
					$data['response'] = $response;
					$data['id'] 	  = $response->id;
				}
			}

			// Load the template
			$section = 'default';
			$template = new template('users.xml', 'application/modules/users/static/');
			$data['template'] = $template;
			$data['section'] = $template->getSection($section);
			
			$this->view->set('data', $data);
			$this->view->set('view', 'user.php');
			$this->view->load();

		}

		public function edituser(){

			$data['title']       = 'mightyCMS - Users / Edit';
			$data['description'] = '';
			$data['keywords']    = '';


			$data['id'] 		= $_POST['id'];
			$data['user']		= Mighty::Users()->getUser($data['id']);

			$data['permissions'] = Mighty::Users()->getPermissions();
			$data['UserPermissions'] = Mighty::Users()->getPermissions($data['id']);

			// Load the template
			$section = 'default';
			$template = new template('users.xml', 'application/modules/users/static/');
			$data['template'] = $template;
			$data['section'] = $template->getSection($section);

			if($data['id'])
			$data['breadcrumbs'] = array($data['user']->firstname.' '.$data['user']->surname);


			$data['additional'] = array(
				'js' => array(
					'/mightycms/'.STATIC_DIR.'/js/users'
				)
			);

			if($_POST) {
				$response = Mighty::Users()->editUser();

				$data['user']		= Mighty::Users()->getUser($data['id']);
				$data['permissions'] = Mighty::Users()->getPermissions();
				$data['UserPermissions'] = Mighty::Users()->getPermissions($data['id']);

				if ($response->ui_alert){
					$data['response'] = $response;
				}
			}
			
			$this->view->set('data', $data);
			$this->view->set('view', 'user.php');
			$this->view->load();

		}

		
		public function controller() {
			$url = ($this->params->url) ? $this->params->url : '';

			switch ($url) {
				case 'new':
					$this->newuser();
					break;
				case 'edit':
					$this->edituser();
					break;

				default:
					$this->users();
					break;
			}
		}



	}

?>