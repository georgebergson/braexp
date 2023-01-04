<?php

class PgModel
{

    private $mysqli;
    private $queries;
    private $tables;

    public function __construct()
    {
        try {
            $this->mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
            $this->queries = require dirname(__DIR__) . '/config/queries.php';
            $this->tables = $this->queries['tables'];
        } catch (Exception $e) {
            echo 'Exceção capturada: ', $e->getMessage(), "\n";
        }
    }

    public function create_database()
    {
        if (!isset($this->mysqli) || $this->mysqli->connect_error) {
            return null;
        }

        $result = $this->mysqli->query("SHOW TABLES LIKE 'wp_plugin%'");

        if ($result->num_rows < 9) {
            $boot = file_get_contents(dirname(__DIR__) . '/config/db-mysql-v5.7.sql');
            $this->mysqli->multi_query($boot);
        }
    }

    public function begin_transaction()
    {
        $this->mysqli->begin_transaction();
    }

    public function rollback()
    {
        $this->mysqli->rollback();
    }

    public function commit()
    {
        $this->mysqli->commit();
    }

    private function get_results($result)
    {
        $rows = array();
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        return isset($rows) ? $rows : null;
    }

    public function get_users()
    {
        if (!isset($this->mysqli) || $this->mysqli->connect_error) {
            return null;
        }

        return SELF::get_results($this->mysqli->query($this->queries['select_user']));
    }

    public function get_env()
    {
        foreach (ENV as $key => $value) {
            if (strpos($_SERVER['HTTP_HOST'], $key) !== false) {
                return $value;
            }
        }

        return ENV['default'];
    }

    public function get_usermeta($user_id)
    {
        if (!isset($this->mysqli) || $this->mysqli->connect_error) {
            return null;
        }

        $stmt = $this->mysqli->prepare($this->queries['select_usermeta']);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return SELF::get_results($stmt->get_result());
    }

    public function get_answer($user_id)
    {
        if (!isset($this->mysqli) || $this->mysqli->connect_error) {
            return null;
        }

        $stmt = $this->mysqli->prepare($this->queries['select_answer_where_user']);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return SELF::get_results($stmt->get_result());
    }

    public function get_question_type()
    {
        if (!isset($this->mysqli) || $this->mysqli->connect_error) {
            return null;
        }

        return SELF::get_results($this->mysqli->query($this->queries['select_question_type']));
    }

