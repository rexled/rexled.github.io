<?php ?><?php ?><?php ?>

<?php

if (is_singular())
return;

global $wp_query;

/** Stop execution if there's only 1 page */
if ($wp_query->max_num_pages <= 1)
return;

$paged = get_query_var('paged') ? absint(get_query_var('paged')) : 1;
$max = intval($wp_query->max_num_pages);

/**    Add current page to the array */
if ($paged >= 1)
$links[] = $paged;

/**    Add the pages around the current page to the array */
//se quisermos ter + do que 1 previous page
if ($paged >= 3) {
$links[] = $paged - 1;
//$links[] = $paged - 2;
}
//se quisermos ter + do que 1 next page
if (($paged + 2) <= $max) {
//$links[] = $paged + 2;
$links[] = $paged + 1;
}
?>

<div class="row">
    <div class="col-md-12">
        <div class="text-center">
            <ul class="pagination">

                <?php /**    Previous Post Link */

                if ($paged != 1): ?>
                <li><a href="<?php echo get_previous_posts_page_link(); ?>"><i
                            class="fa fa-arrow-left"></i><?php echo __('Previous Page', 'ct_theme') ?> </a></li>

                <?php else: ?>
                <li class="disabled"><a href="<?php echo get_previous_posts_page_link(); ?>"><i
                            class="fa fa-arrow-left"></i><?php echo __('Previous Page', 'ct_theme') ?> </a></li>
                <?php endif; ?>

<?php
                /**    Link to first page, plus ellipses if necessary */
                if (! in_array(1, $links)) {
                $class = 1 == $paged ? ' class="active"' : '';
                //se quiseremos mostrar a primeira pagina
                if ($paged==2){
                printf('<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url(get_pagenum_link(1)), '1');
                }

                if (! in_array(2, $links)){
                echo '<li></li>';
                }
                }

                /**    Link to current page, plus 2 pages in either direction if necessary */
                sort($links);
                foreach ((array)$links as $link) {
                $class = $paged == $link ? ' class="active"' : '';
                printf('<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url(get_pagenum_link($link)), $link);
                }

                /**    Link to last page, plus ellipses if necessary */
                if (! in_array($max, $links)) {
                if (! in_array($max - 1, $links))
                echo '<li></li>' . "\n";

                $class = $paged == $max ? ' class="active"' : '';
                }

                if ($paged==$max-1){
                printf('<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url(get_pagenum_link($max)), $max);
                }

                /**    Next Post Link */
                ?>

<?php if ($paged != $wp_query->max_num_pages): ?>
                <li><a href="<?php echo get_next_posts_page_link(); ?>"><?php echo __('Next Page', 'ct_theme') ?> <i
                            class="fa fa-arrow-right"></i></a></li>

                <?php else: ?>
                <li class="disabled"><a
                        href="<?php echo get_next_posts_page_link(); ?>"><?php echo __('Next Page', 'ct_theme') ?> <i
                            class="fa fa-arrow-right"></i></a></li>
                <?php endif; ?>

            </ul>
        </div>
    </div>
</div>