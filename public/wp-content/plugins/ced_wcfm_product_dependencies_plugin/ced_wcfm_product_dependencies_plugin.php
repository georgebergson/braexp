<?php

/**
 * Plugin Name:       Ced WCFM Product Dependencies Plugin  
 * Plugin URI:        https://cedcommerce.com/
 * Description:       Restrict access to WCFM products, depending on the ownership and/or purchase of other, required products.
 * Version:           1.0.0
 * Requires at least: 5.2 
 * Author:            Cedcommerce
 * Author URI:        https://cedcommerce.com/
 * Text Domain:       ced-wcfm-product-dependencies 
 * Domain Path:       /languages
 */
//include_once(ABSPATH . 'wp-admin/includes/plugin.php');
 include_once(ABSPATH . 'wp-content/plugins/woocommerce-product-dependencies/class-wc-pd-core-compatibility.php');
 include_once(ABSPATH . 'wp-content/plugins/woocommerce-product-dependencies/class-wc-pd-helpers.php');
 include_once(ABSPATH . 'wp-content/plugins/woocommerce-product-dependencies/woocommerce-product-dependencies.php');

 
     /**
     * 'Ownership' dependency type code.
     */
    const DEPENDENCY_TYPE_OWNERSHIP = 1;

    /**
     * 'Purchase' dependency type code.
     */
    const DEPENDENCY_TYPE_PURCHASE = 2;

    /**
     * 'Either' dependency type code.
     */
    const DEPENDENCY_TYPE_EITHER = 3;


add_action( 'wp_enqueue_scripts', 'ced_select2_enqueue' );
function ced_select2_enqueue(){
    $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    if (preg_match('/\bproducts-manage\b/', $actual_link)) {
   
    wp_enqueue_style('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.3/css/select2.min.css' );
    wp_enqueue_script('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.3/js/select2.min.js', array('jquery') );
    wp_enqueue_script('ced_custom_js',plugin_dir_url(__FILE__) . 'ced_custom.js', array('jquery','select2'),'',true );
   }
}


add_filter( 'wcfm_product_manage_fields_linked','ced_add_dependency_fields', 50, 3 );
function ced_add_dependency_fields($fields, $product_id, $product_type) {
    global $WCFM;
    $get_login_user_id = get_current_user_id();
    $vendor_products = $WCFM->wcfm_vendor_support->wcfm_get_products_by_vendor($get_login_user_id);
    $get_categories = get_terms('product_cat');
    $all_products_arr = array(); 
    $categories = array();
   

    if( is_array($vendor_products) && !empty($vendor_products) ){
        foreach($vendor_products as $value){
            if($value->ID == $product_id){
                continue;
            }
            $all_products_arr[$value->ID] = $value->post_title;
        }
    }

    if( is_array($get_categories) && !empty($get_categories) ){
        foreach($get_categories as $value){
            $categories[$value->term_id] = $value->name;
        }
    }
    
    
    $product_dependency = get_post_meta($product_id,'ced_product_dependency_drop',true);
    $product_dependency_multi_select_drop = get_post_meta($product_id,'ced_product_dependency_muti_select_drop',true);
    $product_dep_type = get_post_meta($product_id,'ced_product_dependency_type_drop',true);
    $category_depen_drop = get_post_meta($product_id,'ced_category_dependency_drop',true);
    $custom_notice = get_post_meta($product_id,'ced_product_custom_notice',true);    

                                                
    
    $fields['product_dependencies'] = array(
                                                'label' => __('Product Dependencies', 'wc-frontend-manager') ,
                                                'type' => 'select', 
                                                'options' => array(
                                                    '' => __('-Select Dependencies-', 'wc-frontend-manager'), 
                                                    'product_ids' => __('Select products', 'wc-frontend-manager'),  
                                                    'category_ids' => __('Select categories','wc-frontend-manager')
                                                ),
                                                'class' => 'wcfm-select wcfm_ele simple variable booking',
                                                'label_class' => 'wcfm_title',
                                                'name' => 'product_dependencies_dropdown',
                                                'default' => 'product_ids',
                                                'value' => $product_dependency,
                            );
    $fields['product_dependencies_select_drop']  = array(
                                                'label' => __('Select Products', 'wc-frontend-manager') ,
                                                'type' => 'select',
                                                'value' => $product_dependency_multi_select_drop, 
                                                'options' => $all_products_arr,
                                                'attributes' => array( 'multiple' => true,'class' => 'ced_product_multi' ),
                                                'class' => 'wcfm-select wcfm_ele simple variable external grouped booking product_dependencies_select_drop_ele',
                                                'label_class' => 'wcfm_title',
                                                'name' => 'tied_products',
                                            );
     $fields['category_dependencies_select_drop']  = array(
                                                'label' => __('Select Categories', 'wc-frontend-manager') ,
                                                'type' => 'select',
                                                'value' => $category_depen_drop, 
                                                'options' => $categories,
                                                'attributes' => array( 'multiple' => true,'class' => 'ced_category_multi' ),
                                                'class' => 'wcfm-select wcfm_ele simple variable external grouped booking category_dependencies_select_drop_ele',
                                                'label_class' => 'wcfm_title',
                                                'name' => 'tied_categories',
                                            );

    $fields['dependency_type']  = array(
                                                'label' => __('Dependency type', 'wc-frontend-manager') ,
                                                'type' => 'select', 
                                                'options' => array(
                                                    '' => __('-Select Dependency Type-', 'wc-frontend-manager'), 
                                                    '1' => __('Ownership', 'wc-frontend-manager'),  
                                                    '2' => __('Purchase','wc-frontend-manager'),
                                                    '3' => __('Either','wc-frontend-manager')
                                                ),
                                                'class' => 'wcfm-select wcfm_ele simple variable booking',
                                                'label_class' => 'wcfm_title',
                                                'name' => 'dependency_type',
                                                'value' => $product_dep_type,
                            );

    $fields['custom_notice']  = array(
                                                'label' => __('Custom notice', 'wc-frontend-manager') ,
                                                'type' => 'textarea', 
                                                'class' => 'wcfm-textarea wcfm_ele simple variable booking',
                                                'label_class' => 'wcfm_title',
                                                'name' => 'dependency_notice',
                                                'value' => $custom_notice,
                            );
     
    return $fields; 
   
}

