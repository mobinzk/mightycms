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
                                        $url[0]->attr->value = $product->{$input->attr->name};
                                    break;
                                    case 'group': 
                                        if (!is_array($input->input)){
                                            $input->input = $input->{0};
                                            unset($input->{0});
                                        }
                                        echo '<li>';
                                            echo '<section class="group">';
                                                foreach ($input->input as $group_input){
                                                    echo $template->buildInput($group_input, (Mighty::Shop()->getField($group_input->attr->name, $product->id) ? Mighty::Shop()->getField($group_input->attr->name, $product->id) : $page->{$group_input->attr->name}));
                                                    // echo $template->buildInput($group_input, ($product->fields->{$group_input->attr->name} ? $product->fields->{$group_input->attr->name} : $product->{$group_input->attr->name}));
                                                }
                                            echo '</section>';
                                        echo '</li>';
                                    break;
                                    default:
                                        echo '<li>';
                                                    if (!$value = Mighty::Shop()->getField($input->attr->name, $id)){
                                                       $value = $product->{$input->attr->name};
                                                    }
                                                    if ($input->attr->data_link_field){
                                                        $value = $product->{$input->attr->data_link_field};
                                                    }
                                                    echo $template->buildInput($input, $value);
                                                // echo $template->buildInput($input, ($product->fields->{$input->attr->name} ? $product->fields->{$input->attr->name} : $product->{$input->attr->name}));
                                        echo '</li>';
                                    break;
                                }
                            ?>
                        <?php } ?>
                    <?php } ?>
                    
                <?php if ($hidden || $product_parentid || $product_template){ ?>

                        <?php if ($hidden){ ?>

                            <?php $hidden_object = array(); ?>

                            <?php foreach ($hidden as $input){ ?>
                                <?php $hidden_object[$input->attr->name] = $input->attr->value; ?>
                                <?= $template->buildInput($input, ($product->fields->{$input->attr->name} ? $product->fields->{$input->attr->name} : $product->{$input->attr->name})); ?>

                            <?php } ?>

                            <?php $hidden_object = (object) $hidden_object; ?>

                        <?php } ?>

                        <?php if ($product_parentid){ ?>
                            <input type="hidden" name="parentid" value="<?= $product_parentid; ?>" />
                        <?php } ?>

                        <?php if ($product_template){ ?>
                            <input type="hidden" name="template" value="<?= $product_template; ?>" />
                        <?php } ?>

                        <?php if (isset($product->deletable)){ ?>
                            <input type="hidden" name="deletable" value="<?= $product->deletable; ?>" />
                        <?php } ?>

                        <?php if (isset($product->editable)){ ?>
                            <input type="hidden" name="editable" value="<?= $product->editable; ?>" />
                        <?php } ?>

                        <?php if (isset($product->publishable)){ ?>
                            <input type="hidden" name="publishable" value="<?= $product->publishable; ?>" />
                        <?php } ?>

                        <?php if (isset($product->subpagable)){ ?>
                            <input type="hidden" name="subpagable" value="<?= $product->subpagable; ?>" />
                        <?php } ?>
                        
				<?php } ?>

                        <?php if ($id){ ?>
                            <input type="hidden" name="id" value="<?= $id; ?>" />
                        <?php } ?>

            </section>

		<?php } ?>

		<li>
			<button type="submit" class="uim-button green save-page"><?= ($id) ? 'Edit product' : 'Save product' ?></button>
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