<?php include(APP_DIR.'/modules/default/inc/header.php'); ?>

<?php if($permissions->add_article) { ?>
<a class="uim-button green add-new-page" href="/mightycms/articles/add-a-new-category">Add a new category</a>
<?php }?>

<?php 
	$categories = Mighty::Blog()->getCategories();

	if($categories) {
?>
<div class="uim-table-wrapper blog">
	<table class="uim-table">
		<tr>
			<th>Category Title</th>
			<th></th>
		</tr>
		<?php foreach ($categories as $category) { ?>
		<tr>
			<td><?= $category->name ?></td>
			<td class="action">
				<?php if($permissions->delete_article) { ?>
				<form action="" method="POST">
					<input type="hidden" value="delete" name="action">
					<input type="hidden" value="<?= $category->categoryid ?>" name="id">
					<button type="submit" class="uim-button delete red">Delete</button>
				</form>
				<?php } ?>
				<?php if($permissions->publish_article) { ?>
				<form action="" method="POST">
					<input type="hidden" value="publish" name="action">
					<input type="hidden" value="<?= $category->categoryid ?>" name="id">
					<button type="submit" class="uim-button published <?= ($category->published ? "green" : "red")?>">Published</button>
				</form>
				<?php } ?>
				<?php if($permissions->edit_article) { ?>
				<form action="/mightycms/articles/edit-category" method="POST">
					<input type="hidden" value="edit" name="action">
					<input type="hidden" value="<?= $category->categoryid ?>" name="id">
					<button type="submit" class="uim-button edit blue">Edit</button>
				</form>
				<?php }?>
			</td>
		</tr>
		<?php } ?>

	</table>
</div>

<?php } ?>

<?php if($permissions->add_article) { ?>
<a class="uim-button green add-new-page" href="/mightycms/articles/add-a-new-category">Add a new category</a>
<?php }?>

<?php include(APP_DIR.'/modules/default/inc/footer.php'); ?>