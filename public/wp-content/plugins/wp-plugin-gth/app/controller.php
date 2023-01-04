<?php

require_once __DIR__ . '/model.php';
require_once __DIR__ . '/hooks.php';
require_once __DIR__ . '/routes.php';
require_once __DIR__ . '/consumo-pnce.php';

class PgController
{
    private $shortcode_main;

    private $helper;
    private $model;
    private $hooks;

    public function __construct()
    {
        $this->shortcode_main = 'gth';
        $this->shortcode_perfil = 'perfil';
        $this->shortcode_resultados_pnce = 'resultados_pnce';

        $this->helper = new PgHelper();
        $this->model = new PgModel();
        $this->hooks = new PgHooks();
        $this->routes = new PgRoutes();
    }

    public function register($main_file)
    {
        add_action('rest_api_init', array($this, 'register_routes'));
        add_action('wp_enqueue_scripts', array($this, 'scripts'));

        add_shortcode('gth', array($this, 'shortcode_main'));
        add_shortcode('resultados_pnce', array($this, 'shortcode_resultados_pnce'));
        add_shortcode('perfil', array($this, 'shortcode_perfil'));

        $this->hooks->register($main_file);
    }

    public function register_routes()
    {
        $this->routes->register_routes();
    }

    public function shortcode_resultados_pnce()
    {
        $user_id = get_current_user_id();
        $user_cpf = $this->model->get_user_where_id($user_id);

        //echo '@@@CPF@@@';
        //var_dump($user_cpf[0]['user_login']);

        if(!isset($user_cpf) || !isset($user_cpf[0]['user_login'])) {
            return;
        }

        $cpf = $user_cpf[0]['user_login'];
        $data = [];
        $consumo = new ConsumoPnce();
        //$consumo->criarCertificados('GAESI GTH STAGE.p12', 'oRtgXv5Y8GmCFxnt0ptTg6jjggfc');
        //$consumo->criarCertificados('GAESI GTH STAGE.p12', 'oRtgXv5Y8GmCFxnt0ptTg6jjggfc');
        $data = json_decode($consumo->getAvaliacaoMaturidade($cpf), true);

        //echo '@@@META@@@';
        //var_dump($data);

        if(!isset($data) || !isset($data['form_entry_meta']) || !isset($data['entry_id'])){
            echo "Dados não disponíveis.<br>Preencha o perfil pessoal e o empresarial.";
            return;
        }


        //$entry_id = $meta['form_entry_meta'][0]['entry_id'];
        //$data = $consumo->getAvaliacaoMaturidade($cpf);

        //echo 'DATA';
        //var_dump($data);

        /*
        if(isset($data) && is_array($data)) {
            echo "Dados não disponíveis.<br>Preencha o perfil pessoal e o empresarial.";
            return;
        }
        */

        //$data = json_decode($data, true);

        //echo (json_encode($data['scores_avg']));
        //return;

        ob_start();
        ?>
            <div id="report">
            <v-app>
                <router-view></router-view>
                <script type="text/javascript">
                    Vue.prototype.$entries = <?php echo (json_encode($data)); ?>
                    //var $entries = <?php //echo $data; ?>;
                    var $myForms = <?php echo json_encode($data['form_entry_meta']); ?>;

                    var obj = {};
                    //obj.form_id = $entries[0].form_id;
                    obj.entry_id = <?php echo json_encode($data['entry_id']); ?>;
                    obj.date_created = <?php echo json_encode($data['score']['dt_created']); ?>;
                    obj.meta_data = {};

                    /*
                    $entries.forEach((item) => {
                        obj.meta_data[item.meta_key] = {value : item.meta_value};
                    });
                    */

                    if($myForms) {
                        var prev = $myForms[0];
                        var arrMeta = {};
                        var arrMain = [];

                        $myForms.forEach((item, index, arr) => {
                            if(item.entry_id != prev.entry_id || (arr.length - 1) == index ) {

                                if((arr.length - 1) == index) {
                                    arrMeta[item.meta_key] = { value : item.meta_value }
                                }

                                var obj = {};
                                obj.form_id = prev.form_id;
                                obj.entry_id = prev.entry_id;
                                obj.date_created = prev.date_created;
                                obj.meta_data = arrMeta;
                                arrMain.push(obj);
                                prev = item;
                                arrMeta = {};
                            }

                            arrMeta[item.meta_key] = { value : item.meta_value }
                        });
                    }

                    //console.log(JSON.stringify(arrMain));

                    $myForms = arrMain;
                    obj.userdata = <?php echo json_encode($data['usermeta']); ?>

                    //console.log(JSON.stringify($myForms));

                    Vue.prototype.$v = obj;
                    Vue.prototype.$scores_avg = <?php echo json_encode($data['scores_avg']); ?>;
                    Vue.prototype.$myForms = $myForms;
                </script>
            </v-app>
            </div>
            <?php

        return ob_get_clean();
    }