add_action( 'after_wcfm_products_manage_meta_save', 'ced_custom_product_dependency_drop_data_save',10,2 );
function ced_custom_product_dependency_drop_data_save($new_product_id, $wcfm_products_manage_form_data) {
    global $wpdb, $WCFM, $_POST;
    $pdd_value = $wcfm_products_manage_form_data['product_dependencies_dropdown'];
    $pdsd_value = $wcfm_products_manage_form_data['tied_products'];
    $dt_value = $wcfm_products_manage_form_data['dependency_type'];
    $cnotice_value = $wcfm_products_manage_form_data['dependency_notice'];
    $category_value = $wcfm_products_manage_form_data['tied_categories'];


    if(isset($pdd_value)) {
        update_post_meta($new_product_id,'ced_product_dependency_drop',$pdd_value);
    }

    if(isset($pdsd_value) && !empty($pdsd_value)) {
        update_post_meta($new_product_id,'ced_product_dependency_muti_select_drop',$pdsd_value);
    }else{
        update_post_meta($new_product_id,'ced_product_dependency_muti_select_drop','');
    }

    if(isset($dt_value)) {
        update_post_meta($new_product_id,'ced_product_dependency_type_drop',$dt_value);
    }

    if(isset($cnotice_value)) {
        update_post_meta($new_product_id,'ced_product_custom_notice',$cnotice_value);
    }

    if(isset($category_value) && !empty($category_value)) {
        update_post_meta($new_product_id,'ced_category_dependency_drop',$category_value);
    }else{
        update_post_meta($new_product_id,'ced_category_dependency_drop','');
    }
}


add_filter( 'woocommerce_add_to_cart_validation', 'add_to_cart_validation' , 10, 3 );
function add_to_cart_validation( $add, $item_id, $quantity ) {    
    return $add && evaluate_dependencies( $item_id );
}


add_action( 'woocommerce_check_cart_items',  'check_cart_items' , 1 );

 function check_cart_items() {

    $cart_items = WC()->cart->cart_contents;

    foreach ( $cart_items as $cart_item ) {
        $product = $cart_item[ 'data' ];
        evaluate_dependencies( $product );
    }
}


