<?php include(STATIC_DIR.'/inc/header_inner.php'); ?>

<form action="" method="post" class="validate" enctype="multipart/form-data">
	<ul class="uim-form">
        <section class="ui_input_wrapper">
        <?php foreach ($section->input as $input){ ?>
            <?php if($input->attr->type != 'checkbox') { ?>
                <?php if (!in_array($input->attr->name, array('password', 'confirm_password')) || $id ){ ?>

                    <?php if (!in_array($input->attr->name, array('confirm_email')) || !$id ){ ?>
                    <li>
                        <?= $template->buildInput($input, $user->{$input->attr->name}); ?>
                    </li>
                    <?php } ?>

                <?php } ?>
            <?php 
                } else {
                    $checkbox[] = $input;
                } ?>

        <?php } ?>

        <?php if ($id){ ?>
            <input type="hidden" name="id" value="<?= $id; ?>" />
        <?php } ?>

        </section>

		<li>
			<button type="submit" class="uim-button green save-page"><?= (!$id) ? 'Add user' : 'Edit user' ?></button>
		</li>
	</ul>

<?php if($permissions->edit_user) { ?>
    <ul class="uim-form uim-preview">
        <?php 
            $permission = array();
            if($UserPermissions)
            foreach ($UserPermissions as $key => $value) {
                if($value != '0' && $key != 'id' && $key != 'userid' ){
                    $permission[] = $key;
                    $count ++;
                }
            }
            $permission = implode(',', $permission);

            // Tick the All areas & features if all others have been ticked
            if($count == 16) {
                $permission .= ',all';
            }
        ?>

        <?php if($checkbox[0]) {?>
        <section class="ui_input_wrapper default">
            <?php foreach ($checkbox as $input){ ?>
                <li>
                    <?= $template->buildInput($input, $permission); ?>
                </li>
            <?php } ?>
        </section>
        <?php }?>
    </ul>
<?php }?>

</form>

<?php include(STATIC_DIR.'/inc/footer_inner.php'); ?>