    public function shortcode_perfil($atts)
    {
        ob_start();
        $host = $this->helper->get_base_url();
        $host .= $host == "http://localhost" ? ":8888/gth" : "";
        $result = $this->model->get_form_where_user(get_current_user_id());

        $rs = [
            0 => [ "co_form" => false, "enable" => false ],
            1 => [ "co_form" => false, "enable" => false ],
            2 => [ "co_form" => false, "enable" => false ],
        ];

        if (isset($result) && is_array($result)) {
            foreach ($result as $value) {
                $rs[intval($value["co_form"]) - 1]["co_form"] = true;
            }
        }

        $temp = true;
        for($i = 0; $i < 3; $i++) {
            $rs[$i]['enable'] = $temp;
            $temp = $rs[$i]['co_form'];
        }

        ?>
            <!--<?php echo ($str); ?></div>-->
           <div class="gth-main">
            <!-- =================== PASSO 1 =================== -->
            <div class="gth-box">
                <div class="gth-row">
                    <div class="gth-col">
                        <div class="gth-wrapper">
                            <div class="gth-icon-box">
                                <i class="<?php echo $rs[0]["co_form"] ?
        "gth-fa-green fa-regular fa-circle-check" :
        "gth-fa-red fa-regular fa-circle" ?>"></i>
                            </div>

                            <div class="gth-icon-text">
                                <?php echo $rs[0]["co_form"] ? "Preenchido" : "Não preenchido" ?>
                            </div>
                        </div>

                    </div>

                    <div class="gth-col">
                        <div class="gth-wrapper">
                            <strong>PASSO 01</strong><br>Editar Perfil Pessoal
                        </div>
                    </div>

                    <div class="gth-col">
                        <div class="gth-wrapper">
                            <a href="<?php echo $host . "/1-form"; ?>" class="<?php echo $rs[0]["enable"] ?
        "gth-btn-anchor" :
        "gth-btn-anchor gth-btn-disable" ?>">
                                <div class="gth-btn">
                                    <div class="gth-btn-icon">
                                        <i class="gth-fa-btn fas fa-user"></i>
                                    </div>
                                    <div class="gth-btn-text">
                                        PERFIL PESSOAL
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <!-- =================== PASSO 2 =================== -->
            <div class="gth-box">
                <div class="gth-row">
                    <div class="gth-col">
                        <div class="gth-wrapper">
                            <div class="gth-icon-box">
                                <i class="<?php echo $rs[1]["co_form"] ?
        "gth-fa-green fa-regular fa-circle-check" :
        "gth-fa-red fa-regular fa-circle" ?>"></i>
                            </div>

                            <div class="gth-icon-text">
                                <?php echo $rs[1]["co_form"] ? "Preenchido" : "Não preenchido" ?>
                            </div>
                        </div>

                    </div>

                    <div class="gth-col">
                        <div class="gth-wrapper">
                            <strong>PASSO 02</strong><br>Preencher o(s) CNPJ(s) da(s) empresa(s)
                        </div>
                    </div>

                    <div class="gth-col">
                        <div class="gth-wrapper">
                            <a href="<?php echo $host . "/2-form"; ?>" class="<?php echo $rs[1]["enable"] ?
        "gth-btn-anchor" :
        "gth-btn-anchor gth-btn-disable" ?>">
                                <div class="gth-btn">
                                    <div class="gth-btn-icon">
                                        <i class="gth-fa-btn fas fa-house-user"></i>
                                    </div>
                                    <div class="gth-btn-text">
                                        INFORMAR CNPJ
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <br>

            <!-- =================== PASSO 3 =================== -->
            <div class="gth-box">
                <div class="gth-row">
                    <div class="gth-col">
                        <div class="gth-wrapper">
                            <div class="gth-icon-box">
                                <i class="<?php echo $rs[2]["co_form"] ?
        "gth-fa-green fa-regular fa-circle-check" :
        "gth-fa-red fa-regular fa-circle" ?>"></i>
                            </div>

                            <div class="gth-icon-text">
                                <?php echo $rs[2]["co_form"] ? "Preenchido" : "Não preenchido" ?>
                            </div>
                        </div>

                    </div>

                    <div class="gth-col">
                        <div class="gth-wrapper">
                            <strong>PASSO 03</strong><br>Preencher Perfil Empresarial
                        </div>
                    </div>

                    <div class="gth-col">
                        <div class="gth-wrapper">
                            <a href="<?php echo $host . "/3-form"; ?>" class="<?php echo $rs[2]["enable"] ?
        "gth-btn-anchor" :
        "gth-btn-anchor gth-btn-disable" ?>">
                                <div class="gth-btn">
                                    <div class="gth-btn-icon">
                                        <i class="gth-fa-btn fas fa-house-user"></i>
                                    </div>
                                    <div class="gth-btn-text">
                                        PERFIL EMPRESARIAL
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php

        return ob_get_clean();
    }

