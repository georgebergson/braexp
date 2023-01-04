<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo03" aria-controls="navbarTogglerDemo03" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <a class="navbar-brand" href="#"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/BRAEXP_LogoPositiva-e1648824143235.png" ></a>
        <div class="collapse navbar-collapse" id="navbarTogglerDemo03">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="<?php echo get_site_url();?>/servicos-em-oferta/">Serviços em Oferta</a>
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
                        <li><a class="dropdown-item" href="#">Crie seu plano de exportação</a></li>
                        <li><a class="dropdown-item" href="#">Envie o seu produto ou serviço</a></li>
                        <li><a class="dropdown-item" href="#">Financie e receba seu pagamento</a></li>
                        <li><a class="dropdown-item" href="#">Prepare-se para vender</a></li>
                        <li><a class="dropdown-item" href="#">Promova e venda</a></li>
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