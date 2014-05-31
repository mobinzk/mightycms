<?php include(STATIC_DIR.'/inc/header_inner.php'); ?>

<?php if($permissions->add_user) { ?>
<a class="uim-button green add-new-page" href="/mightycms/users/new">Add a new user</a>
<?php }?>

<?php 
	$users = Mighty::Users()->getAll();

	if($users) {
?>
<div class="uim-table-wrapper users">
	<table class="uim-table">
		<tr>
			<th>User</th>
			<th>Email Address</th>
			<th></th>
		</tr>
		<tbody>
		<?php foreach ($users as $user) { ?>
		<tr>
			<td><?= $user->firstname.' '.$user->surname ?></td>
			<td><?= $user->username ?></td>
			<td class="action">
				<?php if($permissions->delete_user) { ?>
				<form action="" method="POST">
					<input type="hidden" value="delete" name="action">
					<input type="hidden" value="<?= $user->id ?>" name="id">
					<button type="submit" class="uim-button delete red">Delete</button>
				</form>
				<?php }?>
				<?php if($permissions->edit_user || ($permissions->edit_the_user && $user->id == Mighty::Auth()->userId() ) ) { ?>
					<form action="/mightycms/users/edit" method="POST">
						<input type="hidden" value="edit" name="action">
						<input type="hidden" value="<?= $user->id ?>" name="id">
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

<?php if($permissions->add_user) { ?>
<a class="uim-button green add-new-page" href="/mightycms/users/new">Add a new user</a>
<?php }?>

<?php include(STATIC_DIR.'/inc/footer_inner.php'); ?>