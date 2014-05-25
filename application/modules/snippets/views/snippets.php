<?php include(STATIC_DIR.'/inc/header_inner.php'); ?>

<div class="uim-table-wrapper">
	<table class="uim-table">
		<tr>
			<th>Title</th>
			<th>Description</th>
			<th class="action"></th>
		</tr>
		<?php 
			if($snippets)
			foreach ($snippets as $Snippet) { ?>
		<tr>
			<td><?= $Snippet->name ?></td>
			<td><?= $Snippet->description ?></td>
			<td class="action">
				<?php if($permissions->edit_snippets) { ?>
				<form action="/mightycms/snippets/edit" method="POST">
					<input type="hidden" value="edit" name="action">
					<input type="hidden" value="<?= $Snippet->id ?>" name="id">
					<button type="submit" class="uim-button edit blue">Edit</button>
				</form>
				<?php }?>
			</td>
		</tr>
		<?php } ?>
	</table>
</div>

<?php include(STATIC_DIR.'/inc/footer_inner.php'); ?>