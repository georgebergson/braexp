<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package storefront
 */

get_header();?>
<?php if (get_header_image() || true): ?>
    <div id="site-header">
        <a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
            <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/BRAEXP_header_contato.jpg" width="<?php echo absint(get_custom_header()->width); ?>" height="<?php echo absint(get_custom_header()->height); ?>" alt="<?php echo esc_attr(get_bloginfo('name', 'display')); ?>">
        </a>
    </div>
<?php endif;?>

    <div class="container mt-5">
        <div id="primary" class="content-area">
            <main id="main" class="site-main" role="main">

                <?php
                while (have_posts()):
                    the_post();

                    do_action('storefront_page_before');

                    get_template_part('content', 'page');

                    /**
                     * Functions hooked in to storefront_page_after action
                     *
                     * @hooked storefront_display_comments - 10
                     */
                    do_action('storefront_page_after');

                endwhile; // End of the loop.
                ?>

            </main><!-- #main -->
        </div><!-- #primary -->
    </div>
<?php
do_action('storefront_sidebar');
get_footer('braexp');
