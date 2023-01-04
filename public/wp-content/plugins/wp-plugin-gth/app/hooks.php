<?php

require_once __DIR__ . '/model.php';
require_once __DIR__ . '/helper.php';

/*
Paths:
woocommerce/template/myaccount/form-login.php
woocommerce/template/myaccount/dashboard.php
woocommerce/template/myaccount/my-account.php
woocommerce/template/myaccount/navigation.php
 */

/* --------------------------------------
Devesse traduzir os seguintes textos:
Username
Username is required
Username or email address
 */

class PgHooks
{
    private $model;
    private $helper;

    public function __construct()
    {
        $this->model = new PgModel();
        $this->helper = new PgHelper();
    }

    public function register($main_file)
    {
        register_activation_hook($main_file, array($this, 'wc_create_database'));

        add_filter('woocommerce_registration_errors', array($this, 'wc_account_registration_field_validation'), 10, 3);
        add_action('woocommerce_before_customer_login_form', array($this, 'wc_before_login_form'), 10, 0);

        add_action('init',
            fn() => add_rewrite_endpoint('profile', EP_ROOT | EP_PAGES), 10, 0);

        add_action('woocommerce_account_profile_endpoint', function () {
            echo do_shortcode('[perfil]');
        }, 10, 0);

        add_filter('woocommerce_account_menu_items',
            fn($items) => (
                array_slice($items, 0, 1, true)
                 + array('profile' => __('Perfil', 'woocommerce'))
                 + array_slice($items, 1, null, true)
            ), 10, 1);

        add_action('woocommerce_before_thankyou', array($this, 'wc_before_thankyou_order'));
        add_filter('woocommerce_my_account_my_orders_actions', array($this, 'wc_my_account_my_orders_custom_action'), 10, 2);
        add_filter( 'woocommerce_add_to_cart_validation', array($this, 'wp_kama_woocommerce_add_to_cart_validation_filter'), 10, 3 );

        add_filter(
            'query_vars',
            array($this, 'custom_query_vars')
        );

        add_action(
            'template_redirect',
            array($this, 'redirect_report')
        );
    }

    /**
     * Function for `woocommerce_add_to_cart_validation` filter-hook.
     * 
     * @param boolean $passed_validation True if the item passed validation.
     * @param integer $product_id        Product ID being validated.
     * @param integer $quantity          Quantity added to the cart.
     *
     * @return boolean
     */
    function wp_kama_woocommerce_add_to_cart_validation_filter( $passed_validation, $product_id, $quantity ) {
        

        
        $pnce = $this->getPnceInfo();
        if ($product_id === $pnce['product_id']) {

            $salt = 1776;
            $result = $this->model->get_form_where_user(get_current_user_id());
            $count = count($result);
            $nonce = wp_create_nonce($product_id + $salt);
            //$env = $this->model->get_env();

            $params = array(
                'count' => $count,
                'dt_created' => ($count > 2)? $result[2]['dt_created'] : null,
                'nonce' => $nonce
            );

            if($count > 2) {
                return $passed_validation;
            }

            if(isset($_GET['p']) && wp_verify_nonce($_GET['p'], $product_id + $salt)) {
                return $passed_validation;
            }
            
            wp_enqueue_script('wc_cart_alert', plugin_dir_url(__DIR__) . '/public/wc_cart_alert.js', [], '1.0.0', true);
            wp_localize_script('wc_cart_alert', 'params', $params );

            $passed_validation = false;
        }

        return $passed_validation;
    }

    public function redirect_report()
    {
        if (get_query_var('report')) {
            include __DIR__ . '/report.php';
            die;
        }
    }

    public function custom_query_vars($query_vars)
    {
        $query_vars[] = 'report';
        return $query_vars;
    }

    public function wc_my_account_my_orders_custom_action($actions, $order)
    {

        $action_slug = 'consult';
        $pnce = $this->getPnceInfo();
        $items = $order->get_items();

        foreach ($items as $item) {
            if ($item->get_product_id() === $pnce['product_id']) {
                $actions[$action_slug] = array(
                    'url' => home_url('/minha-conta/resultados-pnce/'),
                    'name' => 'Ver resultados',
                );
                break;
            }
        }

        return $actions;
    }