function evaluate_dependencies( $item ) {

        if ( is_a( $item, 'WC_Product' ) ) {

            if ( $item->is_type( 'variation' ) ) {
                $product_id = WC_PD_Core_Compatibility::get_parent_id( $item );
                $product    = wc_get_product( $product_id );
            } else {
                $product_id = WC_PD_Core_Compatibility::get_id( $item );
                $product    = $item;
            }

        } else {
            $product_id = absint( $item );
            $product    = wc_get_product( $product_id );
        }

        if ( ! $product ) {
            return;
        }

        $tied_product_ids          = get_tied_product_ids( $product );
        $tied_category_ids         = get_tied_category_ids( $product );
        $dependency_selection_type = get_dependency_selection_type( $product );
        $dependency_notice         = get_dependency_notice( $product );

        $product_title      = $product->get_title();
        $tied_products      = array();
        $dependencies_exist = false;

        // Ensure dependencies exist.
        if ( 'product_ids' === $dependency_selection_type ) {

            if ( ! empty( $tied_product_ids ) ) {
                foreach ( $tied_product_ids as $id ) {

                    $tied_product = wc_get_product( $id );

                    if ( $tied_product ) {
                        $tied_products[ $id ] = $tied_product;
                        $dependencies_exist   = true;
                    }
                }
            }

        } else {

            if ( ! empty( $tied_category_ids ) ) {

                $product_categories   = (array) get_terms( 'product_cat', array( 'get' => 'all' ) );
                $product_category_ids = wp_list_pluck( $product_categories, 'term_id' );
                $tied_category_ids    = array_intersect( $product_category_ids, $tied_category_ids );
                $dependencies_exist   = sizeof( $tied_category_ids ) > 0;
            }
        }

        if ( $dependencies_exist ) {

            $purchase_dependency_result  = false;
            $ownership_dependency_result = false;

            $purchased_product_ids = array();
            $purchased_cat_ids     = array();

            $dependency_type         = get_dependency_type( $product );
            $dependency_relationship = get_dependency_relationship( $product );

            $tied_product_ids = array_keys( $tied_products );
            $has_multiple     = 'product_ids' === $dependency_selection_type ? sizeof( $tied_products ) > 1 : sizeof( $tied_category_ids ) > 1;
            $tied_ids         = 'product_ids' === $dependency_selection_type ? $tied_product_ids : $tied_category_ids;
            $owned_ids        = array();

            // Check cart.
            if ( in_array( $dependency_type, array( DEPENDENCY_TYPE_PURCHASE, DEPENDENCY_TYPE_EITHER ) ) ) {

                $cart_contents = WC()->cart->cart_contents;
                $item_id       = $product_id;

                foreach ( $cart_contents as $cart_item ) {

                    $product_id   = $cart_item[ 'product_id' ];
                    $variation_id = $cart_item[ 'variation_id' ];

                    if ( $product_id === $item_id || $variation_id === $item_id ) {
                        continue;
                    }

                    if ( 'product_ids' === $dependency_selection_type ) {

                        if ( in_array( $product_id, $tied_product_ids ) || in_array( $variation_id, $tied_product_ids ) ) {

                            if ( 'or' === $dependency_relationship ) {
                                $purchase_dependency_result = true;
                                break;
                            } else {
                                $purchased_product_ids = array_unique( array_merge( $purchased_product_ids, array_filter( array( $product_id, $variation_id ) ) ) );
                            }
                        }

                    } else {

                        if ( WC_PD_Core_Compatibility::is_wc_version_gte_2_7() ) {
                            $cart_item_product = $cart_item[ 'data' ]->is_type( 'variation' ) ? wc_get_product( $cart_item[ 'data' ]->get_parent_id() ) : $cart_item[ 'data' ];
                            $cart_item_cat_ids = $cart_item_product->get_category_ids();
                        } else {
                            $cart_item_cat_ids = get_terms( array(
                                'taxonomy'   => 'product_cat',
                                'object_ids' => $cart_item[ 'product_id' ],
                                'fields'     => 'ids'
                            ) );
                        }

                        $matching_cat_ids  = array_intersect( $cart_item_cat_ids, $tied_category_ids );

                        if ( sizeof( $matching_cat_ids ) ) {

                            if ( 'or' === $dependency_relationship ) {
                                $purchase_dependency_result = true;
                                break;
                            } else {
                                $purchased_cat_ids     = array_unique( array_merge( $purchased_cat_ids, array_filter( $matching_cat_ids ) ) );
                                $purchased_product_ids = array_unique( array_merge( $purchased_product_ids, array_filter( array( $product_id, $variation_id ) ) ) );
                            }
                        }
                    }
                }

                $purchased_ids = 'product_ids' === $dependency_selection_type ? $purchased_product_ids : $purchased_cat_ids;

                if ( 'and' === $dependency_relationship ) {
                    if ( sizeof( $purchased_ids ) >= sizeof( $tied_ids ) ) {
                        $purchase_dependency_result = true;
                    }
                }
            }

            // Check ownership.
            if ( in_array( $dependency_type, array( DEPENDENCY_TYPE_OWNERSHIP, DEPENDENCY_TYPE_EITHER ) ) ) {

                if ( is_user_logged_in() ) {

                    $current_user = wp_get_current_user();

                    if ( 'category_ids' === $dependency_selection_type ) {
                        $tied_product_ids = get_product_ids_in_categories( $tied_category_ids );
                    }

                    if ( $dependency_type === DEPENDENCY_TYPE_EITHER && $purchase_dependency_result ) {

                        $ownership_dependency_result = true;

                    } else {

                        $owned_product_ids = customer_bought_products( $current_user->user_email, $current_user->ID, $tied_product_ids );

                        // Find all categories that these products belong to and then compare against the set of required categories.
                        if ( 'and' === $dependency_relationship && $has_multiple ) {

                            if ( $dependency_type === DEPENDENCY_TYPE_EITHER ) {
                                $owned_product_ids = array_unique( array_merge( $owned_product_ids, $purchased_product_ids ) );
                            }

                            if ( 'product_ids' === $dependency_selection_type ) {
                                $owned_ids = $owned_product_ids;
                            } else {
                                $owned_ids = array_unique( wp_get_object_terms( $owned_product_ids, 'product_cat', array( 'fields' => 'ids' ) ) );
                            }

                            $owned_ids = array_intersect( $owned_ids, $tied_ids );

                            $ownership_dependency_result = sizeof( $owned_ids ) >= sizeof( $tied_ids );

                            if ( $ownership_dependency_result ) {
                                $purchase_dependency_result = true;
                            }

                        } else {
                            $ownership_dependency_result = sizeof( $owned_product_ids );
                        }
                    }
                }
            }

            $result = $ownership_dependency_result || $purchase_dependency_result;

            // Show notice.
            if ( false === $result ) {

                if ( ! WC()->session->has_session() ) {
                    // Generate a random customer ID.
                    WC()->session->set_customer_session_cookie( true );
                }

                if ( $dependency_notice ) {

                    wc_add_notice( $dependency_notice, 'error' );

                } else {

                    if ( 'product_ids' === $dependency_selection_type ) {

                        $required_msg = WC_PD_Helpers::merge_product_titles( $tied_products, $dependency_relationship );

                        if ( $has_multiple ) {
                            if ( 'and' === $dependency_relationship ) {
                                $action_msg   = __( 'all required products', 'woocommerce-product-dependencies' );
                                $action_msg_2 = __( 'these products', 'woocommerce-product-dependencies' );
                            } else {
                                $action_msg = __( 'a required product', 'woocommerce-product-dependencies' );
                            }
                        } else {
                            $action_msg = $required_msg;
                        }

                    } else {

                        $merged_category_titles = WC_PD_Helpers::merge_category_titles( $tied_category_ids, $dependency_relationship );

                        if ( $has_multiple ) {
                            if ( 'and' === $dependency_relationship ) {
                                $required_msg = sprintf( __( 'one or more products from the %s categories', 'woocommerce-product-dependencies' ), $merged_category_titles );
                                $action_msg   = __( 'one or more products from all required categories', 'woocommerce-product-dependencies' );
                            } else {
                                $required_msg = sprintf( __( 'a product from the %s category', 'woocommerce-product-dependencies' ), $merged_category_titles );
                                $action_msg   = __( 'a qualifying product', 'woocommerce-product-dependencies' );
                            }
                        } else {
                            $required_msg = sprintf( __( 'a product from the %s category', 'woocommerce-product-dependencies' ), $merged_category_titles );
                            $action_msg   = $required_msg;
                        }
                    }

                    if ( $dependency_type === DEPENDENCY_TYPE_OWNERSHIP ) {

                        if ( is_user_logged_in() ) {
                            $msg = __( 'Access to &quot;%1$s&quot; is restricted to customers who have previously purchased %2$s.', 'woocommerce-product-dependencies' );
                        } else {
                            $msg = __( 'Access to &quot;%1$s&quot; is restricted to customers who have previously purchased %2$s. Please <a href="%3$s">log in</a> to validate ownership and try again.', 'woocommerce-product-dependencies' );
                        }

                        wc_add_notice( sprintf( $msg, $product_title, $required_msg, wp_login_url() ), 'error' );

                    } elseif ( $dependency_type === DEPENDENCY_TYPE_EITHER ) {

                        if ( is_user_logged_in() ) {

                            if ( 'and' === $dependency_relationship && $has_multiple && sizeof( $owned_ids ) ) {

                                if ( 'product_ids' === $dependency_selection_type ) {

                                    $owned_msg  = WC_PD_Helpers::merge_product_titles( array_intersect_key( $tied_products, array_flip( $owned_product_ids ) ), 'and' );
                                    $action_msg = WC_PD_Helpers::merge_product_titles( array_intersect_key( $tied_products, array_flip( array_diff( $tied_ids, $owned_product_ids ) ) ), 'and' );

                                } else {

                                    $owned_category_titles = WC_PD_Helpers::merge_category_titles( $owned_ids, 'and' );
                                    $owned_products_msg    = _n( 'a product', 'some products', sizeof( $owned_product_ids ), 'woocommerce-product-dependencies' );
                                    $owned_msg             = sprintf( _n( '%1$s from the %2$s category', '%1$s from the %2$s categories', sizeof( $owned_category_titles ), 'woocommerce-product-dependencies' ), $owned_products_msg, $owned_category_titles );

                                    $action_category_titles = WC_PD_Helpers::merge_category_titles( array_diff( $tied_ids, $owned_ids ), 'and' );
                                    $action_msg             = sprintf( _n( 'one or more products from the %s category', 'one or more products from the %s categories', sizeof( $action_category_titles ), 'woocommerce-product-dependencies' ), $action_category_titles );
                                }

                                $msg = __( '&quot;%1$s&quot; requires purchasing %2$s. Please add %3$s to your cart and try again (you have already purchased %4$s).', 'woocommerce-product-dependencies' );

                                wc_add_notice( sprintf( $msg, $product_title, $required_msg, $action_msg, $owned_msg ), 'error' );

                            } else {

                                $msg = __( '&quot;%1$s&quot; requires purchasing %2$s. To get access to this product now, please add %3$s to your cart.', 'woocommerce-product-dependencies' );

                                wc_add_notice( sprintf( $msg, $product_title, $required_msg, $action_msg ), 'error' );
                            }

                        } else {

                            $msg = __( '&quot;%1$s&quot; requires purchasing %2$s. If you have previously purchased %3$s, please <a href="%5$s">log in</a> to verify ownership and try again. Alternatively, get access to &quot;%1$s&quot; now by adding %4$s to your cart.', 'woocommerce-product-dependencies' );

                            wc_add_notice( sprintf( $msg, $product_title, $required_msg, isset( $action_msg_2 ) ? $action_msg_2 : $action_msg, $action_msg, wp_login_url() ), 'error' );
                        }

                    } else {

                        $msg = __( '&quot;%1$s&quot; is only available in combination with %2$s. To purchase this product, please add %3$s to your cart.', 'woocommerce-product-dependencies' );

                        wc_add_notice( sprintf( $msg, $product_title, $required_msg, $action_msg ), 'error' );
                    }
                }
            }

            return $result;
        }

        return true;
    }



