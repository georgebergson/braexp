<?php
use Respect\Validation\Validator as v;

require_once __DIR__ . '/model.php';
require_once __DIR__ . '/helper.php';
require_once __DIR__ . '/consumo-pnce.php';

class PgRoutes extends WP_REST_Controller
{

    private $model;
    private $valid;
    /**
     * Register the routes for the objects of the controller.
     */
    public function register_routes()
    {

        $namespace = 'gth';

        $this->valid = [
            "validOption" => fn($value) => v::key('key', v::alnum('-'))->key('value', v::stringType())->validate((array) $value),
            "validPhone" => fn($value) => v::key('value', v::digit()->length(10, 11))->validate((array) $value),
            "validText" => fn($value) => v::key('value', v::stringType()->length(1, 255))->validate((array) $value),
            "validCnpj" => fn($value) => v::key('value', v::digit()->cnpj())->validate((array) $value),
            "validCep" => fn($value) => v::key('value', v::digit()->postalCode('BR'))->validate((array) $value),
        ];

        $this->model = new PgModel();
        $this->helper = new PgHelper();

        register_rest_route($namespace, '/form/create', array(
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => array($this, 'form_create'),
            'permission_callback' => function ($request) {
                return true;
            },
        ));

        register_rest_route($namespace, '/usermeta/pnce', array(
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => array($this, 'save_usermeta_pnce'),
            'permission_callback' => function ($request) {
                return true;
            },
        ));

        register_rest_route($namespace, '/model/get_form_where_user', array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'get_form_where_user'),
            'permission_callback' => '__return_true',
        ));

        register_rest_route($namespace, '/test', array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'get_unit_test'),
            'permission_callback' => '__return_true',
        ));
    }

    public function get_unit_test($request)
    {
        include  __DIR__ . '/unit-test.php';
    }

    public function get_form_where_user($request)
    {
        $user_id = get_current_user_id();
        return (!isset($user_id) || $user_id < 1) ? null : $this->model->get_form_where_user($user_id);
    }

    // MEXER AQUI
    public function save_usermeta_pnce($request)
    {
        $user_id = get_current_user_id();
        $data = json_decode($request->get_body());
        $usermeta = $data->usermeta;
        $usermeta->meta_value = json_decode(json_encode($usermeta->meta_value), true);

        $add = add_user_meta( $user_id, $usermeta->meta_key, $usermeta->meta_value, true);

        if(!$add) {
            $add = update_user_meta( $user_id, $usermeta->meta_key, $usermeta->meta_value);
        }

        if($add || $add > 0) {
            // consumo-pnce
            $consumo = new ConsumoPnce();
            $pnce = $consumo->getEnvData();
            $user = $this->model->get_user_where_id($user_id);
            $usermeta = $this->model->get_usermeta($user_id);
            $answer = $this->model->get_answer($user_id);


            $arr = array();
            foreach($usermeta as $rs) {
                array_push($arr, [
                    'meta_key' => $rs['meta_key'], 
                    'meta_value' => $rs['meta_value']
                ]);
            }

            if(empty($user[0]['user_url'])) {
                unset($user[0]['user_url']);
            }

            $usuarios = [
                'users' => $user,
                'usermeta' => [ $arr ]
            ];

            $meta = array();
            $arr = array_fill(1, 3, array());

            array_push($arr[3], [
                'meta_key' => 'hidden-1',
                'meta_value' => $user[0]['user_login']
            ]);

            array_push($arr[3], [
                'meta_key' => 'hidden-2',
                'meta_value' => $user[0]['user_email']
            ]);

            array_push($arr[3], [
                'meta_key' => 'hidden-3',
                'meta_value' => $user_id
            ]);

            foreach($answer as $rs) {
                $ans = json_decode($rs['js_answer'], true);

                array_push($arr[$rs['co_form'] + ''], [
                    'meta_key' => $rs['co_forminator'], 
                    'meta_value' => $ans['value']
                ]);
            }

            array_push($arr[3], [
                'meta_key' => 'custom-1',
                'meta_value' => ''
            ]);

            array_push($arr[3], [
                'meta_key' => 'custom-2',
                'meta_value' => 'NÃO ENCONTRADO'
            ]);

            $questionarios = [
                'calc_score' => true,
                'frmt_form_entry' => [
                    [
                        'entry_type' => 'custom-forms',
                        'form_id' => 710,
                        'is_spam' => 0,
                    ],
                    [
                        'entry_type' => 'custom-forms',
                        'form_id' => 710,
                        'is_spam' => 0,
                    ],
                    [
                        'entry_type' => 'custom-forms',
                        'form_id' => 710,
                        'is_spam' => 0,
                    ],
                ],
                'frmt_form_entry_meta' => [ $arr[1], $arr[2], $arr[3] ]
            ];

            $rs1 = $consumo->postCriarUsuarios($usuarios);
            $rs2 = $consumo->postSalvarQuestionarios($questionarios);

            if(!$rs1['success'] && strrpos($rs1['exception'], 'Duplicate entry') === false ) {
                $rs1['success'] = true;
            }

            //return $questionarios;
            //return $rs1['success'] && $rs2['success'];

            return [ 
                'rs0' => $rs1['success'] && $rs2['success'], 
                'rs1' => $rs1, 
                'rs2' => $rs2
            ];
        }
        else {
            return $add;
        }
    }

    public function form_create($request)
    {
        $data = json_decode($request->get_body());
        $co_form = $data->co_form;
        $answers = (array) $data->answers;
        $questions = $this->model->get_question_where_form($co_form);

        if (!$this->form_check($co_form, $answers, $questions)) {
            return ["key" => -1, "value" => json_encode($answers)];
        }

        if ($co_form == 1) {
            $user_id = get_current_user_id();
            $res = (!isset($user_id) || $user_id < 1) ? null : $this->model->get_form_where_user($user_id);
            if ($res != null) {
                return new WP_REST_Response(
                    $this->form_update_data(
                        $res[0]['co_form'],
                        $res[0]['co_quiz'],
                        $answers
                    ), 200
                );
            }
        }

        return new WP_REST_Response(
            $this->form_save_data($co_form, $answers),
            200
        );
    }

    // REMOVER
    /*
    public function form_create($request)
    {
        $data = json_decode($request->get_body());

        $co_form = $data->co_form;
        $answers = (array) $data->answers;

        $questions = $this->model->get_question_where_form($co_form);
        $user_id = get_current_user_id();
        $consumo = new ConsumoPnce();
        $pnce = $consumo->getEnvData();
        $has_order = $this->model->has_order($user_id, $pnce['product_id']);
        $select5 = get_user_meta($user_id, 'select-5', true);

        if (!$this->form_check($co_form, $answers, $questions)) {
            return ["key" => -1, "value" => json_encode($answers)];
        }


        $forminator = $this->model->get_forminator_codes($co_form);

        $meta = array();
        $user = null;
        foreach($forminator as $records) {
            $arr = array(
                'meta_key' => null,
                'meta_value' => null,
            );

            foreach($records as $key => $value) {
                if($key == 'co_forminator') {
                    $arr['meta_key'] = $value;
                } else if($key == 'co_question') {
                    $arr['meta_value'] = $answers[$value]->value;
                }
            }

            if($co_form === 3 && strrpos($arr['meta_key'], 'hidden') > -1) {
                if(empty($user)){
                    $user = $this->model->get_user_where_id($user_id);
                }
                if($arr['meta_key'] === 'hidden-1') {
                    $arr['meta_value'] = $user[0]['user_login'];
                }
                if($arr['meta_key'] === 'hidden-2') {
                    $arr['meta_value'] = $user[0]['user_email'];
                }

                if($arr['meta_key'] === 'hidden-3') {
                    $arr['meta_value'] = $user_id;
                }
            }

            if(isset($arr['meta_value'])) {

                if(strpos($arr['meta_key'], '@') > -1) {
                    $arr['meta_key'] = ltrim($arr['meta_key'], '@');
                    $arr['meta_value'] = serialize(array($arr['meta_value']));
                }

                array_push($meta, $arr);
            }
        }

        if($co_form === 3 && !empty($select5)) {
            array_push($meta, ['meta_key' => 'select-5', 'meta_value' => $select5['value'] ]);
        }

        if ($co_form === 1) {
            $res = (!isset($user_id) || $user_id < 1) ? null : $this->model->get_form_where_user($user_id);

            if ($res != null) {
                if(empty($user)) {
                    $user = $this->model->get_user_where_id($user_id);
                }
                
                if(isset($user)) {
                    if(isset($user[0]['ID'])) {
                        unset($user[0]['ID']); 
                    }
        
                    if(isset($user[0]['user_url']) && empty($user[0]['user_url'])) {
                        unset($user[0]['user_url']); 
                    }
                }

                $usermeta = $this->model->get_usermeta($user_id);

                $adden = [];
                foreach($usermeta as $records) {
                    $arr = array(
                        'meta_key' => $records['meta_key'],
                        'meta_value' => $records['meta_value'],
                    );

                    array_push($adden, $arr);
                }

                $meta = array_merge($adden,$meta);

                $dados = [
                    'users' => $user,
                    'usermeta' => [ $meta ]
                ];

                //return $dados;

                try {
                    $this->model->begin_transaction();

                    $rs1 = $this->form_update_data(
                        $res[0]['co_form'],
                        $res[0]['co_quiz'],
                        $answers
                    );

                    //echo "@form1-1\n";
                    //var_dump($rs1);

                    if(!$rs1) {
                        $this->model->rollback();
                        return new WP_REST_Response(
                            ["key" => -1, "value" => "1@Falha ao tentar salvar os dados no banco", "rs" => $rs1],
                            400
                        );
                    }

                    $rs2 = $consumo->postCriarUsuarios($dados);
                    $rs2 = json_decode($rs2);

                    //return $rs2;
                    //echo "@form1-2\n";
                    //var_dump($rs2);

                    if($has_order < 1) {
                        $this->model->commit();
                        return new WP_REST_Response(
                            ["key" => 1, "value" => "Atualizado com sucesso!"], 
                            200
                        );
                    }
            
                    if(isset($rs2) && $rs2->success) {
                        $this->model->commit();
                        return new WP_REST_Response(
                            ["key" => 1, "value" => "Atualizado com sucesso!"], 
                            200
                        );
                    }
                    else {
                        $this->model->rollback();
                        return new WP_REST_Response(
                            [
                                "key" => -1,
                                "value" => "2@Falha ao tentar salvar os dados no banco",
                                "consumo" => $consumo,
                                "pnce" => $pnce,
                                "has_order" => $has_order,
                                "select5" => $select5,
                                "rs" => $dados
                            ],
                            400
                        );
                    }
                } catch(Exception $e) {
                    $this->model->rollback();
                }
            }
        }


        $this->model->begin_transaction();
        $rs1 = $this->form_save_data($co_form, $answers);

        if(!$rs1) {
            $this->model->rollback();
            return new WP_REST_Response(
                ["key" => -1, "value" => "3@Falha ao tentar salvar os dados no banco", "rs" => $rs1],
                400
            );
        }

        $dados = [
            'calc_score' => ($co_form === 3),
            'frmt_form_entry' => [
                [
                    "entry_type" => "custom-forms",
                    "form_id" => $pnce['form_id'],
                    "is_spam" => 0
                ]
            ],
            'frmt_form_entry_meta' => [ $meta ]
        ];

        $rs2 = $consumo->postSalvarQuestionarios($dados);
        $rs2 = json_decode($rs2);

        if($has_order < 1) {
            $this->model->commit();
            return new WP_REST_Response(
                ["key" => 1, "value" => "Atualizado com sucesso!"], 
                200
            );
        }
            
        if(isset($rs2) && $rs2->success) {
            $this->model->commit();
            return new WP_REST_Response(
                ["key" => 1, "value" => "Atualizado com sucesso!"], 
                200
            );
        } else {
            $this->model->rollback();
            return new WP_REST_Response(
                ["key" => -1, "value" => "4@Falha ao tentar salvar os dados no banco", "rs" => $rs2],
                400
            );
        }
        
    }
    */

    public function form_check($co_form, $answers, $questions)
    {
        $result = [];
        foreach ($questions as $key => $value) {

            if ($value["st_required"] == 1) {
                if (isset($value["js_visibility"])) {
                    $result[$key] = true;
                } else if (!$answers[$value["co_question"]]) {
                    $result[$key] = false;
                } else {
                    $row = json_decode($value["js_settings"]);
                    $rule = $row->rules ? $this->valid[$row->rules] : null;
                    $resp = isset($rule) ? $rule($answers[$value["co_question"]]) : true;
                    $result[$key] = $resp;
                }
            }
        }

        return array_sum($result) === count($result);
    }

    public function form_update_data($co_form, $co_quiz, $answers)
    {
        $affected_rows = $this->model->set_quiz([$co_quiz], true);

        if ($affected_rows < 1) {
            return false;
        }

        $co_answer = [];
        foreach ($answers as $key => $value) {
            array_push($co_answer, $this->model->set_answer(array(
                json_encode($value),
                $co_quiz,
                $key,
            ), true));
        }
        
        return true;
    }

    public function form_save_data($co_form, $answers)
    {
        $co_quiz = $this->model->set_quiz(array(
            get_current_user_id(),
            $this->helper->get_current_user_ip(),
        ));

        if (!isset($co_quiz)) {
            return false;
        }

        $co_answer = [];
        foreach ($answers as $key => $value) {
            if (count((array) $value) > 0) {
                array_push($co_answer, $this->model->set_answer(array(
                    $co_quiz,
                    $co_form,
                    $key,
                    json_encode($value),
                )));
            }
        }

        return true;
    }
}
