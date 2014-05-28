<?php include(STATIC_DIR.'/inc/header_inner.php'); ?>

<?php if($permissions->add_product) { ?>
<a class="uim-button green add-new-page" href="/mightycms/shop/new-product">Add a new product</a>
<?php }?>

<?php 
	if($products) {
?>
<div class="uim-table-wrapper shop">
	<table class="uim-table">
		<tr>
			<th>Product Title</th>
			<th>Category</th>
			<th></th>
		</tr>
		<tbody>
		<?php foreach ($products as $product) { ?>
		<tr id="<?= $product->id ?>">
			<td><?= $product->name ?></td>
			<td><?php 
				$category = Mighty_Shop::getCategory($product->categoryid)->name;
				if($category) { 
					echo $category;
				}
			?></td>
			<td class="action">
				<?php if($permissions->delete_product) { ?>
				<form action="" method="POST">
					<input type="hidden" value="delete" name="action">
					<input type="hidden" value="<?= $product->id ?>" name="id">
					<button type="submit" class="uim-button delete red">Delete</button>
				</form>
				<?php } ?>
				<?php if($permissions->publish_product) { ?>
				<form action="" method="POST">
					<input type="hidden" value="publish" name="action">
					<input type="hidden" value="<?= $product->id ?>" name="id">
					<button type="submit" class="uim-button published <?= ($product->published ? "green" : "red")?>"><?= ($product->published ? "Published" : "Unpublished")?></button>
				</form>
				<?php } ?>
				<?php if($permissions->edit_product) { ?>
				<form action="/mightycms/shop/edit-product" method="POST">
					<input type="hidden" value="edit" name="action">
					<input type="hidden" value="<?= $product->id ?>" name="id">
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
<a class="uim-button green add-new-page" href="/mightycms/shop/new-product">Add a new product</a>
<?php }?>

<?php include(STATIC_DIR.'/inc/footer_inner.php'); ?>