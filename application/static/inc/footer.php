    <script src="/mightycms/<?= Versioning::auto(STATIC_DIR."/js/global.js"); ?>"></script>
	<script src="/mightycms/<?= Versioning::auto(STATIC_DIR."/js/preview.js"); ?>"></script>
	<?php
		
		// Load additional JS files
		if (isset($additional['js'])){
			echo "\n".'<!-- Additional JS -->'."\n";
			foreach ($additional['js'] as $js){
				echo '<script type="text/javascript" src="'.Versioning::auto($js.'.js').'"></script>'."\n";	
			}
		}
		
	?>
    </body>
</html>
