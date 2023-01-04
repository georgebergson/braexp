<?php

/* enqueue script for parent theme stylesheeet */
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js');
    wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css');
    wp_enqueue_style('open-sans', 'https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;700&display=swap');
    wp_enqueue_style('storefront-parent-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_script('fontawesome', 'https://kit.fontawesome.com/41806f53ae.js');
    wp_enqueue_script('js-cookie', 'https://cdn.jsdelivr.net/npm/js-cookie@3.0.1/dist/js.cookie.min.js', [], '3.0.1');
});

function wpb_woo_endpoint_title($title, $id)
{
    if (is_wc_endpoint_url('lost-password') && in_the_loop()) {
        $title = "Recuperação de senha";
    }
    return $title;
}

add_filter('the_title', 'wpb_woo_endpoint_title', 10, 2);


/*************** Deixar produto digital como padrão e remover produto transferível ***************/
//Define digital como padrão
add_filter( 'wcfm_product_manage_fields_general', function( $general_fields, $product_id, $product_type ) { 
	if( isset( $general_fields['is_virtual'] ) ) { 
		$general_fields['is_virtual']['dfvalue'] = 'enable'; 
	}

//Oculta produto transferível
	if( isset( $general_fields['is_downloadable'] ) ) { 
		$general_fields['is_downloadable']['class'] = 'wcfm_custom_hide'; 
		$general_fields['is_downloadable']['desc_class'] = 'wcfm_custom_hide'; 
	} 

//Oculta produto digital
	if( isset( $general_fields['is_virtual'] ) ) { 
		$general_fields['is_virtual']['class'] = 'wcfm_custom_hide'; 
		$general_fields['is_virtual']['desc_class'] = 'wcfm_custom_hide'; 
	} 
	return $general_fields;
}, 50, 3 );


/*************** Deixar produto digital como padrão e remover produto transferível ***************/
//Define digital como padrão
add_filter( 'wcfm_product_manage_fields_general', function( $general_fields, $product_id, $product_type ) { 
	if( isset( $general_fields['is_virtual'] ) ) { 
		$general_fields['is_virtual']['dfvalue'] = 'enable'; 
	}
//Oculta produto transferível
	if( isset( $general_fields['is_downloadable'] ) ) { 
		$general_fields['is_downloadable']['class'] = 'wcfm_custom_hide'; 
		$general_fields['is_downloadable']['desc_class'] = 'wcfm_custom_hide'; 
	} 
//Oculuta produto digital
	if( isset( $general_fields['is_virtual'] ) ) { 
		$general_fields['is_virtual']['class'] = 'wcfm_custom_hide'; 
		$general_fields['is_virtual']['desc_class'] = 'wcfm_custom_hide'; 
	} 
	return $general_fields;
}, 50, 3 );


/*************** Remover campo cobrado para transporte em [Marketplace]>[Relatórios] ***************/

add_filter( 'wcfm_admin_sales_report_legends', 'gth_ced_remove_shipping_from_reports' );
function gth_ced_remove_shipping_from_reports($legend) {
    foreach ($legend as $key => $value) {
        if($key == 5) {
            unset($legend[$key]);
        }
    }
    return $legend;
}


/*************** Redirecionamento do usuário para a página anterior após login ou cadastro ***************/
add_filter('woocommerce_login_redirect', 'gth_wc_login_redirect', 9999, 2); 
add_filter('woocommerce_registration_redirect', 'gth_wc_login_redirect', 9999, 2);
function gth_wc_login_redirect($uri, $user = null) {

    if(empty($user)) {
        $user = wp_get_current_user();
    }

    if($user->has_cap('customer')) {
        if(isset($_GET['redirect'])) {
            $uri =  base64_decode($_GET['redirect']);
        }
    }
    else {
        $uri = str_contains($_SERVER['HTTP_HOST'], 'localhost') ? '/gth/login' : '/login';
    }

    return $uri;
}

/*************** Remover campos do Detalhes de faturamento e Informação adicional no checkout ***************/
//Detalhes de faturamento
add_filter( 'woocommerce_checkout_fields' , 'remove_billing_fields_from_checkout' );
function remove_billing_fields_from_checkout( $fields ) {
    $fields[ 'billing' ] = array();
    return $fields;
}

//Informação adicional
add_filter( 'woocommerce_enable_order_notes_field', '__return_false' );


/*************** Traduzir o botão Clear All do plugin woof ***************/

function change_translate_text( $translated_text ) {   
	if ( 'Clear All'  === $translated_text ) {
        $translated_text = 'Limpar todos os Filtros';
    }
    return $translated_text;
}
add_filter( 'gettext', 'change_translate_text', 20 );


/*************** Remover Serviços relacionados automáticos ***************/

remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );


/*************** remover páginas das buscas, deixando apenas produtos e posts (artigos) ***************/

function remove_pages_from_search() {
    global $wp_post_types;
    $wp_post_types['page']->exclude_from_search = true;
}
add_action('init', 'remove_pages_from_search');