function get_dependency_relationship( $product ) {
        return apply_filters( 'wc_pd_dependency_relationship', 'or', $product );
}


function get_tied_product_ids( $product ) {

    if ( WC_PD_Core_Compatibility::is_wc_version_gte_2_7() ) {
        $dependent_ids = $product->get_meta( 'ced_product_dependency_muti_select_drop', true );
    } else {
        $dependent_ids = (array) get_post_meta( $product->id, 'ced_product_dependency_muti_select_drop', true );
    }

    return empty( $dependent_ids ) ? array() : array_unique( $dependent_ids );
}


function get_tied_category_ids( $product ) {

    if ( WC_PD_Core_Compatibility::is_wc_version_gte_2_7() ) {
        $category_ids = $product->get_meta( 'ced_category_dependency_drop', true );
    } else {
        $category_ids = (array) get_post_meta( $product->id, 'ced_category_dependency_drop', true );
    }

    return empty( $category_ids ) ? array() : array_unique( $category_ids );
}

function get_dependency_selection_type( $product ) {

    if ( WC_PD_Core_Compatibility::is_wc_version_gte_2_7() ) {
        $selection_type = $product->get_meta( 'ced_product_dependency_drop', true );
    } else {
        $selection_type = get_post_meta( $product->id, 'ced_product_dependency_drop', true );
    }

    $selection_type = in_array( $selection_type, array( 'product_ids', 'category_ids' ) ) ? $selection_type : 'product_ids';

    return $selection_type;
}

