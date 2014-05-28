<?php 

	class Shop_Controller extends Controller {

		public function __construct(){
			parent::__construct();
		}

		public function Products(){

			$data['title']       = 'mightyCMS - Products';
			$data['description'] = '';
			$data['keywords']    = '';

			$data['additional'] = array(
				'js' => array(
					'/mightycms/'.STATIC_DIR.'/js/product'
				)
			);
			
			$data['permissions'] = Mighty::Users()->getPermissions();

			if ($_POST){
				switch ($_POST['action']){
					case 'delete':
							$response = Mighty::Shop()->delete($_POST['id']);
							if ($response->ui_alert){
								$data['response'] = $response;
								$data['id'] 	  = $response->id;
							}
						break;
					case 'publish':
							$response = Mighty::Shop()->publish($_POST['id']);
							if ($response->ui_alert){
								$data['response'] = $response;
								$data['id'] 	  = $response->id;
							}
						break;
				}
			}

			$data['products'] = Mighty::Shop()->getAll();
			
			$this->view->set('data', $data);
			$this->view->set('view', 'products.php');
			$this->view->load();

		}

		public function newProduct(){

			$data['title']       = 'mightyCMS - Products / New Product';
			$data['description'] = '';
			$data['keywords']    = '';

			$data['additional'] = array(
				'js' => array(
					'/mightycms/'.STATIC_DIR.'/js/product'
				)
			);

			$template = new template('shop.product.xml');

			$data['sections'] = $template->getSections();
			$data['template'] = $template;

			$data['default_section'] = $data['sections'][0]->name;


			if($_POST) {
				$response = Mighty::Shop()->addNewProduct();
				if ($response->ui_alert){
					$data['response'] = $response;
					$data['id'] 	  = $response->id;
				}
			}
			
			$this->view->set('data', $data);
			$this->view->set('view', 'product.php');
			$this->view->load();

		}

		public function editProduct(){

			$data['title']       = 'mightyCMS - Products / Edit product';
			$data['description'] = '';
			$data['keywords']    = '';

			$data['additional'] = array(
				'js' => array(
					'/mightycms/'.STATIC_DIR.'/js/product'
				)
			);

			$data['id'] 		= $_POST['id'];
			$data['product']		= Mighty::Shop()->getProduct($data['id']);

			$template = new template('shop.product.xml');

			$data['sections'] 	= $template->getSections();
			$data['template'] 	= $template;

			$data['default_section'] = $data['sections'][0]->name;

			if($data['id'])
			$data['breadcrumbs'] = array($data['product']->name);


			if($_POST) {
				$response = Mighty::Shop()->editProduct();
				$data['product'] = Mighty::Shop()->getProduct($data['id']);

				if ($response->ui_alert){
					$data['response'] = $response;
				}
			}
			
			$this->view->set('data', $data);
			$this->view->set('view', 'product.php');
			$this->view->load();

		}

		public function makeURL(){
			
			$url 			= $_POST['content'];
			$array['url'] 	=  Mighty_Utilities::urlify($url);

			$result = DBi::getRow('SELECT url, id FROM `shop_products` WHERE `url` = "'.$array['url'].'" ');

			$array['result'] = $result;
			$array['id'] = $result->id;

			echo json_encode($array);
		}

		public function makeURLCategory(){
			
			$url 			= $_POST['content'];
			$array['url'] 	=  Mighty_Utilities::urlify($url);

			$result = DBi::getRow('SELECT url, categoryid FROM `shop_products_categories` WHERE `url` = "'.$array['url'].'" ');

			$array['result'] = $result;
			$array['id'] = $result->id;

			echo json_encode($array);
		}

		public function categories(){

			$data['title']       = 'mightyCMS - Products / Categories';
			$data['description'] = '';
			$data['keywords']    = '';

			$data['additional'] = array(
				'js' => array(
					'/mightycms/'.STATIC_DIR.'/js/shop-category'
				)
			);
			
			$data['permissions'] = Mighty::Users()->getPermissions();

			if ($_POST){
				switch ($_POST['action']){
					case 'delete':
							$response = Mighty::Shop()->deleteCategory($_POST['id']);
							if ($response->ui_alert){
								$data['response'] = $response;
								$data['id'] 	  = $response->id;
							}
						break;
					case 'publish':
							$response = Mighty::Shop()->publishCategory($_POST['id']);
							if ($response->ui_alert){
								$data['response'] = $response;
								$data['id'] 	  = $response->id;
							}
						break;
				}
			}

			$data['categories'] = Mighty::Shop()->getCategories();

			
			$this->view->set('data', $data);
			$this->view->set('view', 'categories.php');
			$this->view->load();

		}

		public function newCategory(){

			$data['title']       = 'mightyCMS - Products / Add a New Category';
			$data['description'] = '';
			$data['keywords']    = '';

			$data['additional'] = array(
				'js' => array(
					'/mightycms/'.STATIC_DIR.'/js/shop-category'
				)
			);

			$template = new template('shop.category.xml');

			$data['sections'] = $template->getSections();
			$data['template'] = $template;

			$data['default_section'] = $data['sections'][0]->name;


			if($_POST) {
				$response = Mighty::Shop()->addNewCategory();
				if ($response->ui_alert){
					$data['response'] = $response;
					$data['id'] 	  = $response->id;
				}
			}
			
			$this->view->set('data', $data);
			$this->view->set('view', 'category.php');
			$this->view->load();

		}
		
		public function editCategory(){

			$data['title']       = 'mightyCMS - Products / Edit Category';
			$data['description'] = '';
			$data['keywords']    = '';

			$data['additional'] = array(
				'js' => array(
					'/mightycms/'.STATIC_DIR.'/js/shop-category'
				)
			);

			$data['id'] 		= $_POST['id'];
			$data['article']		= Mighty::Shop()->getCategory($data['id']);

			$template = new template('shop.category.xml');

			$data['sections'] 	= $template->getSections();
			$data['template'] 	= $template;

			$data['default_section'] = $data['sections'][0]->name;

			if($data['id'])
			$data['breadcrumbs'] = array($data['article']->name);


			if($_POST) {
				$response = Mighty::Shop()->editCategory();
				$data['article'] = Mighty::Shop()->getCategory($data['id']);

				if ($response->ui_alert){
					$data['response'] = $response;
				}
			}
			
			$this->view->set('data', $data);
			$this->view->set('view', 'category.php');
			$this->view->load();

		}

		public function sort_products(){
			Mighty::Shop()->sort_products();
		}

		public function sort_categories(){
			Mighty::Shop()->sort_categories();
		}

		public function controller() {
			$url = ($this->params->url) ? $this->params->url : '';

			switch ($url) {
				case 'new-product':
					$this->newProduct();
					break;
				case 'edit-product':
					$this->editProduct();
					break;
				case 'url':
					$this->makeURL();
					break;
				case 'urlCategory':
					$this->makeURLCategory();
					break;
				case 'categories':
					$this->categories();
					break;
				case 'add-a-new-category':
					$this->newCategory();
					break;
				case 'edit-category':
					$this->editCategory();
					break;
				case 'sort-products':
					$this->sort_products();
					break;
				case 'sort-categories':
					$this->sort_categories();
					break;
				default:
					$this->products();
					break;
			}
		}

	}

?>