    public function shortcode_main($atts)
    {
        $data = [];
        $user_id = get_current_user_id();
        $env = $this->model->get_env();

        $data['env'] = $env;
        $data['has_order'] = $this->model->has_order($user_id, $env['product_id']);
        $data['co_form'] = null;
        $data['co_quiz'] = null;
        $co_form = null;

        // Questoes
        if (isset($atts) && isset($atts['id'])) {
            $co_form = $atts['id'];
            $co_form = sanitize_title_with_dashes($co_form, '', 'save');
            $data['co_form'] = $co_form;
            $data['form'] = $this->model->get_form_where_form($co_form);
            $data['questions'] = $this->model->get_question_where_form($co_form);

            // Carrega os CNPJ e outras listas de opções
            foreach ($data['questions'] as &$value) {
                if ($value["co_option"] == 20) {
                    $sql = json_decode($value["js_settings"])->sql;
                    $sql->values[0] = get_current_user_id();
                    $result = $this->model->{$sql->key}($sql->values);

                    $rs = [];

                    if (isset($result) && is_array($result)) {
                        foreach ($result as $v2) {
                            $rs[] = [
                                "key" => $v2["co_quiz"],
                                "value" => json_decode($v2["js_answer"])->value,
                            ];
                        }
                    }

                    $value["js_option"] = json_encode($rs);
                }
            }
        }

        // Respostas
        //
        if (isset($co_form) && $co_form == 1) {
            $form = $this->model->get_form_where_user_form([$user_id, $co_form]);

            if (@isset($form) && @isset($form[0])) {
                $data['co_user'] = $form[0]['co_user'];
                $data['co_form'] = $form[0]['co_form'];
                $data['co_quiz'] = $form[0]['co_quiz'];
                $data['answers'] = $this->model->get_answer_where_quiz($data['co_quiz']);
            }
        }

        ob_start();
        ?>
        <div id="main">
        <v-app>
            <router-view></router-view>
            <script type="text/javascript">
                Vue.prototype.$entries = <?php echo (json_encode($data)); ?>
            </script>
        </v-app>
        </div>
        <?php

        return ob_get_clean();
    }

    public function scripts()
    {
        global $post;
        $postMain = has_shortcode($post->post_content, $this->shortcode_main);
        $postResultados = has_shortcode($post->post_content, $this->shortcode_resultados_pnce);

        wp_enqueue_script('sweetalert2', 'https://cdn.jsdelivr.net/npm/sweetalert2@11.4.9/dist/sweetalert2.all.min.js', [], '11.4.9');
        wp_enqueue_script('axios', 'https://cdn.jsdelivr.net/npm/axios@0.26.1/dist/axios.min.js', [], '0.26.1');
        wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css', [], '6.1.1');

        wp_enqueue_style('global-style', plugin_dir_url(__DIR__) . '/public/global.css', [], '1.0.0');
        wp_enqueue_script('global-script', plugin_dir_url(__DIR__) . '/public/global.js', [], '1.0.0');

        wp_localize_script('global-script', 'wp_rest', array(
            'user_id' => get_current_user_id(),
            'root' => esc_url_raw(rest_url()),
            'nonce' => wp_create_nonce('wp_rest'),
        ));

        //https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css

        if($post && ($postMain || $postResultados)) {
            wp_enqueue_script('vue', 'https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.min.js', [], '2.6.14');
            wp_enqueue_script('vue-router', 'https://cdn.jsdelivr.net/npm/vue-router@3.5.3/dist/vue-router.min.js', [], '3.5.3');
            wp_enqueue_script('vuetify', 'https://cdn.jsdelivr.net/npm/vuetify@2.6.4/dist/vuetify.min.js', [], '2.6.4');
            wp_enqueue_script('v-maska', 'https://cdn.jsdelivr.net/npm/maska@1.5.0/dist/maska.js', [], '1.5.0');
            wp_enqueue_script('chartjs', 'https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js', [], '2.9.4');
            wp_enqueue_script('jspdf', 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.2.0/jspdf.umd.min.js', [], '2.2.0');

            wp_enqueue_style('mdi', 'https://cdn.jsdelivr.net/npm/@mdi/font@6.6.96/css/materialdesignicons.min.css', [], '6.6.96');
            wp_enqueue_style('vuetify', 'https://cdn.jsdelivr.net/npm/vuetify@2.6.4/dist/vuetify.min.css', [], '2.6.4');
        }

        if ($post && $postMain) {
            wp_enqueue_script('main-script', plugin_dir_url(__DIR__) . '/public/main.js', [], '2.20.15', true);
            wp_enqueue_style('main-style', plugin_dir_url(__DIR__) . '/public/main.css', [], '2.20.15');
        }
        else  if ($post && $postResultados) {
            wp_enqueue_script('report-script', plugin_dir_url(__DIR__) . '/public/report.js', [], '2.20.15', true);
            wp_enqueue_style('report-style', plugin_dir_url(__DIR__) . '/public/report.css', [], '2.20.15');
        }
    }
}