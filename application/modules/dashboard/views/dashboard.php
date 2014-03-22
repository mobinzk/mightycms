<?php include(APP_DIR.'/modules/default/inc/header.php'); ?>

<div class="uim-table-wrapper">
	<table class="uim-table">
		<tr>
			<th width="16"></th>
			<th>Recent activities</th>
			<th class="time">Time</th>
		</tr>

		<?php foreach (Mighty::Activities()->getAll(15) as $activity) { ?>
		<tr>
			<td class="icons"><span class="icon-<?= $activity->action ?>"></span></td>
			<td><?= '<strong>'.$activity->user.'</strong> '.$activity->message ?></td>
			<td class="time" title="<?= DATE('l jS \of F Y - h:i:s A', $activity->time) ?>"><?= Mighty::Activities()->timeAgo($activity->time) ?></td>
		</tr>
		<?php } ?>

	</table>
</div>

<?php include(APP_DIR.'/modules/default/inc/footer.php'); ?>