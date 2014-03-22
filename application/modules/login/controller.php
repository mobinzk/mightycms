<?php 

	class Login_Controller extends Controller {

		public function __construct(){
			parent::__construct();
		}

		public function login(){

			$data['title']       = 'mightyCMS - Login';
			$data['description'] = '';
			$data['keywords']    = '';

			// redirect if logged in
			if(Mighty::Auth()->userId()){
				header("Location: /mightycms/dashboard");
				die();
			}

			if($_POST) {
				extract($_POST);
				$response = Mighty::Auth()->login($username, $password, $remember);

				if (!$response->result){
			        $data['response'] = $response->message;
			    } else {
			    	header("Location: /mightycms/dashboard");
			    	die();
			    }
			}

			if (file_exists(MIGHTY_CONFIG_DIR.'/templates/images/logo.png')){
				$data['logoURL'] = '/mighty_config/templates/images/logo.png';
			} else {
				$data['logoURL'] = '/mightycms/'.STATIC_DIR.'/images/logo.png';
			}
			
			$this->view->set('data', $data);
			$this->view->set('view', 'login.php');
			$this->view->load();

		}

		public function forgotten(){

			$data['title']       = 'mightyCMS - Forgotten Password';
			$data['description'] = '';
			$data['keywords']    = '';

			if (file_exists(MIGHTY_CONFIG_DIR.'/templates/images/logo.png')){
				$data['logoURL'] = '/mighty_config/templates/images/logo.png';
			} else {
				$data['logoURL'] = '/mightycms/'.STATIC_DIR.'/images/logo.png';
			}

			if($_POST) {
				extract($_POST);
				$response = Mighty::Auth()->forgotten($email);
		        $data['response'] = $response;
			}

			$this->view->set('data', $data);
			$this->view->set('view', 'forgotten.php');
			$this->view->load();
		}

		public function logout() {
			
			$response = Mighty::Auth()->logout();
			if ($response){
			    header("Location: /mightycms");
			    die();
			}

		}


	}

?>