<?php 

	class Pages_Controller extends Controller {

		public function __construct(){
			parent::__construct();
		}

		public function pages(){

			$data['title']       = 'mightyCMS - Pages';
			$data['description'] = '';
			$data['keywords']    = '';

			$data['permissions'] = Mighty::Users()->getPermissions();

			$data['additional'] = array(
				'js' => array(
					'/mightycms/'.STATIC_DIR.'/js/pages'
				)
			);

			
			if ($_POST){
				switch ($_POST['action']){
					case 'delete':
							$response = Mighty::Pages()->delete($_POST['id']);
							if ($response->ui_alert){
								$data['response'] = $response;
							}						
						break;
					case 'publish':
							$response = Mighty::Pages()->publish($_POST['id']);
							if ($response->ui_alert){
								$data['response'] = $response;
							}
						break;
				}
			}
			
			$this->view->set('data', $data);
			$this->view->set('view', 'pages.php');
			$this->view->load();

		}

		public function newpage(){

			$data['title']       = 'mightyCMS - Pages / New';
			$data['description'] = '';
			$data['keywords']    = '';

			$data['additional'] = array(
				'js' => array(
					'/mightycms/'.STATIC_DIR.'/js/pages'
				)
			);

			$page_template = ($_POST['template'] ? $_POST['template'] : 'general');

			$template = new template('pages.'.$page_template.'.xml');

			$data['sections'] = $template->getSections();
			$data['template'] = $template;
			$data['page_template'] = $page_template;
			$data['page_parentid'] = $_POST['parentid'];

			$data['default_section'] = $data['sections'][0]->name;

			if($data['page_parentid'])
			$data['breadcrumbs'] = array('Sub Page', Mighty::Pages()->getPage($data['page_parentid'])->name);


			if($_POST) {
				$response = Mighty::Pages()->addNewPage();

				if ($response->ui_alert){
					$data['response'] = $response;
					$data['id'] 	  = $response->id;
				}
			}
			
			$this->view->set('data', $data);
			$this->view->set('view', 'page.php');
			$this->view->load();

		}

		public function editpage(){

			$data['title']       = 'mightyCMS - Pages / Edit';
			$data['description'] = '';
			$data['keywords']    = '';

			$data['additional'] = array(
				'js' => array(
					'/mightycms/'.STATIC_DIR.'/js/pages'
				)
			);

			$data['id'] 		= $_POST['id'];
			$data['page']		= Mighty::Pages()->getPage($data['id']);

			$page_template = $data['page']->template;
			$template = new template('pages.'.$page_template.'.xml');

			$data['sections'] 	= $template->getSections();
			$data['template'] 	= $template;

			$data['default_section'] = $data['sections'][0]->name;

			if($data['id'])
			$data['breadcrumbs'] = array($data['page']->name);


			if($_POST) {
				$response = Mighty::Pages()->editPage();
				$data['page']		= Mighty::Pages()->getPage($data['id']);

				if ($response->ui_alert){
					$data['response'] = $response;
				}
			}
			
			$this->view->set('data', $data);
			$this->view->set('view', 'page.php');
			$this->view->load();

		}

		public function makeURL(){
			
			$url 			= $_POST['content'];
			$array['url'] 	=  Mighty::urlify($url);

			$result = DBi::getRow('SELECT url, id FROM `pages` WHERE `url` = "'.$array['url'].'" ');

			$array['result'] = $result;
			$array['id'] = $result->id;

			echo json_encode($array);
		}

		public function sort(){
			Mighty::Pages()->sort();
		}

		public function controller() {
			$url = ($this->params->url) ? $this->params->url : '';

			switch ($url) {
				case 'new':
					$this->newpage();
					break;
				case 'edit':
					$this->editpage();
					break;
				case 'url':
					$this->makeURL();
					break;
				case 'sort':
					$this->sort();
					break;

				default:
					$this->pages();
					break;
			}
		}

	}

?>