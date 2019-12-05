<?php
    //
    // QUERIES TO ITEMS
    //
    // Get items + categories
    function getAllItems($link) {
        $sql = "SELECT items.id, items.name, categories.title, items.price, items.image FROM `items`
        JOIN `categories` ON items.id_category = categories.id";
        $result = mysqli_query($link, $sql);
        $items = mysqli_fetch_all($result, MYSQLI_ASSOC);

        return $items;
    }
    // Get items + categories
    function getItem($link, $id) {
        $sql = "SELECT * FROM `items` WHERE items.id = '$id'";
        $result = mysqli_query($link, $sql);
        $items = mysqli_fetch_all($result, MYSQLI_ASSOC);

        return $items;
    }
    // New item
    function insertItem($link, $title, $price, $id_category, $url){
        // Insert a new item to table
        $sql = "INSERT INTO `items`(id,title,price,id_category,image) VALUES(NOW(), '$title', '$price', '$id_category', '$url')";
        $result = mysqli_query($link, $sql);
        return;
    }
    // Get items from category
    function getItemsFromCategory($link, $title){
        $sql = "SELECT * FROM `items` JOIN `categories`
        ON items.id_category = categories.id
        WHERE categories.title = '$title'";
        $result = mysqli_query($link, $sql);
        $arr = mysqli_fetch_all($result, MYSQLI_ASSOC);
        
        return $arr;
    }
////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////
    //
    //  QUERIES TO CATEGORIES
    //
    // Get categories
    function getAllCategories($link) {
        $sql = "SELECT * FROM `categories`";
        $result = mysqli_query($link, $sql);
        $categories = mysqli_fetch_all($result, MYSQLI_ASSOC);

        return $categories;
    }
    // New category
    function insertCategory($link, $title, $comment){
        $sql = "INSERT INTO `categories` VALUES(NOW(), '$title' , '$comment')";
        $result = mysqli_query($link, $sql);
        $categories = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////
    //
    //  QUERIES TO ORDERS
    //
    // get orders 
    function getOrders($link, $id_user){
        $sql = "SELECT items.id, items.name, items.image, items.price, categories.title, itemtoorder.count FROM `items` JOIN `itemtoorder`
        ON items.id = itemtoorder.id_Item JOIN `orders`
        ON itemtoorder.id_Order = orders.id JOIN `user`
        ON orders.id_User = user.id JOIN `categories`
        ON items.id_category = categories.id
        WHERE user.id = '$id_user' AND orders.bought = 0";
        $result = mysqli_query($link, $sql);
        $order = mysqli_fetch_all($result,MYSQLI_ASSOC);
        return $order;
    }

    // GET MY orders
    function getMyOrders($link, $id_user){
        $sql = "SELECT orders.id as orderId, items.id, items.name, items.image, items.price, categories.title, itemtoorder.count FROM `items` JOIN `itemtoorder`
        ON items.id = itemtoorder.id_Item JOIN `orders`
        ON itemtoorder.id_Order = orders.id JOIN `user`
        ON orders.id_User = user.id JOIN `categories`
        ON items.id_category = categories.id
        WHERE user.id = '$id_user' AND orders.bought = 1";
        $result = mysqli_query($link, $sql);
        $order = mysqli_fetch_all($result,MYSQLI_ASSOC);
        return $order;
    }
    // adding an item to order or creating new order
    function addItemToOrder($link, $id_item, $id_user){
        $sql = "SELECT * FROM `user` WHERE id = '$id_user'";
        $result = mysqli_query($link, $sql);
        $user = mysqli_fetch_all($result, MYSQLI_ASSOC);

        // if user exists
        if (!empty($user)){
            $sql = "SELECT id FROM `orders` WHERE id_User = '$id_user' AND bought = 0";
            $result = mysqli_query($link, $sql);
            $orders = mysqli_fetch_all($result, MYSQLI_ASSOC);
            $orders = $orders[0];
            $orders = $orders['id'];

            // if order doesn't exist
            if (empty($orders)){ 
                $orders = time();
                $sql = "INSERT INTO `orders` VALUES('$orders', '$id_user', NOW(), 0)";
                $result = mysqli_query($link, $sql);   
            }
            // if item does not exist in the order
            $sql = "SELECT * FROM `itemtoorder` JOIN `orders`
            ON itemtoorder.id_Order = orders.id 
            WHERE orders.id = '$orders' AND itemtoorder.id_Item = '$id_item' AND orders.bought = 0";
            $result = mysqli_query($link, $sql);
            $item = mysqli_fetch_all($result, MYSQLI_ASSOC);

            // put an item to itemToOrder
            if (empty($item)){
                $sql = "INSERT INTO `itemtoorder`(id_item , id_Order, count) VALUES('$id_item' , '$orders', 1)";
                $result = mysqli_query($link, $sql);
            }
        }
        else{
            http_response_code(400);
            echo json_encode(array('error'=> 'user with the id does not exist'));
        }            
    }
    // deleting item from order
    function deleteItemFromOrder($link, $id_item, $id_user){
        $sql = "DELETE itemtoorder FROM `itemtoorder` JOIN `orders` 
        ON itemtoorder.id_Order = orders.id 
        WHERE itemtoorder.id_item = '$id_item' AND orders.id_User = '$id_user'";
        $result = mysqli_query($link, $sql);
    }
    // buy
    function buy($link, $id_user) {
        $sql = "UPDATE `orders` SET bought = 1 
        WHERE id_User = '$id_user' AND bought = 0";
        $result = mysqli_query($link, $sql);
    }
    // changing count of the item in an order
    function changeCount($link, $id_item, $id_user, $count){
        if ($count == 0) deleteItemFromOrder($link, $id_item, $id_user);
        else{
            $sql = "UPDATE `itemtoorder` JOIN `orders`
            ON itemtoorder.id_Order = orders.id
            SET count = '$count'
            WHERE itemtoorder.id_Item = '$id_item'
            AND orders.id_User = '$id_user' AND orders.bought = 0";
            $result = mysqli_query($link, $sql);
        }
    }
////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////
    //
    //  QUERIES TO USERS
    //
    // if user with the login exists
    function loginExists($link, $login){
        $sql = "SELECT * FROM `user` WHERE login = '$login'";
        $result = mysqli_query($link, $sql);
        $user = mysqli_fetch_all($result, MYSQLI_ASSOC);
        return !empty($user);
    }
    // checking login and password
    function login($link, $login, $password){
        $sql = "SELECT id FROM `user` WHERE login = '$login' AND password = '$password'";
        $result = mysqli_query($link, $sql);
        $user = mysqli_fetch_all($result, MYSQLI_ASSOC);        
        $user = $user[0];
        $user = $user['id'];

        if (empty($user)){
            http_response_code(400);
            return array('error'=> 'wrong login or password' );
        }
        else
            return array('id_User' => $user);
    }
    // registation
    function reg($link, $login, $password){
        if (!loginExists($link, $login) && !empty($login) && !empty($password)){
            $now = time();
            $sql = "INSERT INTO `user` VALUES('$now', '$login', '$password')";
            $result = mysqli_query($link, $sql);
            return (array('id_User' => $now));
        }
        else {
            http_response_code(400);
            return(array('error'=> 'the login already exists'));
        }
    }
?>