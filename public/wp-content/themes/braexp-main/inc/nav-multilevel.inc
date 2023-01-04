<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo03" aria-controls="navbarTogglerDemo03" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <a class="navbar-brand" href="<?php echo get_site_url(); ?>"><img src="<?php echo get_site_url(); ?>/wp-content/uploads/2022/04/BRAEXP_LogoPositiva-e1648824143235.png" ></a>
        <div class="collapse navbar-collapse" id="navbarTogglerDemo03">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="#">Serviços em Oferta</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Seja Fornecedor</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" tabindex="-1" aria-disabled="true">Lançamentos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" tabindex="-1" aria-disabled="true">Artigos</a>
                </li>
                <li class="nav-item dropdown d-block d-sm-none">
                    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">Categorias</a>
                    <ul class="dropdown-menu">
                        <li class="dropdown-submenu">
                            <a class="multinivel dropdown-toggle text-decoration-none" href="#">Crie seu plano de exportação</a>
                            <ul class="dropdown-menu">
                                <li><a href="#" class="dropdown-item">Avaliação da capacidade de exportação</a></li>
                                <li><a href="#" class="dropdown-item">Instalação internacional</a></li>
                                <li><a href="#" class="dropdown-item">Mercado-alvo</a></li>
                                <li><a href="#" class="dropdown-item">Planejamento para exportação</a></li>
                                <li><a href="#" class="dropdown-item">Regulamentações, acordos e barreiras</a></li>
                            </ul>
                        </li>
                        <li class="dropdown-submenu">
                            <a class="multinivel dropdown-toggle text-decoration-none" href="#">Envie o seu produto ou serviço</a>
                            <ul class="dropdown-menu">
                                <li><a href="#" class="dropdown-item">Armazenagem e outros</a></li>
                                <li><a href="#" class="dropdown-item">Documentação logística</a></li>
                                <li><a href="#" class="dropdown-item">Frete</a></li>
                                <li><a href="#" class="dropdown-item">Procedimentos aduaneiros</a></li>
                            </ul>
                        </li>
                        <li class="dropdown-submenu">
                            <a class="multinivel dropdown-toggle text-decoration-none" href="#">Financie e receba seu pagamento</a>
                            <ul class="dropdown-menu">
                                <li><a href="#" class="dropdown-item">Câmbio</a></li>
                                <li><a href="#" class="dropdown-item">Formas de Pagamento</a></li>
                                <li><a href="#" class="dropdown-item">Linhas de Financiamento</a></li>
                                <li><a href="#" class="dropdown-item">Riscos, garantias e seguros</a></li>
                            </ul>
                        </li>
                        <li class="dropdown-submenu">
                            <a class="multinivel dropdown-toggle text-decoration-none" href="#">Prepare-se para vender</a>
                            <ul class="dropdown-menu">
                                <li><a href="#" class="dropdown-item">Marketing</a></li>
                                <li><a href="#" class="dropdown-item">Preço</a></li>
                                <li><a href="#" class="dropdown-item">Produto</a></li>
                            </ul>
                        </li>
                        <li class="dropdown-submenu">
                            <a class="multinivel dropdown-toggle text-decoration-none" href="#">Promova e venda</a>
                            <ul class="dropdown-menu">
                                <li><a href="#" class="dropdown-item">Aspectos jurídicos e tributários</a></li>
                                <li><a href="#" class="dropdown-item">Diretório de compradores e parceiros<br> comerciais</a></li>
                                <li><a href="#" class="dropdown-item">Feiras e eventos</a></li>
                                <li><a href="#" class="dropdown-item">Marketplaces</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>
            </ul>
            <form class="d-flex me-lg-3 mb-3 mb-lg-0">
                <input class="form-control me-2" type="search" placeholder="Buscar no site" aria-label="Search">
                <button class="btn" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>
            <div>
                <button type="button" class="btn btn-warning me-2 text-light"><i class="fa-solid fa-bag-shopping"></i>&nbsp;Minha sacola</button>
                <button type="button" class="btn btn-success">Fazer login</button>
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