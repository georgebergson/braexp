<?php

require_once  __DIR__  . '/consumo-pnce.php';

try {


    $consumo = new ConsumoPnce(['header' => 1]);

    $data = [
        'users' => [
            [
                'user_login' => '95391679060',
                'user_email' => 'www@pirus.com.br'
            ]
        ],
        'usermeta' => [
            [
                [
                    'meta_key' => 'nome',
                    'meta_value' => 'LeonardoQWER'
                ],
                [
                    'meta_key' => 'Sobrenome',
                    'meta_value' => 'MatiasQWER'
                ]
            ]
        ]
    ];

    $result = $consumo->postCriarUsuarios( $data );
    //$result = curl_exec($ch);
    //curl_close($ch);

    // display
    var_dump($result);

} catch (Exception $e) {
    echo 'Exceção capturada: ', $e->getMessage(), "\n";
}