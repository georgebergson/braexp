<?php

require_once __DIR__ . '/model.php';
require_once __DIR__ . '/consumo-pnce.php';

$types = [
    1 => "report_evaluation",
    2 => "report_action",
    3 => "report_evaluation_analyst",
    4 => "report_evaluation_average",
    5 => "user_report_action",
    6 => "user_report_evaluation",
];

$report = intval(get_query_var('report'));

if ($report < 5 || $report > 6) {
    echo 'Campo report com o valor incorreto';
    die;
}

$user_id = get_current_user_id();

if($user_id == 0) {
    return;
}

$cpf = get_userdata($user_id)->data->user_login;
//exit;

$data = [];
$consumo = new ConsumoPnce();
$data = json_decode($consumo->getAvaliacaoMaturidade($cpf), true);

//echo '@@@META@@@';
//var_dump($data);

if(!isset($data) || !isset($data['form_entry_meta']) || !isset($data['entry_id'])){
    echo "Dados não disponíveis.<br>Preencha o perfil pessoal e o empresarial.";
    return;
}

//$data = json_decode($data, true);

/*
$model = new Pnce_model();
$form_id = intval(get_query_var('form_id'));
$entry_id = intval(get_query_var('entry_id'));
$user_id = intval(get_query_var('user_id'));
$token = get_query_var('token');
$data = $model->get_entry_join_meta($form_id, $entry_id);
$userdata = $model->get_user($user_id);
*/

//print_r($data['form_entry_meta']);

$cnpj = '';
foreach ($data['form_entry_meta'] as $value) {
    if ($value['meta_key'] == 'select-11') {
        $cnpj = $value['meta_value'];
    }
}


$cnpj = preg_replace('/\D/', '', $cnpj);

//echo '@@@@@@' . $cnpj;

//exit;


//$myForms = $model->get_entries_cnpj($cnpj);
//echo var_dump( $data );
//$data['forms'] = $forms;
//echo var_dump( $forms );

//$data = json_encode($data);

// =================================================

$year = date("Y", strtotime("-1 year"));
$scores_avg = $data['scores_avg'];

//var_dump($scores_avg);
//exit;

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title>Report</title>
</head>
<body>
<script src="https://cdn.jsdelivr.net/npm/vue@2.6.12/dist/vue.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.2.0/jspdf.umd.min.js"></script>

<div id="report">
    <<?php echo $types[$report] ?>/>
    <script>
        var $entries = <?php echo json_encode($data); ?>;
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
        obj.meta_data = $myForms[0]['meta_data'];
        obj.userdata = <?php echo json_encode($data['usermeta']); ?>

        Vue.prototype.$v = obj;
        Vue.prototype.$scores_avg = <?php echo json_encode($scores_avg); ?>;
        Vue.prototype.$myForms = $myForms;
    </script>
</div>
<script src="<?php echo (plugin_dir_url(__DIR__) . 'public/' . $types[$report] . '.js?ver=2.13.8') ?>" ></script>
</body>
</html>