function get_dependency_notice( $product ) {

    if ( WC_PD_Core_Compatibility::is_wc_version_gte_2_7() ) {
        $notice = $product->get_meta( 'ced_product_custom_notice', true );
    } else {
        $notice = get_post_meta( $product->id, 'ced_product_custom_notice', true );
    }

    return $notice;
}

function get_dependency_type( $product ) {

    if ( WC_PD_Core_Compatibility::is_wc_version_gte_2_7() ) {
        $type = absint( $product->get_meta( 'ced_product_dependency_type_drop', true ) );
    } else {
        $type = absint( get_post_meta( $product->id, 'ced_product_dependency_type_drop', true ) );
    }

    $type = in_array( $type, array( DEPENDENCY_TYPE_OWNERSHIP, DEPENDENCY_TYPE_PURCHASE, DEPENDENCY_TYPE_EITHER ) ) ? $type : DEPENDENCY_TYPE_EITHER;
    return $type;
}

function get_product_ids_in_categories( $category_ids ) {

        $query_results = new WP_Query( array(
            'post_type'   => array( 'product', 'product_variation' ),
            'fields'      => 'ids',
            'tax_query'   => array(
                array(
                    'taxonomy' => 'product_cat',
                    'field'    => 'term_id',
                    'terms'    => $category_ids,
                    'operator' => 'IN',
                )
            )
        ) );

        return $query_results->posts;
    }


