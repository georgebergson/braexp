<?php
/**
 * Displayed when no products are found matching the current query
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/no-products-found.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;

?>
<h3 class="woocommerce-info" style="background-color: #1F394D;"><?php esc_html_e( 'Ainda não temos nenhum serviço disponível nesta categoria. Em breve novos serviços serão disponibilizados!', 'woocommerce' ); ?></h3>

<div class="container pt-5 pb-5">
    <div class="row">
        <div class="col-md-4 mb-2">
            <strong>Apoio</strong>
            <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/uk.png">
        </div>
        <div class="col-md-8">
            <strong>Realização</strong>
            <!--<img src="/img/sebrae.png">-->
            <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/assinaturas_periodoeleitoral.png">
        </div>
    </div>
</div>
