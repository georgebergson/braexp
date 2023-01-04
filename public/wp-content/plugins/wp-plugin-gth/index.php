<?php

/*
Plugin Name: GTH
Description: Plugin de personalização do WordPress para o projeto do GTH
Version: 1.0.0
Author: Leonardo
 */

require_once plugin_dir_path(__FILE__) . '/vendor/autoload.php';
define('ROOT', __DIR__);

const ENV = [
    'default' => [
        'name' => 'local',
        'url' => 'https://pnce-api-test.its.org.br/', 
        'form_id' => 710,
        'product_id' => 1276,
        'product_uri' => 'produto/plano-nacional-da-cultura-exportadora-pnce/'
    ],
    'localhost' => [
        'name' => 'local',
        'url' => '127.0.0.1:8000/', 
        'form_id' => 710,
        'product_id' => 1276,
        'product_uri' => 'produto/plano-nacional-da-cultura-exportadora-pnce/'
    ],
    'test' => [
        'name' => 'test',
        'url' => 'https://pnce-api-test.its.org.br/',
        'form_id' => 710,
        'product_id' => 1276,
        'product_uri' => 'produto/plano-nacional-da-cultura-exportadora-pnce/'
    ],
    'stage' => [
        'name' => 'stage',
        'url' => 'https://pnce-api-stage2.its.org.br/',
        'form_id' => 710,
        'product_id' => 1276,
        'product_uri' => 'produto/plano-nacional-da-cultura-exportadora-pnce/'
    ],
    'prod' => [
        'name' => 'prod',
        'url' => 'https://pnce-api-prod.its.org.br/',
        'form_id' => 710,
        'product_id' => 1276,
        'product_uri' => 'produto/plano-nacional-da-cultura-exportadora-pnce/'
    ],
];

const CURLOPT = [
    'url' => '',
    'sslversion' => CURL_SSLVERSION_TLSv1_2,
    'returntransfer' => 1,
    'ssl_verifypeer' => 0,
    'sslcert' => 'sslcert.pem',
    'sslkey' => 'sslkey.pem',
    'cainfo' => 'cainfo.pem',
    'header' => 0,
    'timeout' => 3000,
];

if (!class_exists('PgController')) {
    require_once __DIR__ . '/app/controller.php';
    (new PgController())->register(__FILE__);
}