    public function wc_before_thankyou_order($order_id)
    {

        $order = wc_get_order($order_id);

        if ($order->has_status('failed')) {
            // if order failed
            return;
        }

        $pnce = $this->getPnceInfo();
        $user_id = $order->get_user_id();
        $items = $order->get_items();
        $product = null;

        foreach ($items as $item) {

            if ($item->get_product_id() === $pnce['product_id']) {
                $product = [
                    'id' => $item->get_product_id(),
                    'name' => $item->get_name(),
                    //'quantity' => $item->get_quantity(),
                ];
                break;
            }
        }

        if (empty($product)) {
            return;
        }

        $result = $this->model->get_form_where_user(get_current_user_id());
        $count = count($result);
        $ultimaData = array_key_exists(2, $result) ? $result[2]['dt_created'] : null;
        $msg = 'Para consultar a sua Avaliação de Maturidade e o Plano de Ações, acesse a área de pedidos em:</br><b>Minha Conta > Pedidos';

        echo $msg?>&nbsp;<a href="<?php echo get_site_url() ?>/minha-conta/orders/">Clicando aqui</a></b><?php
        echo '<br><br>';

        ?>
        <script>
            let format = (entries) => {
                var newArr = {};
                for (var i in entries) {
                    newArr[entries[i]["key"]] = entries[i]["value"];
                }
                return newArr;
            };

            let salvarPergunta = ( num_id, opts, result ) => {
                axios({
                    url: wp_rest.root + "gth/usermeta/pnce",
                    method: "post",
                    headers: {
                        "X-WP-Nonce": wp_rest.nonce,
                    },
                    data: {
                        usermeta: {
                            meta_key: 'select-5',
                            meta_value: { key: num_id, value: opts[num_id]}
                        },
                    },
                })
                .then((response) => {
                    //console.log(JSON.stringify(response.data));
                    //console.log(response.data.rs0);

                    if(response.data.rs0) {
                        if (result.isConfirmed) {
                            window.location.href = "<?php echo get_site_url() ?>/minha-conta/resultados-pnce/";
                        }
                    }
                    else {
                        Swal.fire({
                            icon: "error",
                            title: "Oops...",
                            text: "Alguma coisa deu errado!",
                            confirmButtonColor: "#04A5AC",
                        });
                    }
                })
                .catch((error) => {
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: "Alguma coisa deu errado!",
                        confirmButtonColor: "#04A5AC",
                    });
                });
            }

            jQuery(document).ready(() => {
                var preenchido = <?php echo ($count < 3 ? 'false' : 'true') ?>;
                var ultimaData = "<?php echo $ultimaData ?>";
                var opts = JSON.parse(<?php echo json_encode($this->model->get_option_where(17)[0]['js_option']); ?>);
                opts = format(opts);

                const diffTime = Math.abs(new Date() - new Date(ultimaData));
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                //console.log('@preenchido: ' + JSON.stringify(preenchido));
                //console.log('@ultimaData' + JSON.stringify(ultimaData));
                //console.log('@opts' + JSON.stringify(opts));
                //console.log('@diffDays' + diffDays);

                var option = '';
                for (var i in opts) {
                    option += '<option value="' + i + '">' + opts[i] + '</option>';
                }

                if (preenchido && diffDays > 365) {
                    Swal.fire({
                        title: 'PNCE',
                        html:
                        '<label for="popup2">Como conheceu o PNCE?</label><br>' +
                        '<select name="popup2" id="popup2">' +
                            '<option value="" disabled selected>Selecione</option>' +
                            option +
                        '</select><br><br>' +
                        '<div>Você já possui um perfil empresarial cadastrado no PNCE. Os dados deste perfil foram informados a mais de 12 meses. Deseja criar um novo perfil empresarial?</div>',
                        allowOutsideClick: false,
                        showDenyButton: true,
                        confirmButtonColor: '#04A5AC',
                        denyButtonColor: '#D5D5D5',
                        confirmButtonText: 'Sim',
                        denyButtonText: 'Não',
                        preConfirm: () => {
                            return !(!$( "#popup2" ).val());
                        },
                        preDeny: () => {
                            return !(!$( "#popup2" ).val());
                        }
                    }).then((result) => {
                        var num_id = parseInt($( "#popup2" ).val());
                        salvarPergunta(num_id, opts, result);
                    });
                }
                else {
                    Swal.fire({
                        title: "PNCE",
                        allowOutsideClick: false,
                        html:
                        '<label for="popup1">Como conheceu o PNCE?</label><br>' +
                        '<select name="popup1" id="popup1">' +
                            '<option value="" disabled selected>Selecione</option>' +
                            option +
                        '</select><br><br>' +
                        '<div>Obrigada! Você concluiu todos os passos necessários para receber sua Avaliação de Maturidade Exportadora e o seu Plano de Ações para Internacionalização.<br><br>Clique no botão abaixo, para visualizar:</div>',
                        confirmButtonColor: '#04A5AC',
                        cancelButtonColor: '#D5D5D5',
                        confirmButtonText: "Visualizar resultados",
                        preConfirm: () => {
                            return !(!$( "#popup1" ).val());
                        }
                    }).then((result) => {
                        var num_id = parseInt($( "#popup1" ).val());
                        salvarPergunta(num_id, opts, result);
                    });
                }

            });
        </script>
        <?php
    }

    /**
     *
     */
    public function getPnceInfo()
    {
        foreach (ENV as $key => $value) {
            if (strpos($_SERVER['HTTP_HOST'], $key) !== false) {
                return $value;
            }
        }

        return ENV['default'];
    }

    public function wc_create_database()
    {
        $this->model->create_database();
    }

    public function wc_before_login_form()
    {
        ?>
        <script>
            jQuery(document).ready(() => {
                jQuery('#reg_username').keypress((e) => {
                    var charCode = (e.which) ? e.which : e.keyCode;
                    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                        return false;
                    }
                });
            });
        </script>
        <?php
    }

    // Registration field validation
    public function wc_account_registration_field_validation($errors, $username, $email)
    {
        if (isset($username) && empty($username)) {
            $errors->add('username', __('<strong>Error</strong>: Campo obrigatório', 'woocommerce'));
        } else if (!$this->helper->validate_cpf($username, true)) {
            $errors->add('username', __('<strong>Error</strong>: CPF inválido', 'woocommerce'));
        }

        return $errors;
    }
}