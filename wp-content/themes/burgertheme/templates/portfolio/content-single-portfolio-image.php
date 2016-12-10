<?php $imageUrl = '';
$width = 600;
$height = 1000;
$atr = ct_get_featured_image_data (get_the_ID(),$width,$height);?>

<?php if (has_post_thumbnail(get_the_ID())): ?>
	<?php $image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'full'); ?>
	<?php $imageUrl = $image[0]; ?>
<?php endif; ?>
	<?php echo do_shortcode('[img src="' . $imageUrl . '" width="" height="" title="'.esc_attr( $atr['title']).'" alt="'. esc_attr( $atr['alt']).'"]') ?>