<?php $imgsrc = ct_get_feature_image_src(get_the_ID(), 'featured_image');
$atr = ct_get_featured_image_data (get_the_ID(),1000,1000);?>


<img src="<?php echo $imgsrc?>" alt="<?php echo esc_attr( $atr['alt']) ?>"
     title="<?php echo esc_attr( $atr['title']) ?>">


