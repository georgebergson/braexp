<?php get_header(); ?>


<div class="container mt-5">
    <div class="row">
        <?php
        $s=get_search_query();
        $args = array(
            's' =>$s
        );
        // The Query
        $the_query = new WP_Query( $args );
        if ( $the_query->have_posts() ) {
            _e("<h2 style='font-weight:bold;color:#000'>Resultados de busca para: ".get_query_var('s')."</h2>");
            while ( $the_query->have_posts() ) {
                $the_query->the_post();
                ?>
                    <div class="col-md-3 p-3">
                        <img src="<?php echo get_the_post_thumbnail_url(); ?>" class="img-thumbnail" width="304">
                        <h4 class="mt-3"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                        <a href="<?php the_permalink(); ?>" class="button product_type_variable mt-2">Mais Detalhes</a>

                    </div>

                <?php
            }
        }else{
            ?>
            <h2 style='font-weight:bold;color:#000'>Nada encontrado</h2>
            <div class="alert alert-info">
                <p>Desculpe, mas nada corresponde aos seus critérios de busca. Por favor, tente novamente com algumas palavras-chave diferentes.</p>
            </div>
        <?php } ?>
    </div>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>