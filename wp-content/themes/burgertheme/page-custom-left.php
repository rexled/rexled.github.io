<?php
/*
Template Name: Left Sidebar Template
*/
?>
<?php get_template_part('templates/page', 'head'); ?>
<?php $breadcrumbs = ct_show_single_post_breadcrumbs('page') ? 'yes' : 'no';?>
<?php $pageTitle = ct_get_single_post_title('page');?>
<?php if($pageTitle || $breadcrumbs == "yes"):?>

	<?php echo do_shortcode('[title_row header="' . $pageTitle . '" breadcrumbs="' . $breadcrumbs . '"]')?>

<?php endif;?>


    <div class="row">
	    <section id="Sidebar" class="col-md-3">
            <?php get_template_part('templates/sidebar'); ?>
        </section>
        <section id="Content" class="col-md-4 offset2">
	        <?php get_template_part('templates/content', 'page-custom'); ?>
        </section>
    </div>
</section>