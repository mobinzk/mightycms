<?php include(STATIC_DIR.'/inc/header_inner.php'); ?>

<form action="" method="post" class="validate" enctype="multipart/form-data">
	<ul class="uim-form">
        <?php foreach ($sections as $s){ ?>

            <?php $section = $template->getSection($s->name); ?>

                <section class="ui_input_wrapper <?= $s->name; ?> <?= ($default_section != $s->name ? 'uim-hidden' : ''); ?>">

                    <?php foreach ($section->input as $input){ ?>
                        <?php if (!in_array($input->attr->name, array())){ ?>
                            <?php 
                                switch ($input->attr->type){
                                    case 'hidden':
                                        $hidden[] = $input;
                                    break;
                                    case 'url':
                                        $url[] = $input;
                                        $url[0]->attr->value = $page->{$input->attr->name};
                                    break;
                                    case 'group': 
                                        if (!is_array($input->input)){
                                            $input->input = $input->{0};
                                            unset($input->{0});
                                        }
                                        echo '<li>';
                                            echo '<section class="group">';
                                                foreach ($input->input as $group_input){
                                                    echo $template->buildInput($group_input, (Mighty::Pages()->getField($group_input->attr->name, $page->id) ? Mighty::Pages()->getField($group_input->attr->name, $page->id) : $page->{$group_input->attr->name}));
                                                }
                                            echo '</section>';
                                        echo '</li>';
                                    break;
                                    default:
                                        echo '<li>';
                                                    if (!$value = Mighty::Pages()->getField($input->attr->name, $id)){
                                                       $value = $page->{$input->attr->name};
                                                    }
                                                    if ($input->attr->data_link_field){
                                                        $value = $page->{$input->attr->data_link_field};
                                                    }
                                                    echo $template->buildInput($input, $value);
                                            // echo $template->buildInput($input, (Mighty::Pages()->getField($input->attr->name, $id) ? Mighty::Pages()->getField($input->attr->name, $id) : $page->{$input->attr->name}));
                                        echo '</li>';
                                    break;
                                }
                            ?>
                        <?php } ?>
                    <?php } ?>
                    
                <?php if ($hidden || $page_parentid || $page_template){ ?>

                        <?php if ($hidden){ ?>

                            <?php $hidden_object = array(); ?>

                            <?php foreach ($hidden as $input){ ?>
                                <?php $hidden_object[$input->attr->name] = $input->attr->value; ?>
                                <?= $template->buildInput($input, (Mighty::Pages()->getField($input->attr->name, $id) ? Mighty::Pages()->getField($input->attr->name, $id) : $page->{$input->attr->name})); ?>

                            <?php } ?>

                            <?php $hidden_object = (object) $hidden_object; ?>

                        <?php } ?>

                        <?php if ($page_parentid){ ?>
                            <input type="hidden" name="parentid" value="<?= $page_parentid; ?>" />
                        <?php } ?>

                        <?php if ($page_template){ ?>
                            <input type="hidden" name="template" value="<?= $page_template; ?>" />
                        <?php } ?>

                        <?php if ($id){ ?>
                            <input type="hidden" name="id" value="<?= $id; ?>" />
                        <?php } ?>

                        <?php if (isset($page->deletable)){ ?>
                            <input type="hidden" name="deletable" value="<?= $page->deletable; ?>" />
                        <?php } ?>

                        <?php if (isset($page->editable)){ ?>
                            <input type="hidden" name="editable" value="<?= $page->editable; ?>" />
                        <?php } ?>

                        <?php if (isset($page->publishable)){ ?>
                            <input type="hidden" name="publishable" value="<?= $page->publishable; ?>" />
                        <?php } ?>

                        <?php if (isset($page->subpagable)){ ?>
                            <input type="hidden" name="subpagable" value="<?= $page->subpagable; ?>" />
                        <?php } ?>
                        
				<?php } ?>

            </section>

		<?php } ?>

		<li>
			<button type="submit" class="uim-button green save-page"><?= ($id) ? 'Edit page' : 'Save page' ?></button>
		</li>
	</ul>

	<ul class="uim-form uim-preview">
        <?php if($url[0]) {?>
        <section class="ui_input_wrapper default">
    		<li>
    			<?= $template->buildInput($url[0], $url[0]->attr->value); ?>
    		</li>
        </section>
        <?php }?>
	</ul>

</form>

<?php include(STATIC_DIR.'/inc/footer_inner.php'); ?>