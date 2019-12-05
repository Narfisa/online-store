<?php
    require_once 'W:\domains\localhost\funcs.php';

    function route($method, $urlData, $formData) {
        // connect to database in:
        require_once 'W:\domains\localhost\db.php';
        
        if ($method === 'POST') {
            $str = file_get_contents('php://input');
            $data = json_decode($str,true);

            // GET /orders/my/orders
            if (count($urlData) == 2)
              echo json_encode(getMyOrders($link, $data["id_User"]));
            else
            // GET /orders
            if (empty($urlData)){              
                echo json_encode(getOrders($link, $data["id_User"]));
            }
            // GET /orders/add
            else{
                echo json_encode(addItemToOrder($link, $data['id_Item'], $data['id_User']));
            }
        }        
        else
        // PUT /orders/change
        if ($method === 'PUT' && count($urlData) === 1){
            $str = file_get_contents('php://input');
            $data = json_decode($str, true);

            echo json_encode(changeCount($link, $data['id_Item'], $data['id_User'], $data['count']));
        }
        else
        // PUT /orders
        if ($method === 'PUT'){
            $str = file_get_contents('php://input');
            $data = json_decode($str, true);

            echo json_encode(buy($link, $data['id_User']));
        }
        else
        {
            echo json_encode(array('error'=> 'the query is inc'));
        }
    }
?>