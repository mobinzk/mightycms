<?php include(STATIC_DIR.'/inc/header_inner.php'); ?>

<?php if($permissions->add_article) { ?>
<a class="uim-button green add-new-page" href="/mightycms/articles/new">Add a new article</a>
<?php }?>

<?php 
	if($articles) {
?>
<div class="uim-table-wrapper blog">
	<table class="uim-table">
		<tr>
			<th>Title</th>
			<th>Date</th>
			<th></th>
		</tr>
		<tbody>
		<?php foreach ($articles as $article) { ?>
		<tr>
			<td><?= $article->name ?></td>
			<td><?php
					$date = explode ('-', $article->date);
					$datep = $date[2].'/'.$date[1].'/'.$date[0];
					echo $datep;
				?></td>
			<td class="action">
				<?php if($permissions->delete_article) { ?>
				<form action="" method="POST">
					<input type="hidden" value="delete" name="action">
					<input type="hidden" value="<?= $article->id ?>" name="id">
					<button type="submit" class="uim-button delete red">Delete</button>
				</form>
				<?php } ?>
				<?php if($permissions->publish_article) { ?>
				<form action="" method="POST">
					<input type="hidden" value="publish" name="action">
					<input type="hidden" value="<?= $article->id ?>" name="id">
					<button type="submit" class="uim-button published <?= ($article->published ? "green" : "red")?>"><?= ($article->published ? "Published" : "Unpublished")?></button>
				</form>
				<?php } ?>
				<?php if($permissions->edit_article) { ?>
				<form action="/mightycms/articles/edit" method="POST">
					<input type="hidden" value="edit" name="action">
					<input type="hidden" value="<?= $article->id ?>" name="id">
					<button type="submit" class="uim-button edit blue">Edit</button>
				</form>
				<?php }?>
			</td>
		</tr>
		<?php } ?>
		</tbody>

	</table>
</div>

<?php } ?>

<?php if($permissions->add_article) { ?>
<a class="uim-button green add-new-page" href="/mightycms/articles/new">Add a new article</a>
<?php }?>

<?php include(STATIC_DIR.'/inc/footer_inner.php'); ?>