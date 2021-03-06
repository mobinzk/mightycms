<?php include(STATIC_DIR.'/inc/header_inner.php'); ?>

<div class="uim-table-wrapper">
	<table class="uim-table">
		<tr>
			<th width="16"></th>
			<th>Recent activities</th>
			<th class="time">Time</th>
		</tr>

		<tbody>
		<?php foreach (Mighty::Activities()->getAll(15) as $activity) { ?>
		<tr>
			<td class="icons"><span class="icon-<?= $activity->action ?>"></span></td>
			<td><?= '<strong>'.$activity->user.'</strong> '.$activity->message ?></td>
			<td class="time" title="<?= DATE('l jS \of F Y - h:i:s A', $activity->time) ?>"><?= Mighty::Activities()->timeAgo($activity->time) ?></td>
		</tr>
		<?php } ?>
		</tbody>

	</table>
</div>

<?php include(STATIC_DIR.'/inc/footer_inner.php'); ?>