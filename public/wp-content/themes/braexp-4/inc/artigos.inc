<div class="container">
    <div class="row g-3 mb-5">
        <h3 class="fw-bold mb-4">Notícias para empresas que querem exportar</h3>
        <?php
        $primeiro_posts = wp_get_recent_posts(array('posts_per_page'   => 1));
        foreach( $primeiro_posts as $post_item ) : ?>
        <div class="col-md-8" style="height: 100%;">
        <div style="background: url('<?php echo get_the_post_thumbnail_url($post_item['ID'], 'full'); ?>') no-repeat rgba(88,88,88,0.3); min-height: 300px; background-size: cover; background-blend-mode: multiply;" class="rounded-3 p-3">
            <p class="text-white">Há 2 horas em <u>oportunidade</u></p>
                <a href="<?php echo get_permalink($post_item['ID']) ?>">
                    <h4 class="text-white fw-bold w-75"><?php echo $post_item['post_title'] ?></h4>
                </a>
            <a href="<?php echo get_permalink($post_item['ID']) ?>">
                <p class="text-white">Leia a matéria completa ></p>
            </a>
            <?php endforeach; ?>
        </div></div>

        <?php
        $recent_posts = wp_get_recent_posts(array(
            'numberposts' => 4, 'offset' => 1,
            'post_status' => 'publish'
        ));
        foreach( $recent_posts as $post_item ) : ?>
        <div class="col-md-4">
            <div style="background: url('<?php echo get_the_post_thumbnail_url($post_item['ID'], 'full'); ?>') no-repeat rgba(88,88,88,0.3); min-height: 300px; background-size: cover; background-blend-mode: multiply; height: 100%;" class="rounded-3 p-3">
                <p class="text-white">Há 2 horas em <u>oportunidade</u></p>
                <a href="<?php echo get_permalink($post_item['ID']) ?>">
                    <h4 class="text-white fw-bold w-75"><?php echo $post_item['post_title'] ?></h4>
                </a>
                <a href="<?php echo get_permalink($post_item['ID']) ?>">
                    <p class="text-white">Saiba mais ></p>
                </a>
            </div>
        </div>
        <?php endforeach; ?>

    </div>
</div>





