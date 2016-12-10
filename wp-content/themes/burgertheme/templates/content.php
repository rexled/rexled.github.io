<?php
//The Query
global $wp_query;
$arrgs = $wp_query->query_vars;
$arrgs['posts_per_page'] = ct_get_option("posts_index_per_page", 3);
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$arrgs['paged'] = $paged;
$wp_query->query($arrgs);
?>

<div class="inner">

    <?php if (have_posts()) : ?>
        <?php while (have_posts()) : the_post(); ?>
            <?php $format = get_post_format();
            $format = $format ? $format : 'standard';
            $class = $format == 'standard' ? 'format-type-image' : 'format-type-' . $format;
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class($class); ?>>
                <?php get_template_part('templates/post/content-' . $format); ?>
            </article>
        <?php endwhile; ?>

        <?php get_template_part('templates/post/content-post', 'pagination'); ?>

    <?php else: ?>
        <div class="inner">
            <h2 class="search-header col-md-9">
                <?php _e('No search results found', 'ct_theme'); ?>
            </h2>
        </div>
    <?php endif; ?>
    <!-- / row -->

</div>
