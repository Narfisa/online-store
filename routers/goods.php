<?php
    require_once 'W:\domains\localhost\funcs.php';

    function route($method, $urlData, $formData) {
        // connect to database in:
        require_once 'W:\domains\localhost\db.php';

        if ($method === 'GET') {
            // GET /goods
            if (count($urlData) === 0){
                echo json_encode(getAllItems($link));
            }
            // GET /goods/{id}
            elseif (count($urlData) === 1){            
                echo json_encode(getItem($link, $urlData[0]));
            }
            // GET /goods/category/{id}
            elseif (count($urlData) === 2){
                echo json_encode(getItemsFromCategory($link, $urlData[1]));
            }
        }
        else
        if ($method === 'POST' && empty($urlData)){
            $str = file_get_contents('php://input');
            $data = json_decode($str,true);

            echo json_encode(insertItem($link, $data["title"], $data["price"], $data["id_category"], $data['image']));
        }
        else
        {
            // Возвращаем ошибку
            header('HTTP/1.0 400 Bad Request');
            echo json_encode(array(
                'error' => 'Bad Request'
            ));
        }
    }
?>