function customer_bought_products( $customer_email, $user_id, $product_ids ) {

        global $wpdb;

        $results = apply_filters( 'wc_pd_pre_customer_bought_products', null, $customer_email, $user_id, $product_ids );

        if ( null !== $results ) {
            return $results;
        }

        $transient_name = 'wc_cbp_' . md5( $customer_email . $user_id . WC_Cache_Helper::get_transient_version( 'orders' ) );

        if ( false === ( $results = get_transient( $transient_name ) ) ) {

            $customer_data = array( $user_id );

            if ( $user_id ) {

                $user = get_user_by( 'id', $user_id );

                if ( isset( $user->user_email ) ) {
                    $customer_data[] = $user->user_email;
                }
            }

            if ( is_email( $customer_email ) ) {
                $customer_data[] = $customer_email;
            }

            $customer_data = array_map( 'esc_sql', array_filter( array_unique( $customer_data ) ) );

            if ( WC_PD_Core_Compatibility::is_wc_version_gte_2_7() ) {
                $statuses = array_map( 'esc_sql', wc_get_is_paid_statuses() );
            } else {
                $statuses = array_map( 'esc_sql', apply_filters( 'woocommerce_order_is_paid_statuses', array( 'processing', 'completed' ) ) );
            }

            if ( sizeof( $customer_data ) == 0 ) {
                return false;
            }

            $results = $wpdb->get_col( "
                SELECT im.meta_value FROM {$wpdb->posts} AS p
                INNER JOIN {$wpdb->postmeta} AS pm ON p.ID = pm.post_id
                INNER JOIN {$wpdb->prefix}woocommerce_order_items AS i ON p.ID = i.order_id
                INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS im ON i.order_item_id = im.order_item_id
                WHERE p.post_status IN ( 'wc-" . implode( "','wc-", $statuses ) . "' )
                AND pm.meta_key IN ( '_billing_email', '_customer_user' )
                AND im.meta_key IN ( '_product_id', '_variation_id' )
                AND im.meta_value != 0
                AND pm.meta_value IN ( '" . implode( "','", $customer_data ) . "' )
            " );

            $results = array_map( 'absint', $results );

            set_transient( $transient_name, $results, DAY_IN_SECONDS * 30 );
        }

        return array_intersect( $results, $product_ids );
    }
?>