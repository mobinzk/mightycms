<?php include(STATIC_DIR.'/inc/header_inner.php'); ?>

<div class="uim-table-wrapper">
	<?php if($permissions->add_filemanager) { ?>
	<form action="" method="post" class="upload" enctype="multipart/form-data">
		<label for="files">Upload a new file: </label>
		<input class="file" type="file" name="files_file_upload_item" id="files">
		<input type="hidden" name="action" value="upload">
		<input type="hidden" name="files_option_upload_dir" value="../mighty_config/dyn">
		<button type="submit" class="uim-button blue red">Upload</button>
	</form>
	<?php }?>

	<?php if($files) { ?>
	<table class="uim-table">
		<tr>
			<th width="100"></th>
			<th>URL</th>
			<th class="action"></th>
		</tr>
		<?php foreach ($files as $file) { ?>
		<tr>
			<td>
				<?php if(Mighty_Utilities::isimage(MIGHTY_CONFIG_DIR.$file->url)) { ?>
					<a href="<?= $file->url ?>" target="_blank"><img class="pimage" src="<?= $file->url ?>" alt=""></a>
				<?php } ?>
			</td>
			<td><a href="<?= $file->url ?>" target="_blank"><?= $file->url ?></a></td>
			<td class="action">
				<a href="<?= $file->url ?>" target="_blank" class="uim-button green view">View file</a>
				<?php if($permissions->delete_filemanager) { ?>
				<form action="" method="post">
					<input type="hidden" value="delete" name="action">
					<input type="hidden" value="<?= $file->id ?>" name="id">
					<button type="submit" class="uim-button delete red">Delete</button>
				</form>
				<?php }?>
			</td>
		</tr>
		<?php } ?>
	</table>
	<?php }?>
</div>

<?php include(STATIC_DIR.'/inc/footer_inner.php'); ?>