<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo03" aria-controls="navbarTogglerDemo03" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <a class="navbar-brand" href="<?php echo get_site_url(); ?>">
            <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/BRAEXP_LogoPositiva-e1648824143235.png" class="img-fluid"></a>
        <div class="collapse navbar-collapse" id="navbarTogglerDemo03">
            <div class="container">
                <div class="row mb-0">
                    <div class="col-9">
                        <div class="row text-center mt-1 mb-2">
                            <div class="col border-start border-end ">
                                <a class="nav-link text-nowrap" aria-current="page" href="<?php echo get_site_url(); ?>/servicos-em-oferta/">Serviços em Oferta</a>
                            </div>
                            <div class="col border-end">
                                <a class="nav-link text-nowrap" href="<?php echo get_site_url(); ?>/cadastro-de-parceiro/">Seja Fornecedor</a>
                            </div>
                            <div class="col border-end">
                                <a class="nav-link" href="<?php echo get_site_url(); ?>/lancamentos/" tabindex="-1" aria-disabled="true">Lançamentos</a>
                            </div>
                            <div class="col border-end">
                                <a class="nav-link" href="<?php echo get_site_url(); ?>/artigos/" tabindex="-1" aria-disabled="true">Artigos</a>
                            </div>
                        </div>
                        <div class="row">
                            <form role="search" method="get" id="search-form" action="<?php echo esc_url(home_url('/')); ?>" class="d-flex me-lg-3 mb-3 mb-lg-0">
                                <div class="input-group">
                                    <input type="search" class="form-control border-0" placeholder="Buscar no site" aria-label="search nico" name="s" id="search-input" value="<?php echo esc_attr(get_search_query()); ?>">
                                    <div class="input-group-append d-grid gap-2">
                                        <button class="btn btn-secondary" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                        <div class="col-3 d-grid gap-1">
                            <a class="btn btn-warning me-2 text-light" href="<?php echo get_site_url(); ?>/carrinho/">
                                <i class="fa-solid fa-bag-shopping"></i> Minha Sacola
                            </a>
                            <a class="btn btn-success me-2 text-light" href="<?php echo get_site_url(); ?>/minha-conta/<?php echo (!is_user_logged_in() ? "?redirect=" . base64_encode($_SERVER['REQUEST_URI']) : ""); ?>">
                                <i class="fa fa-user"></i> 
                                <?php if (is_user_logged_in() === TRUE) {
                                        echo "Minha Conta";
                                        } else {
                                            echo "Fazer Login";
                                        } ?>
                            </a>
                        </div>
                </div>
            </div>
        </div>
    </div>
</nav>
<script>
    var $ = jQuery.noConflict();
    $(document).ready(function(){
        $('.dropdown-submenu a.multinivel').on("click", function(e){
            $(this).next('ul').toggle();
            e.stopPropagation();
            e.preventDefault();
        });
    });
</script>