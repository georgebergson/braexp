<?php

global $wpdb;

$tables = [
    'users' => $wpdb->prefix . 'users',
    'usermeta' => $wpdb->prefix . 'usermeta',
    'answer' => $wpdb->prefix . 'plugin_answer',
    'form' => $wpdb->prefix . 'plugin_form',
    'log' => $wpdb->prefix . 'plugin_log',
    'message' => $wpdb->prefix . 'plugin_message',
    'option' => $wpdb->prefix . 'plugin_option',
    'question' => $wpdb->prefix . 'plugin_question',
    'question_type' => $wpdb->prefix . 'plugin_question_type',
    'quiz' => $wpdb->prefix . 'plugin_quiz',
    'score' => $wpdb->prefix . 'plugin_score',
    'order_product_lookup' => $wpdb->prefix . 'wc_order_product_lookup',
    'customer_lookup' => $wpdb->prefix . 'wc_customer_lookup'
];

return [

    'tables' => $tables,

    // ----- SQL SELECTS ------
    // ------------------------

    'select_user' =>
    "SELECT * FROM {$tables['users']}",

    'select_usermeta' =>
    "SELECT * FROM {$tables['usermeta']} WHERE user_id = ?",

    'select_user_where_cpf' =>
    "SELECT * FROM {$tables['users']} WHERE user_login = ?",

    'select_user_where_email' =>
    "SELECT * FROM {$tables['users']} WHERE user_email = ?",

    'select_user_where_id' =>
    "SELECT * FROM {$tables['users']} WHERE ID = ? LIMIT 1",

    'select_log' =>
    "SELECT t1.*, t2.user_email FROM {$tables['log']} AS t1 LEFT JOIN {$tables['users']} AS t2
            ON t1.co_user = t2.ID WHERE t1.dt_deleted IS NULL;",

    'select_message' =>
    "SELECT * FROM {$tables['message']}",

    'select_score' =>
    "SELECT * FROM {$tables['score']}",

    'select_form_where_form' =>
    "SELECT * FROM {$tables['form']} WHERE co_form = ?",

    // AQUI
    'select_answer_where_user' =>
    "SELECT t3.co_forminator, t2.*, t1.dt_created 
        FROM {$tables['quiz']} AS t1 
        JOIN {$tables['answer']} AS t2 
        ON t1.co_quiz = t2.co_quiz
        JOIN {$tables['question']} AS t3
        ON t2.co_form = t3.co_form AND t2.co_question = t3.co_question 
        WHERE t1.co_quiz IN ( 
            SELECT MAX(s2.co_quiz) AS co_quiz 
            FROM {$tables['quiz']} AS s1 
            JOIN {$tables['answer']} AS s2 
            ON s1.co_quiz = s2.co_quiz WHERE s1.co_user = ? 
            GROUP BY s2.co_form 
        ) ORDER BY t2.co_form;",

    'select_forminator_codes' =>
    "SELECT co_question, co_forminator FROM {$tables['question']} WHERE co_form = ?",

    'select_question_type' =>
    "SELECT * FROM {$tables['question_type']}",

    'select_question_where_form' =>
    "SELECT t1.*, t2.js_option FROM {$tables['question']} AS t1 LEFT JOIN {$tables['option']} AS t2
            ON t1.co_option = t2.co_option WHERE t1.co_form = ? ORDER BY t1.co_question",

    'select_option_where' =>
        "SELECT * FROM {$tables['option']} WHERE co_option = ?",

    'select_answer_where_quiz' =>
    "SELECT * FROM {$tables['answer']} WHERE co_quiz = ?",

    'select_score_where_quiz' =>
    "SELECT * FROM {$tables['score']} WHERE co_quiz = ?",

    'select_form_where_user' =>
    "SELECT t1.co_user, t2.co_form, t2.co_quiz, t1.dt_created
        FROM {$tables['quiz']} AS t1 JOIN {$tables['answer']} AS t2
        ON t1.co_quiz = t2.co_quiz
        WHERE t1.co_user = ?
        GROUP BY t1.co_user, t2.co_form, t2.co_quiz
        ORDER BY t2.co_quiz DESC",

    /*
    SELECT MAX(t2.co_quiz) AS co_quiz, t2.co_form 
    FROM wp_plugin_quiz AS t1 
    JOIN wp_plugin_answer AS t2 
    ON t1.co_quiz = t2.co_quiz 
    WHERE t1.co_user = 62 
    GROUP BY t2.co_form
    */

    'select_form_where_user_form' =>
    "SELECT t1.co_user, t2.co_form, t2.co_quiz
            FROM {$tables['quiz']} AS t1 JOIN {$tables['answer']} AS t2
            ON t1.co_quiz = t2.co_quiz
            WHERE t1.co_user = ? AND t2.co_form = ?
            ORDER BY t1.dt_created DESC
            LIMIT 1",

    'select_cnpj' =>
    "SELECT t2.* FROM wp_plugin_quiz AS t1 JOIN wp_plugin_answer AS t2
            ON t1.co_quiz = t2.co_quiz
            WHERE t1.co_user = ? AND t2.co_form = ? AND t2.co_question = ?;",

    'has_order' =>
        "SELECT COUNT(0) AS has_order 
            FROM {$tables['customer_lookup']} AS t1 
            JOIN {$tables['order_product_lookup']} AS t2 
            ON t1.customer_id = t2.customer_id 
            WHERE t1.user_id = ? AND t2.product_id = ?;",

    // ----- SQL INSERTS ------
    // ------------------------

    'insert_log' =>
    "INSERT INTO {$tables['log']} (
            co_message,
            co_quiz,
            co_user,
            vl_ipv4_ipv6,
            js_settings
        ) VALUES ( ?, ?, ?, ?, ? )",

    'insert_quiz' =>
    "INSERT INTO {$tables['quiz']} (
            co_quiz,
            co_user,
            vl_ipv4_ipv6
        ) VALUES ( NULL, ?, HEX(INET6_ATON( ? )));",

    'insert_answer' =>
    "INSERT INTO {$tables['answer']} (
            co_answer,
            co_quiz,
            co_form,
            co_question,
            js_answer
        ) VALUES ( NULL, ?, ?, ?, ? );",

    // ----- SQL UPDATES ------
    // ------------------------
    'update_quiz' =>
    "UPDATE {$tables['quiz']}
        SET dt_updated = now()
        WHERE co_quiz = ?;",

    'update_answer' =>
    "UPDATE {$tables['answer']}
        SET js_answer = ?
        WHERE co_quiz = ? AND co_question = ?;",

];
