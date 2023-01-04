<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package storefront
 */

?>


</div><!-- #content -->

<?php do_action('storefront_before_footer');?>

<footer id="colophon" class="site-footer pt-5" role="contentinfo" style="background-color: #1F394D;">
    <div class="container">
        <div class="row">
            <div class="col-md-3 mb-3">
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/BRAEXP_LogoPositiva-e1648824143235.png">
            </div>
            <div class="col-md-3 mb-3">
                <ul class="list-unstyled m-0">
                    <li><a class="text-decoration-none text-light" href="<?php echo get_site_url(); ?>"><small>Principal</small></a></li>
                    <li><a class="text-decoration-none text-light" href="<?php echo get_site_url(); ?>/cadastro-de-parceiro/"><small>Seja fornecedor</small></a></li>
                    <li><a class="text-decoration-none text-light" href="<?php echo get_site_url(); ?>/servicos-em-oferta/"><small>Serviços em oferta</small></a></li>
                    <li><a class="text-decoration-none text-light" href="<?php echo get_site_url(); ?>/artigos/"><small>Artigos</small></a></li>
                    <li><a class="text-decoration-none text-light" href="<?php echo get_site_url(); ?>/lancamentos/"><small>Lançamentos</small></a></li>
                    <li><a class="text-decoration-none text-light" href="<?php echo get_site_url(); ?>/contato/"><small>Entre em Contato</small></a></li>
                </ul>
            </div>
            <div class="col-md-3 mb-3">
                <ul class="list-unstyled m-0">
                    <li class="d-flex mb-2"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/Icone_menu_1-1.png">
                        <a class="text-decoration-none text-light" href="<?php echo get_site_url(); ?>/categoria-produto/crie-seu-plano-de-exportacao/">
                            <span class="ms-2"><small>Crie seu plano de exportação</small></span>
                        </a></li>
                    <li class="d-flex mb-2"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/Icone_menu-2.png">
                        <a class="text-decoration-none text-light" href="<?php echo get_site_url(); ?>/categoria-produto/prepare-se-para-vender/">
                            <span class="ms-2"><small>Prepare-se para vender</small></span>
                        </a></li>
                    <li class="d-flex mb-2"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/Icone_menu.png">
                        <a class="text-decoration-none text-light" href="<?php echo get_site_url(); ?>/categoria-produto/promova-e-venda/">
                            <span class="ms-2"><small>Promova sua venda</small></span>
                        </a></li>
                    <li class="d-flex mb-2"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/Icone_menu_2-1.png">
                        <a class="text-decoration-none text-light" href="<?php echo get_site_url(); ?>/categoria-produto/envie-seu-produto-ou-servico/">
                            <span class="ms-2"><small>Envie seu produto ou serviço</small></span>
                        </a></li>
                    <li class="d-flex mb-2"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/Icone_menu_3-1.png">
                        <a class="text-decoration-none text-light" href="<?php echo get_site_url(); ?>/categoria-produto/financie-e-receba-seu-pagamento/">
                            <span class="ms-2"><small>Financie e receba seu pagamento</small></span>
                        </a></li>


                </ul>
            </div>
            <div class="col-md-3 text-light">
                <a href="<?php echo get_site_url() ?>/minha-conta/"><button type="button" class="btn me-2 fw-bold mb-3 text-light" style="background-color: #03a5aa;"><?php if ( is_user_logged_in() === TRUE ) { echo "MINHA CONTA"; } else { echo "FAZER LOGIN"; }?></button></a><br>
                Copyright &copy; BRAEXP<br>
                Todos os direitos reservados.
            </div>
        </div>
    </div>
    <!-- .col-full -->
</footer><!-- #colophon -->
<?php do_action('storefront_after_footer');?>
</div><!-- #page -->
<?php wp_footer();?>
<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
-->
</body>
</html>