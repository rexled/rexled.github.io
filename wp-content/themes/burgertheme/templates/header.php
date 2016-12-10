<!DOCTYPE html>
<!--[if lt IE 7]>
<html class="no-js lt-ie9 lt-ie8 lt-ie7" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 7]>
<html class="no-js lt-ie9 lt-ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8]>
<html class="no-js lt-ie9" <?php language_attributes(); ?>> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" <?php language_attributes(); ?>> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, maximum-scale=1, shrink-to-fit=no">

	<?php if (have_posts()) : ?>
		<link rel="alternate" type="application/rss+xml" title="<?php echo get_bloginfo('name') ?> Feed" href="<?php echo home_url() ?>/feed/">
	<?php endif; ?>

	<?php wp_head(); ?>

	<!--[if lt IE 9]>
	<script src="<?php echo CT_THEME_ASSETS ?>/bootstrap/js/html5shiv.js"></script>
	<script src="<?php echo CT_THEME_ASSETS ?>/bootstrap/js/respond.min.js"></script>
	<![endif]-->

</head>
