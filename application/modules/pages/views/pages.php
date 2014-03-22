<?php include(APP_DIR.'/modules/default/inc/header.php'); ?>

<?php $pages = Pages::getPages(0); ?>

<?php if($permissions->add_page) { ?>
<a class="uim-button green add-new-page" href="/mightycms/pages/new">Add a new page</a>
<?php }?>

<?php if($pages) { ?>
<ul class="pages">
	<?= Pages::echoPages($pages);?>
</ul>
<?php }?>

<?php if($permissions->add_page) { ?>
<a class="uim-button green add-new-page" href="/mightycms/pages/new">Add a new page</a>
<?php }?>

<?php include(APP_DIR.'/modules/default/inc/footer.php'); ?>