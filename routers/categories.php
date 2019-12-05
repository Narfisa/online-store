<?php
    require_once 'W:\domains\localhost\funcs.php';

    function route($method, $urlData, $formData) {

        // connect to database in:
        require_once 'W:\domains\localhost\db.php';

        if ($method === 'GET') {
            // GET categories
            if (count($urlData) === 0){
                echo json_encode(getAllCategories($link));
                return;
            }
        }
        if ($method === 'POST' && empty($urlData)) {
            $str = file_get_contents('php://input');
            $data = json_decode($str,true);

            echo json_encode(insertCategory($link, $data["title"], $data["comment"]));
            return;
        }
    
        // Возвращаем ошибку
        header('HTTP/1.0 400 Bad Request');
        echo json_encode(array(
            'error' => 'Bad Request'
        ));
    }
?>