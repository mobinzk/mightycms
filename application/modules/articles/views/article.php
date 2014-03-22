<?php include(APP_DIR.'/modules/default/inc/header.php'); ?>

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
                                        $url[0]->attr->value = $article->{$input->attr->name};
                                    break;
                                    case 'group': 
                                        if (!is_array($input->input)){
                                            $input->input = $input->{0};
                                            unset($input->{0});
                                        }
                                        echo '<li>';
                                            echo '<section class="group">';
                                                foreach ($input->input as $group_input){
                                                    echo $template->buildInput($group_input, ($article->fields->{$group_input->attr->name} ? $article->fields->{$group_input->attr->name} : $article->{$group_input->attr->name}));
                                                }
                                            echo '</section>';
                                        echo '</li>';
                                    break;
                                    default:
                                        echo '<li>';
                                                echo $template->buildInput($input, ($article->fields->{$input->attr->name} ? $article->fields->{$input->attr->name} : $article->{$input->attr->name}));
                                        echo '</li>';
                                    break;
                                }
                            ?>
                        <?php } ?>
                    <?php } ?>
                    
                <?php if ($hidden || $article_parentid || $article_template){ ?>

                        <?php if ($hidden){ ?>

                            <?php $hidden_object = array(); ?>

                            <?php foreach ($hidden as $input){ ?>
                                <?php $hidden_object[$input->attr->name] = $input->attr->value; ?>
                                <?= $template->buildInput($input, ($article->fields->{$input->attr->name} ? $article->fields->{$input->attr->name} : $article->{$input->attr->name})); ?>

                            <?php } ?>

                            <?php $hidden_object = (object) $hidden_object; ?>

                        <?php } ?>

                        <?php if ($article_parentid){ ?>
                            <input type="hidden" name="parentid" value="<?= $article_parentid; ?>" />
                        <?php } ?>

                        <?php if ($article_template){ ?>
                            <input type="hidden" name="template" value="<?= $article_template; ?>" />
                        <?php } ?>

                        <?php if (isset($article->deletable)){ ?>
                            <input type="hidden" name="deletable" value="<?= $article->deletable; ?>" />
                        <?php } ?>

                        <?php if (isset($article->editable)){ ?>
                            <input type="hidden" name="editable" value="<?= $article->editable; ?>" />
                        <?php } ?>

                        <?php if (isset($article->publishable)){ ?>
                            <input type="hidden" name="publishable" value="<?= $article->publishable; ?>" />
                        <?php } ?>

                        <?php if (isset($article->subpagable)){ ?>
                            <input type="hidden" name="subpagable" value="<?= $article->subpagable; ?>" />
                        <?php } ?>
                        
				<?php } ?>

                        <?php if ($id){ ?>
                            <input type="hidden" name="id" value="<?= $id; ?>" />
                        <?php } ?>

            </section>

		<?php } ?>

		<li>
			<button type="submit" class="uim-button green save-page"><?= ($id) ? 'Edit article' : 'Save article' ?></button>
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

<?php include(APP_DIR.'/modules/default/inc/footer.php'); ?>