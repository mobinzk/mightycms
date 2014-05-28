<?php include(STATIC_DIR.'/inc/header_inner.php'); ?>

<?php if($permissions->add_product) { ?>
<a class="uim-button green add-new-page" href="/mightycms/shop/add-a-new-category">Add a new category</a>
<?php }?>

<?php 
	if($categories) {
?>
<div class="uim-table-wrapper shop">
	<table class="uim-table">
		<tr>
			<th>Category Title</th>
			<th>Number of Products</th>
			<th></th>
		</tr>
		<tbody>
		<?php foreach ($categories as $category) { ?>
		<tr id="<?= $category->categoryid ?>">
			<td><?= $category->name ?></td>
			<td><?= Mighty_Shop::getNoProducts($category->categoryid) ?></td>
			<td class="action">
				<?php if($permissions->delete_product) { ?>
				<form action="" method="POST">
					<input type="hidden" value="delete" name="action">
					<input type="hidden" value="<?= $category->categoryid ?>" name="id">
					<button type="submit" class="uim-button delete red">Delete</button>
				</form>
				<?php } ?>
				<?php if($permissions->publish_product) { ?>
				<form action="" method="POST">
					<input type="hidden" value="publish" name="action">
					<input type="hidden" value="<?= $category->categoryid ?>" name="id">
					<button type="submit" class="uim-button published <?= ($category->published ? "green" : "red")?>"><?= ($category->published ? "Published" : "Unpublished")?></button>
				</form>
				<?php } ?>
				<?php if($permissions->edit_product) { ?>
				<form action="/mightycms/shop/edit-category" method="POST">
					<input type="hidden" value="edit" name="action">
					<input type="hidden" value="<?= $category->categoryid ?>" name="id">
					<button type="submit" class="uim-button edit blue">Edit</button>
				</form>
				<?php }?>
				<div class="move">
					<div class="icon-move"></div>
				</div>
			</td>
		</tr>
		<?php } ?>
		</tbody>

	</table>
</div>

<?php } ?>

<?php if($permissions->add_product) { ?>
<a class="uim-button green add-new-page" href="/mightycms/shop/add-a-new-category">Add a new category</a>
<?php }?>

<?php include(STATIC_DIR.'/inc/footer_inner.php'); ?>