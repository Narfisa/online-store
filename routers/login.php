<?php
    require_once 'W:\domains\localhost\funcs.php';

    function route($method, $urlData, $formData) {
        // connect to database in:
        require_once 'W:\domains\localhost\db.php';
        // POST /login
        if ($method === 'POST' && count($urlData) === 0){
            $str = file_get_contents('php://input');
            $data = json_decode($str, true);
            
            echo json_encode(login($link, $data['login'], $data['password']));
            return;
        }
    }
?>