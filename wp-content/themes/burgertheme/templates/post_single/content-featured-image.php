<?php $imageSize = ct_show_single_post_image_size();    ?>

<?php switch ($imageSize){
		case 'full':
				$width = '620px';
				$height = '211px';
                 $atr = ct_get_featured_image_data (get_the_ID(),$width,$height);
				if (has_post_thumbnail(get_the_ID())){
					$image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), array($width, $height));
					$imageUrl = $image[0]; ?>

					<img src="<?php echo $imageUrl?>" alt="<?php echo esc_attr( $atr['alt']) ?>"
                         title="<?php echo esc_attr( $atr['title']) ?>">

				<?php }
				break;
		case 'small':
				$width ='180px';
				$height = '180px';
            $atr = ct_get_featured_image_data (get_the_ID(),$width,$height);
				if (has_post_thumbnail(get_the_ID())){
					$image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), array($width, $height));
					$imageUrl = $image[0]; ?>

					<img src="<?php echo $imageUrl?>" alt="<?php echo esc_attr( $atr['alt']) ?>"
                         title="<?php echo esc_attr( $atr['title']) ?>">

				<?php }
				break;
		default: '1';
	}?>

