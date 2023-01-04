<?php
/**
 * The template for displaying the homepage.
 *
 * This page template will display any functions hooked into the `homepage` action.
 * By default this includes a variety of product displays and the page content itself. To change the order or toggle these components
 * use the Homepage Control plugin.
 * https://wordpress.org/plugins/homepage-control/
 *
 * Template name: Homepage
 *
 * @package storefront
 */

get_header();?>
<div class="container-fluid" style="background: url('<?php echo get_stylesheet_directory_uri(); ?>/img/shutterstock_2077002754.jpg'); background-repeat: no-repeat; background-position: top;">
    <div class="container pt-5">
        <div class="row d-flex align-items-center">
            <div class="col-md-4 p-0" style="background-color: white; border-radius: 30px; -webkit-box-shadow: -5px 10px 15px -5px rgba(71,71,71,0.8);
box-shadow: -5px 10px 15px -5px rgba(71,71,71,0.8);">
                <?php include "inc/menu-categorias.php";?>
            </div>
            <div class="col-md-2 text-light align-items-center">
                    <h3 class="text-light fw-bold">Chegar longe nunca ficou tão perto</h3>
                <p class="fw-light">Tudo o que você precisa para exportar o seu produto/serviço em apenas um lugar.</p>
                <button type="button" class="btn btn-warning me-2 fw-bold mb-3">CONHEÇA O BRAEXP</button>
            </div>
            <div class="col"></div>
        </div>
    </div>
</div>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h3 class="fw-bold">Serviços Recomendados</h3>
            <?php echo do_shortcode("[wpcs id=1114]"); ?></div>
    </div>
</div>
<?php
include "inc/parceiro-novidades.php";
include "inc/artigos.inc";
include "inc/parallax.php";
?>
<script src="<?php echo get_stylesheet_directory_uri(); ?>/js/homepage.js"></script>
<?php
get_footer('braexp');