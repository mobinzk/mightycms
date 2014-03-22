<?php 

	class Articles_Controller extends Controller {

		public function __construct(){
			parent::__construct();
		}

		public function articles(){

			$data['title']       = 'mightyCMS - Articles';
			$data['description'] = '';
			$data['keywords']    = '';
			
			$data['permissions'] = Mighty::Users()->getPermissions();

			if ($_POST){
				switch ($_POST['action']){
					case 'delete':
							$response = Mighty::Blog()->delete($_POST['id']);
							if ($response->ui_alert){
								$data['response'] = $response;
								$data['id'] 	  = $response->id;
							}
						break;
					case 'publish':
							$response = Mighty::Blog()->publish($_POST['id']);
							if ($response->ui_alert){
								$data['response'] = $response;
								$data['id'] 	  = $response->id;
							}
						break;
				}
			}

			
			$this->view->set('data', $data);
			$this->view->set('view', 'articles.php');
			$this->view->load();

		}

		public function newArticle(){

			$data['title']       = 'mightyCMS - Articles / New Article';
			$data['description'] = '';
			$data['keywords']    = '';

			$data['additional'] = array(
				'js' => array(
					'/mightycms/'.STATIC_DIR.'/js/blog'
				)
			);

			$template = new template('article.xml');

			$data['sections'] = $template->getSections();
			$data['template'] = $template;

			$data['default_section'] = $data['sections'][0]->name;


			if($_POST) {
				$response = Mighty::Blog()->addNewArticle();
				if ($response->ui_alert){
					$data['response'] = $response;
					$data['id'] 	  = $response->id;
				}
			}
			
			$this->view->set('data', $data);
			$this->view->set('view', 'article.php');
			$this->view->load();

		}

		public function editArticle(){

			$data['title']       = 'mightyCMS - Articles / Edit Article';
			$data['description'] = '';
			$data['keywords']    = '';

			$data['additional'] = array(
				'js' => array(
					'/mightycms/'.STATIC_DIR.'/js/blog'
				)
			);

			$data['id'] 		= $_POST['id'];
			$data['article']		= Mighty::Blog()->getArticle($data['id']);

			$template = new template('article.xml');

			$data['sections'] 	= $template->getSections();
			$data['template'] 	= $template;

			$data['default_section'] = $data['sections'][0]->name;

			if($data['id'])
			$data['breadcrumbs'] = array($data['article']->name);


			if($_POST) {
				$response = Mighty::Blog()->editArticle();
				$data['article'] = Mighty::Blog()->getArticle($data['id']);

				if ($response->ui_alert){
					$data['response'] = $response;
				}
			}
			
			$this->view->set('data', $data);
			$this->view->set('view', 'article.php');
			$this->view->load();

		}

		public function makeURL(){
			
			$url 			= $_POST['content'];
			$array['url'] 	=  Mighty::urlify($url);

			$result = DBi::getRow('SELECT url, id FROM `blog` WHERE `url` = "'.$array['url'].'" ');

			$array['result'] = $result;
			$array['id'] = $result->id;

			echo json_encode($array);
		}
		
		public function controller() {
			$url = ($this->params->url) ? $this->params->url : '';

			switch ($url) {
				case 'new':
					$this->newArticle();
					break;
				case 'edit':
					$this->editArticle();
					break;
				case 'url':
					$this->makeURL();
					break;

				default:
					$this->articles();
					break;
			}
		}

	}

?>