    public function get_user_where_cpf($value)
    {
        if (!isset($this->mysqli) || $this->mysqli->connect_error) {
            return null;
        }

        $stmt = $this->mysqli->prepare($this->queries['select_user_where_cpf']);
        $stmt->bind_param("s", $value);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function get_user_where_email($value)
    {
        if (!isset($this->mysqli) || $this->mysqli->connect_error) {
            return null;
        }

        $stmt = $this->mysqli->prepare($this->queries['select_user_where_email']);
        $stmt->bind_param("s", $value);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function get_user_where_id($user_id)
    {
        if (!isset($this->mysqli) || $this->mysqli->connect_error) {
            return null;
        }

        $stmt = $this->mysqli->prepare($this->queries['select_user_where_id']);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return SELF::get_results($stmt->get_result());
    }

    public function get_form_where_form($value)
    {
        if (!isset($this->mysqli) || $this->mysqli->connect_error) {
            return null;
        }

        $stmt = $this->mysqli->prepare($this->queries['select_form_where_form']);
        $stmt->bind_param("i", $value);
        $stmt->execute();
        return SELF::get_results($stmt->get_result());
    }

    public function get_question_where_form($value)
    {
        if (!isset($this->mysqli) || $this->mysqli->connect_error) {
            return null;
        }

        $stmt = $this->mysqli->prepare($this->queries['select_question_where_form']);
        $stmt->bind_param("i", $value);
        $stmt->execute();
        return SELF::get_results($stmt->get_result());
    }

    public function get_forminator_codes($value)
    {
        if (!isset($this->mysqli) || $this->mysqli->connect_error) {
            return null;
        }

        $stmt = $this->mysqli->prepare($this->queries['select_forminator_codes']);
        $stmt->bind_param("i", $value);
        $stmt->execute();
        return SELF::get_results($stmt->get_result());
    }

    public function get_answer_where_quiz($value)
    {
        if (!isset($this->mysqli) || $this->mysqli->connect_error) {
            return null;
        }

        $stmt = $this->mysqli->prepare($this->queries['select_answer_where_quiz']);

        if (!$stmt->bind_param("i", $value)) {
            return "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
        }

        if (!$stmt->execute()) {
            return "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        }

        return SELF::get_results($stmt->get_result());
    }

    public function get_form_where_user( $co_user )
    {
        if (!isset($this->mysqli) || $this->mysqli->connect_error) {
            return null;
        }

        $stmt = $this->mysqli->prepare($this->queries['select_form_where_user']);

        //$stmt->bind_param("i", $co_user);
        //$stmt->execute();

        if (!$stmt->bind_param("i", $co_user)) {
            return "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
        }

        if (!$stmt->execute()) {
            return "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        }

        return SELF::get_results($stmt->get_result());
    }

    public function get_option_where( $co_option )
    {
        if (!isset($this->mysqli) || $this->mysqli->connect_error) {
            return null;
        }

        $stmt = $this->mysqli->prepare($this->queries['select_option_where']);

        //$stmt->bind_param("i", $co_user);
        //$stmt->execute();

        if (!$stmt->bind_param("i", $co_option)) {
            return "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
        }

        if (!$stmt->execute()) {
            return "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        }

        return SELF::get_results($stmt->get_result());
    }

    public function get_form_where_user_form( $values )
    {
        if (!isset($this->mysqli) || $this->mysqli->connect_error) {
            return null;
        }

        $stmt = $this->mysqli->prepare($this->queries['select_form_where_user_form']);

        if (!$stmt->bind_param("ii", ...$values)) {
            return "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
        }

        if (!$stmt->execute()) {
            return "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        }

        return SELF::get_results($stmt->get_result());
    }

    public function get_user_cnpj( $values )
    {
        if (!isset($this->mysqli) || $this->mysqli->connect_error) {
            return null;
        }

        $stmt = $this->mysqli->prepare($this->queries['select_cnpj']);
        $stmt->bind_param("iii", ...$values);
        $stmt->execute();
        return SELF::get_results($stmt->get_result());
    }

    public function get_log()
    {
        if (!isset($this->mysqli) || $this->mysqli->connect_error) {
            return null;
        }

        return $this->mysqli->query($this->queries['select_log']);
    }

    public function get_message()
    {
        if (!isset($this->mysqli) || $this->mysqli->connect_error) {
            return null;
        }

        return $this->mysqli->query($this->queries['select_message']);
    }

    public function set_log($values)
    {
        if (!isset($this->mysqli) || $this->mysqli->connect_error) {
            return null;
        }

        $stmt = $this->mysqli->prepare($this->queries['insert_log']);
        $stmt->bind_param("sssss", $values);
        $stmt->execute();
        return SELF::get_results($stmt->get_result());
    }

    public function has_order( $user_id, $product_id )
    {
        if (!isset($this->mysqli) || $this->mysqli->connect_error) {
            return null;
        }

        $stmt = $this->mysqli->prepare($this->queries['has_order']);

        $values = [(int)$user_id, (int)$product_id];
        if (!$stmt->bind_param("ii", ...$values)) {
            return "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
        }

        if (!$stmt->execute()) {
            return "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        }

        $result = $stmt->get_result();
        $row = $result->fetch_row();
        return $row[0];
    }


    // ================================================================
    // Setters

    public function set_quiz($values, $update = false)
    {
        if (!isset($this->mysqli) || $this->mysqli->connect_error) {
            return null;
        }
        
        if($update == true) {
            $stmt = $this->mysqli->prepare($this->queries['update_quiz']);

            if (!$stmt->bind_param("i", ...$values)) {
                return "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
            }
            if (!$stmt->execute()) {
                return "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
            }

            return $stmt->affected_rows;
        }
        else {
            $stmt = $this->mysqli->prepare($this->queries['insert_quiz']);

            if (!$stmt->bind_param("is", ...$values)) {
                return "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
            }
            if (!$stmt->execute()) {
                return "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
            }
            return $stmt->insert_id;
        }
    }

    public function set_answer($values, $update = false)
    {
        if (!isset($this->mysqli) || $this->mysqli->connect_error) {
            return null;
        }

        if($update == true) {
            $stmt = $this->mysqli->prepare($this->queries['update_answer']);

            if (!$stmt->bind_param("sss", ...$values)) {
                return "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
            }

            if (!$stmt->execute()) {
                return "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
            }

            return $stmt->affected_rows;
        }
        else {
            $stmt = $this->mysqli->prepare($this->queries['insert_answer']);

            if (!$stmt->bind_param("iiis", ...$values)) {
                return "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
            }

            if (!$stmt->execute()) {
                return "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
            }

            return $stmt->insert_id;
        }
    }
}


/*
SELECT t1.co_user, t2.co_form 
FROM wp_plugin_quiz AS t1 JOIN wp_plugin_answer AS t2 
ON t1.co_quiz = t2.co_quiz
WHERE t1.co_user > 0
GROUP BY t1.co_user, t2.co_form
*/