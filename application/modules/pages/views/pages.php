<?php include(STATIC_DIR.'/inc/header_inner.php'); ?>

<?php if($permissions->add_page) { ?>
<a class="uim-button green add-new-page" href="/mightycms/pages/new">Add a new page</a>
<?php }?>

<?php if($pages) { ?>
<ul class="pages">
	<?= Mighty_pages::echoPages($pages);?>
</ul>
<?php }?>

<?php if($permissions->add_page) { ?>
<a class="uim-button green add-new-page" href="/mightycms/pages/new">Add a new page</a>
<?php }?>

<?php include(STATIC_DIR.'/inc/footer_inner.php'); ?>