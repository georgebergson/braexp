<?php

require_once __DIR__ . '/model.php';
require_once __DIR__ . '/helper.php';

class PgServices
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
        add_action('wp_ajax_gth_logs', [$this, 'get_rest_logs']);
        add_action('wp_ajax_gth_messages', [$this, 'get_rest_messages']);
        add_action('wp_ajax_gth_users', [$this, 'get_rest_users']);
        add_action('wp_ajax_nopriv_gth_users', [$this, 'get_rest_users']);
    }

    public function get_rest_logs()
    {
        wp_send_json($this->model->get_logs());
        wp_die();
    }

    public function get_rest_messages()
    {
        wp_send_json($this->model->get_messages());
        wp_die();
    }

    public function get_rest_users()
    {
        wp_send_json($this->model->get_users());
        wp_die();
    }
}
