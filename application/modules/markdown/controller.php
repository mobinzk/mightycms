<?php 

	class Markdown_Controller extends Controller {

		public function __construct(){
			parent::__construct();
		}

		public function markdownHTML(){
			
			$parser = new Markdown;
			echo $parser->transform($_POST['content']);
			// echo Markdown::defaultTransform($_POST['content']);

		}
	}

?>