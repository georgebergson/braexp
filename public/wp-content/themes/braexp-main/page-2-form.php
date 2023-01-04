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
            <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/header_login.jpg" width="<?php echo absint(get_custom_header()->width); ?>" height="<?php echo absint(get_custom_header()->height); ?>" alt="<?php echo esc_attr(get_bloginfo('name', 'display')); ?>" class="mx-auto d-block">
        </a>
    </div>
<?php endif;?>

    <div class="container mt-5">
        <div id="primary" class="content-area">
            <main id="main" class="site-main" role="main">

                <?php echo do_shortcode('[gth id="2"]');?>

            </main><!-- #main -->
        </div><!-- #primary -->
    </div>
    <script src="<?php echo get_stylesheet_directory_uri(); ?>/js/autocomplete_cnpj.js"></script>
<?php
do_action('storefront_sidebar');
get_footer('braexp');