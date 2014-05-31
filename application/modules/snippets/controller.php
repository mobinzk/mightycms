<?php 

	class Snippets_Controller extends Controller {

		public function __construct(){
			parent::__construct();
		}

		public function snippets() {

			$data['title']       = 'mightyCMS - Snippets';
			$data['description'] = '';
			$data['keywords']    = '';

			$data['permissions'] = Mighty::Users()->getPermissions();

			if ($_POST){
				switch ($_POST['action']){
					case 'delete':
					break;
				}
			}

			$data['breadcrumbs'] = array('settings');

			$data['snippets'] = Mighty::Snippets()->getAll();
			
			$this->view->set('data', $data);
			$this->view->set('view', 'snippets.php');
			$this->view->load();

		}

		public function editSnippets(){

			$data['title']       = 'mightyCMS - Blog / Edit Snippets';
			$data['description'] = '';
			$data['keywords']    = '';

			$data['id'] 		= $_POST['id'];
			$snippets 			= Mighty::Snippets()->getSnippet($data['id']);
			$data['snippets']	= $snippets;
			$page_template 		= $snippets->template;

			$template = new template('snippets.'.$page_template.'.xml');

			$data['sections'] 	= $template->getSections();
			$data['template'] 	= $template;


			$data['default_section'] = $data['sections'][0]->name;

			if($data['id'])
			$data['breadcrumbs'] = array($snippets->name);


			if($_POST) {
				$response = Mighty::Snippets()->editSnippet();
				$data['snippets'] = Mighty::Snippets()->getSnippet($data['id']);

				if ($response->ui_alert){
					$data['response'] = $response;
				}
			}
			
			$this->view->set('data', $data);
			$this->view->set('view', 'snippet.php');
			$this->view->load();

		}

		
		public function controller() {
			$url = ($this->params->url) ? $this->params->url : '';

			switch ($url) {
				case 'edit':
					$this->editSnippets();
					break;

				default:
					$this->snippets();
					break;
			}
		}



	}

?>